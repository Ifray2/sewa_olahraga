<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\Request;

class AdminRentalController extends Controller
{
    public function index(Request $request)
    {
        $rentals = Rental::with(['user', 'equipment.category', 'payment'])
            ->when($request->search, fn($q) =>
                $q->where('rental_code', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"))
            )
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->equipment_id, fn($q) => $q->where('equipment_id', $request->equipment_id))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Summary counts
        $summary = [
            'all'       => Rental::count(),
            'pending'   => Rental::where('status', 'pending')->count(),
            'confirmed' => Rental::where('status', 'confirmed')->count(),
            'active'    => Rental::where('status', 'active')->count(),
            'returned'  => Rental::where('status', 'returned')->count(),
            'cancelled' => Rental::where('status', 'cancelled')->count(),
        ];

        return view('admin.rentals.index', compact('rentals', 'summary'));
    }

    public function create()
    {
        $equipment = Equipment::available()->with('category')->orderBy('name')->get();
        $users     = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.rentals.create', compact('equipment', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'      => 'required|exists:users,id',
            'equipment_id' => 'required|exists:equipment,id',
            'quantity'     => 'required|integer|min:1',
            'start_date'   => 'required|date|after_or_equal:today',
            'end_date'     => 'required|date|after:start_date',
            'notes'        => 'nullable|string|max:500',
        ]);

        $equip        = Equipment::findOrFail($validated['equipment_id']);
        $start        = \Carbon\Carbon::parse($validated['start_date']);
        $end          = \Carbon\Carbon::parse($validated['end_date']);
        $durationDays = $start->diffInDays($end);
        $totalPrice   = $equip->price_per_day * $durationDays * $validated['quantity'];

        if ($equip->stock < $validated['quantity']) {
            return back()->withInput()
                ->with('error', "Stok tidak mencukupi. Tersedia: {$equip->stock} unit.");
        }

        Rental::create([
            ...$validated,
            'duration_days' => $durationDays,
            'total_price'   => $totalPrice,
            'status'        => 'pending',
            'handled_by'    => auth()->id(),
        ]);

        return redirect()->route('admin.rentals.index')
            ->with('success', 'Transaksi sewa berhasil dibuat.');
    }

    public function show(Rental $rental)
    {
        $rental->load([
            'user', 'equipment.category',
            'handler', 'payment', 'review.user',
        ]);

        return view('admin.rentals.show', compact('rental'));
    }

    public function edit(Rental $rental)
    {
        $equipment = Equipment::with('category')->orderBy('name')->get();
        $users     = User::where('role', 'user')->orderBy('name')->get();
        $staff     = User::whereIn('role', ['admin', 'petugas'])->orderBy('name')->get();

        return view('admin.rentals.edit', compact('rental', 'equipment', 'users', 'staff'));
    }

    public function update(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'quantity'    => 'required|integer|min:1',
            'status'      => 'required|in:pending,confirmed,active,returned,cancelled',
            'notes'       => 'nullable|string|max:500',
            'handled_by'  => 'nullable|exists:users,id',
        ]);

        $start        = \Carbon\Carbon::parse($validated['start_date']);
        $end          = \Carbon\Carbon::parse($validated['end_date']);
        $durationDays = $start->diffInDays($end);
        $totalPrice   = $rental->equipment->price_per_day * $durationDays * $validated['quantity'];

        // Timestamps otomatis berdasarkan status
        $extra = ['duration_days' => $durationDays, 'total_price' => $totalPrice];
        if ($validated['status'] === 'confirmed' && ! $rental->confirmed_at) {
            $extra['confirmed_at'] = now();
        }
        if ($validated['status'] === 'returned' && ! $rental->returned_at) {
            $extra['returned_at'] = now();
        }

        $rental->update([...$validated, ...$extra]);

        return redirect()->route('admin.rentals.show', $rental)
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Rental $rental)
    {
        if (in_array($rental->status, ['active', 'confirmed'])) {
            return back()->with('error', 'Transaksi yang sedang aktif/dikonfirmasi tidak dapat dihapus.');
        }
        $code = $rental->rental_code;
        $rental->delete();

        return redirect()->route('admin.rentals.index')
            ->with('success', "Transaksi {$code} berhasil dihapus.");
    }

    // ── Quick status actions ─────────────────────────────────────────────────
    public function confirm(Rental $rental)
    {
        if ($rental->status !== 'pending') {
            return back()->with('error', 'Hanya transaksi pending yang dapat dikonfirmasi.');
        }
        $rental->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
            'handled_by'   => auth()->id(),
        ]);

        return back()->with('success', "Transaksi {$rental->rental_code} berhasil dikonfirmasi.");
    }

    public function activate(Rental $rental)
    {
        if ($rental->status !== 'confirmed') {
            return back()->with('error', 'Hanya transaksi confirmed yang dapat diaktifkan.');
        }
        $rental->update(['status' => 'active', 'handled_by' => auth()->id()]);

        return back()->with('success', "Transaksi {$rental->rental_code} sekarang aktif.");
    }

    public function markReturned(Rental $rental)
    {
        if ($rental->status !== 'active') {
            return back()->with('error', 'Hanya transaksi aktif yang dapat dikembalikan.');
        }
        $rental->update([
            'status'      => 'returned',
            'returned_at' => now(),
            'handled_by'  => auth()->id(),
        ]);

        return back()->with('success', "Alat pada transaksi {$rental->rental_code} telah dikembalikan.");
    }

    public function cancel(Rental $rental)
    {
        if (in_array($rental->status, ['returned', 'cancelled'])) {
            return back()->with('error', 'Transaksi ini sudah selesai/dibatalkan.');
        }
        $rental->update(['status' => 'cancelled', 'handled_by' => auth()->id()]);

        return back()->with('success', "Transaksi {$rental->rental_code} telah dibatalkan.");
    }
}
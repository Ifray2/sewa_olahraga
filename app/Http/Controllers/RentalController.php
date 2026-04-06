<?php

namespace App\Http\Controllers;



use App\Models\Equipment;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class RentalController extends Controller

{
   
    // ── Daftar semua rental milik user yang login ────────────────────────────
    public function index(Request $request)
    {
        $rentals = Rental::with(['equipment.category', 'payment'])
            ->where('user_id', Auth::id())
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'all'       => Rental::where('user_id', Auth::id())->count(),
            'pending'   => Rental::where('user_id', Auth::id())->where('status', 'pending')->count(),
            'active'    => Rental::where('user_id', Auth::id())->where('status', 'active')->count(),
            'returned'  => Rental::where('user_id', Auth::id())->where('status', 'returned')->count(),
            'cancelled' => Rental::where('user_id', Auth::id())->where('status', 'cancelled')->count(),
        ];

        return view('rentals.index', compact('rentals', 'summary'));
    }

    // ── Form buat transaksi baru ─────────────────────────────────────────────
    public function create(Request $request)
    {
        $equipment = null;

        if ($request->equipment_id) {
            $equipment = Equipment::available()->with('category')->find($request->equipment_id);
            if (! $equipment) {
                return redirect()->route('catalog')
                    ->with('error', 'Alat tidak tersedia atau tidak ditemukan.');
            }
        }

        // Pre-fill from URL params (from detail page booking card)
        $defaults = [
            'start_date' => $request->start_date ?? date('Y-m-d', strtotime('+1 day')),
            'end_date'   => $request->end_date   ?? date('Y-m-d', strtotime('+3 days')),
            'quantity'   => $request->quantity   ?? 1,
        ];

        return view('rentals.create', compact('equipment', 'defaults'));
    }

    // ── Simpan transaksi baru ────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'quantity'     => 'required|integer|min:1',
            'start_date'   => 'required|date|after_or_equal:today',
            'end_date'     => 'required|date|after:start_date',
            'notes'        => 'nullable|string|max:500',
        ]);

        $equipment = Equipment::available()->find($validated['equipment_id']);

        if (! $equipment) {
            return back()->withInput()->with('error', 'Alat tidak tersedia saat ini.');
        }

        if ($equipment->stock < $validated['quantity']) {
            return back()->withInput()
                ->with('error', "Stok tidak mencukupi. Tersedia: {$equipment->stock} unit.");
        }

        $start        = \Carbon\Carbon::parse($validated['start_date']);
        $end          = \Carbon\Carbon::parse($validated['end_date']);
        $durationDays = $start->diffInDays($end);
        $totalPrice   = $equipment->price_per_day * $durationDays * $validated['quantity'];

        $rental = Rental::create([
            ...$validated,
            'user_id'       => Auth::id(),
            'duration_days' => $durationDays,
            'total_price'   => $totalPrice,
            'status'        => 'pending',
        ]);

        return redirect()->route('rentals.show', $rental)
            ->with('success', 'Transaksi berhasil dibuat! Silakan lakukan pembayaran.');
    }

    // ── Detail transaksi ─────────────────────────────────────────────────────
    public function show(Rental $rental)
    {
        // Only the owner can see their rental
        abort_if($rental->user_id !== Auth::id(), 403);

        $rental->load(['equipment.category', 'payment', 'review', 'handler']);

        $canReview = $rental->status === 'returned'
            && ! $rental->review
            && $rental->user_id === Auth::id();

        return view('rentals.show', compact('rental', 'canReview'));
    }

    // ── Batalkan transaksi ───────────────────────────────────────────────────
    public function cancel(Rental $rental)
    {
        abort_if($rental->user_id !== Auth::id(), 403);

        if (! in_array($rental->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Transaksi ini tidak dapat dibatalkan.');
        }

        $rental->update(['status' => 'cancelled']);

        return back()->with('success', 'Transaksi berhasil dibatalkan.');
    }

    // ── Upload bukti pembayaran ──────────────────────────────────────────────
    public function pay(Request $request, Rental $rental)
    {
        abort_if($rental->user_id !== Auth::id(), 403);

        if ($rental->status === 'cancelled') {
            return back()->with('error', 'Transaksi sudah dibatalkan.');
        }

        if ($rental->payment && $rental->payment->status === 'paid') {
            return back()->with('error', 'Pembayaran sudah dilakukan.');
        }

        $validated = $request->validate([
            'method'      => 'required|in:transfer,cash,ewallet',
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')
                ->store('payments/proof', 'public');
        }

        Payment::updateOrCreate(
            ['rental_id' => $rental->id],
            [
                'amount'      => $rental->total_price,
                'method'      => $validated['method'],
                'status'      => 'unpaid',
                'proof_image' => $proofPath,
            ]
        );

        return back()->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi admin.');
    }

    // ── Submit ulasan ────────────────────────────────────────────────────────
    public function review(Request $request, Rental $rental)
    {
        abort_if($rental->user_id !== Auth::id(), 403);

        if ($rental->status !== 'returned') {
            return back()->with('error', 'Ulasan hanya dapat diberikan setelah alat dikembalikan.');
        }

        if ($rental->review) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk transaksi ini.');
        }

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            ...$validated,
            'rental_id'    => $rental->id,
            'user_id'      => Auth::id(),
            'equipment_id' => $rental->equipment_id,
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }
}
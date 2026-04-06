<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetugasPaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['rental.user', 'rental.equipment'])
            ->when($request->search, fn($q) =>
                $q->whereHas('rental', fn($r) =>
                    $r->where('rental_code', 'like', "%{$request->search}%")
                      ->orWhereHas('user', fn($u) =>
                          $u->where('name', 'like', "%{$request->search}%")
                      )
                )
            )
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->method, fn($q) => $q->where('method', $request->method))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total_all'      => Payment::sum('amount'),
            'total_paid'     => Payment::paid()->sum('amount'),
            'count_unpaid'   => Payment::where('status', 'unpaid')->count(),
            'count_refunded' => Payment::where('status', 'refunded')->count(),
        ];

        return view('petugas.payments.index', compact('payments', 'summary'));
    }

    public function create(Request $request)
    {
        // Only rentals that don't have payment yet
        $rentals = Rental::with(['user', 'equipment'])
            ->whereDoesntHave('payment')
            ->whereNotIn('status', ['cancelled'])
            ->orderByDesc('created_at')
            ->get();

        $selectedRental = $request->rental_id
            ? Rental::with(['user', 'equipment'])->find($request->rental_id)
            : null;

        return view('petugas.payments.create', compact('rentals', 'selectedRental'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rental_id'   => 'required|exists:rentals,id|unique:payments,rental_id',
            'amount'      => 'required|numeric|min:0',
            'method'      => 'required|in:transfer,cash,ewallet',
            'status'      => 'required|in:unpaid,paid,refunded',
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'paid_at'     => 'nullable|date',
        ]);

        if ($request->hasFile('proof_image')) {
            $validated['proof_image'] = $request->file('proof_image')
                ->store('payments/proof', 'public');
        }

        if ($validated['status'] === 'paid' && empty($validated['paid_at'])) {
            $validated['paid_at'] = now();
        }

        Payment::create($validated);

        return redirect()->route('petugas.payments.index')
            ->with('success', 'Data pembayaran berhasil ditambahkan.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['rental.user', 'rental.equipment.category', 'rental.handler']);

        return view('petugas.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $payment->load(['rental.user', 'rental.equipment']);

        return view('petugas.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:0',
            'method'      => 'required|in:transfer,cash,ewallet',
            'status'      => 'required|in:unpaid,paid,refunded',
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'paid_at'     => 'nullable|date',
        ]);

        if ($request->hasFile('proof_image')) {
            if ($payment->proof_image) {
                Storage::disk('public')->delete($payment->proof_image);
            }
            $validated['proof_image'] = $request->file('proof_image')
                ->store('payments/proof', 'public');
        }

        if ($request->boolean('remove_proof') && $payment->proof_image) {
            Storage::disk('public')->delete($payment->proof_image);
            $validated['proof_image'] = null;
        }

        // Auto-set paid_at when marking as paid
        if ($validated['status'] === 'paid' && ! $payment->paid_at && empty($validated['paid_at'])) {
            $validated['paid_at'] = now();
        }
        // Clear paid_at if back to unpaid
        if ($validated['status'] === 'unpaid') {
            $validated['paid_at'] = null;
        }

        $payment->update($validated);

        return redirect()->route('petugas.payments.show', $payment)
            ->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->status === 'paid') {
            return back()->with('error', 'Pembayaran yang sudah lunas tidak dapat dihapus.');
        }

        if ($payment->proof_image) {
            Storage::disk('public')->delete($payment->proof_image);
        }

        $payment->delete();

        return redirect()->route('petugas.payments.index')
            ->with('success', 'Data pembayaran berhasil dihapus.');
    }

    // ── Quick verify (mark as paid) ─────────────────────────────────────────
    public function verify(Payment $payment)
    {
        if ($payment->status !== 'unpaid') {
            return back()->with('error', 'Hanya pembayaran dengan status belum bayar yang dapat diverifikasi.');
        }

        $payment->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', "Pembayaran untuk {$payment->rental->rental_code} telah diverifikasi.");
    }

    // ── Mark as refunded ────────────────────────────────────────────────────
    public function refund(Payment $payment)
    {
        if ($payment->status !== 'paid') {
            return back()->with('error', 'Hanya pembayaran lunas yang dapat direfund.');
        }

        $payment->update(['status' => 'refunded']);

        return back()->with('success', "Pembayaran untuk {$payment->rental->rental_code} telah ditandai sebagai refund.");
    }
}
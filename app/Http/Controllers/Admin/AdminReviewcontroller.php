<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class AdminReviewcontroller extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['user', 'equipment.category', 'rental'])
            ->when($request->search, fn($q) =>
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"))
                  ->orWhereHas('equipment', fn($e) => $e->where('name', 'like', "%{$request->search}%"))
                  ->orWhereHas('rental', fn($r) => $r->where('rental_code', 'like', "%{$request->search}%"))
            )
            ->when($request->rating, fn($q) => $q->where('rating', $request->rating))
            ->when($request->equipment_id, fn($q) => $q->where('equipment_id', $request->equipment_id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total'   => Review::count(),
            'avg'     => round(Review::avg('rating'), 1),
            'five'    => Review::where('rating', 5)->count(),
            'low'     => Review::where('rating', '<=', 2)->count(),
        ];

        // Rating distribution
        $distribution = Review::selectRaw('rating, count(*) as total')
            ->groupBy('rating')
            ->orderByDesc('rating')
            ->pluck('total', 'rating')
            ->toArray();

        $equipmentList = Equipment::orderBy('name')->get(['id', 'name']);

        return view('admin.reviews.index', compact('reviews', 'summary', 'distribution', 'equipmentList'));
    }

    public function create()
    {
        // Only rentals with status 'returned' that don't have a review yet
        $rentals = Rental::with(['user', 'equipment'])
            ->where('status', 'returned')
            ->whereDoesntHave('review')
            ->orderByDesc('returned_at')
            ->get();

        return view('admin.reviews.create', compact('rentals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rental_id' => 'required|exists:rentals,id',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string|max:1000',
        ]);

        $rental = Rental::findOrFail($validated['rental_id']);

        // Check uniqueness
        if ($rental->review()->exists()) {
            return back()->withInput()
                ->with('error', 'Transaksi ini sudah memiliki ulasan.');
        }

        Review::create([
            ...$validated,
            'user_id'      => $rental->user_id,
            'equipment_id' => $rental->equipment_id,
        ]);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Ulasan berhasil ditambahkan.');
    }

    public function show(Review $review)
    {
        $review->load(['user', 'equipment.category', 'rental.payment']);

        return view('admin.reviews.show', compact('review'));
    }

    public function edit(Review $review)
    {
        $review->load(['user', 'equipment', 'rental']);

        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return redirect()->route('admin.reviews.show', $review)
            ->with('success', 'Ulasan berhasil diperbarui.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Ulasan berhasil dihapus.');
    }
}
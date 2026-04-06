<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->withCount(['equipment' => fn($q) => $q->where('is_available', true)])
            ->orderByDesc('equipment_count')
            ->limit(8)
            ->get();

        $featured = Equipment::available()
            ->with('category')
            ->withCount('rentals')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('rentals_count')
            ->limit(6)
            ->get();

        $stats = [
            'equipment'  => Equipment::available()->count(),
            'users'      => User::where('role', 'user')->count(),
            'rentals'    => Rental::where('status', 'returned')->count(),
            'avg_rating' => round(Review::avg('rating') ?? 4.9, 1),
        ];

        $reviews = Review::with(['user', 'equipment'])
            ->where('rating', '>=', 4)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->latest()
            ->limit(6)
            ->get();

        return view('welcome', compact('categories', 'featured', 'stats', 'reviews'));
    }

    public function catalog(Request $request)
    {
        $categories = Category::active()
            ->withCount(['equipment' => fn($q) => $q->where('is_available', true)])
            ->orderByDesc('equipment_count')
            ->get();

        $equipment = Equipment::available()
            ->with('category')
            ->withCount(['rentals', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->when($request->category, fn($q) =>
                $q->whereHas('category', fn($c) => $c->where('slug', $request->category))
            )
            ->when($request->search, fn($q) =>
                $q->where(fn($s) =>
                    $s->where('name', 'like', "%{$request->search}%")
                      ->orWhere('brand', 'like', "%{$request->search}%")
                      ->orWhere('description', 'like', "%{$request->search}%")
                )
            )
            ->when($request->price_min, fn($q) => $q->where('price_per_day', '>=', $request->price_min))
            ->when($request->price_max, fn($q) => $q->where('price_per_day', '<=', $request->price_max))
            ->when($request->condition, fn($q) => $q->where('condition', $request->condition))
            ->when($request->sort === 'price_asc',  fn($q) => $q->orderBy('price_per_day'))
            ->when($request->sort === 'price_desc', fn($q) => $q->orderByDesc('price_per_day'))
            ->when($request->sort === 'newest',     fn($q) => $q->latest())
            ->when($request->sort === 'rating',     fn($q) => $q->orderByDesc('reviews_avg_rating'))
            ->when(!$request->sort || $request->sort === 'popular', fn($q) => $q->orderByDesc('rentals_count'))
            ->paginate(12)
            ->withQueryString();

        return view('catalog', compact('equipment', 'categories'));
    }

    public function equipmentDetail(Equipment $equipment)
    {
        $equipment->load(['category', 'reviews.user']);
        $equipment->loadCount('rentals');

        $related = Equipment::available()
            ->where('category_id', $equipment->category_id)
            ->where('id', '!=', $equipment->id)
            ->with('category')
            ->withAvg('reviews', 'rating')
            ->limit(4)
            ->get();

        return view('equipment-detail', compact('equipment', 'related'));
    }
}
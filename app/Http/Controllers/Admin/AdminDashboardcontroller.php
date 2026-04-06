<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardcontroller extends Controller
{
    public function index()
    {
        // ── KPI Cards ───────────────────────────────────────────────────────
        $kpi = [
            'revenue_month'  => Payment::paid()
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'revenue_total'  => Payment::paid()->sum('amount'),
            'rentals_active' => Rental::where('status', 'active')->count(),
            'rentals_pending'=> Rental::where('status', 'pending')->count(),
            'users_total'    => User::where('role', 'user')->count(),
            'users_new_month'=> User::where('role', 'user')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'equipment_total'=> Equipment::count(),
            'low_stock'      => Equipment::where('stock', '<=', 3)->where('is_available', true)->count(),
            'avg_rating'     => round(Review::avg('rating') ?? 0, 1),
            'reviews_total'  => Review::count(),
        ];

        // ── Revenue last 12 months ───────────────────────────────────────────
        $revenueChart = collect(range(11, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            $amount = Payment::paid()
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('amount');
            return [
                'month'  => $date->format('M'),
                'year'   => $date->format('Y'),
                'amount' => (float) $amount,
            ];
        });

        // ── Rentals by status (donut) ────────────────────────────────────────
        $rentalStatus = Rental::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // ── Daily rentals last 30 days ───────────────────────────────────────
        $dailyRentals = collect(range(29, 0))->map(function ($i) {
            $date  = now()->subDays($i);
            $count = Rental::whereDate('created_at', $date)->count();
            return ['date' => $date->format('d/m'), 'count' => $count];
        });

        // ── Top equipment by rental count ────────────────────────────────────
        $topEquipment = Equipment::withCount('rentals')
            ->with('category')
            ->orderByDesc('rentals_count')
            ->limit(6)
            ->get();

        // ── Payment method distribution ──────────────────────────────────────
        $paymentMethods = Payment::selectRaw('method, count(*) as total, sum(amount) as revenue')
            ->groupBy('method')
            ->get();

        // ── Recent activity feed ─────────────────────────────────────────────
        $recentRentals = Rental::with(['user', 'equipment'])
            ->latest()
            ->limit(8)
            ->get();

        // ── Rating distribution ──────────────────────────────────────────────
        $ratingDist = Review::selectRaw('rating, count(*) as total')
            ->groupBy('rating')
            ->orderByDesc('rating')
            ->pluck('total', 'rating')
            ->toArray();

        return view('dashboard', compact(
            'kpi',
            'revenueChart',
            'rentalStatus',
            'dailyRentals',
            'topEquipment',
            'paymentMethods',
            'recentRentals',
            'ratingDist',
        ));
    }
}
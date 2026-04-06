<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUsercontroller extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount(['rentals', 'reviews'])
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
            )
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'all'     => User::count(),
            'admin'   => User::where('role', 'admin')->count(),
            'petugas' => User::where('role', 'petugas')->count(),
            'user'    => User::where('role', 'user')->count(),
        ];

        return view('admin.users.index', compact('users', 'summary'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => 'required|in:admin,petugas,user',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:500',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$validated['name']} berhasil ditambahkan.");
    }

    public function show(User $user)
    {
        $user->loadCount(['rentals', 'reviews', 'handledRentals']);

        $recentRentals = $user->rentals()
            ->with('equipment.category')
            ->latest()
            ->limit(5)
            ->get();

        $recentReviews = $user->reviews()
            ->with('equipment')
            ->latest()
            ->limit(5)
            ->get();

        $rentalStats = [
            'total'     => $user->rentals()->count(),
            'active'    => $user->rentals()->where('status', 'active')->count(),
            'returned'  => $user->rentals()->where('status', 'returned')->count(),
            'cancelled' => $user->rentals()->where('status', 'cancelled')->count(),
            // 'spent'     => $user->rentals()->where('status', 'returned')
            //                    ->join('payments', 'rentals.id', '=', 'payments.rental_id')
            //                    ->where('payments.status', 'paid')
            //                    ->sum('payments.amount'),
        ];

        return view('admin.users.show', compact('user', 'recentRentals', 'recentReviews', 'rentalStats'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'    => 'required|in:admin,petugas,user',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        // Prevent demoting the only admin
        if ($user->isAdmin() && $validated['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withInput()
                    ->with('error', 'Tidak dapat mengubah role admin terakhir.');
            }
        }

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Prevent self-delete
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Prevent deleting last admin
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus admin terakhir.');
        }

        // Block if has active rentals
        if ($user->rentals()->whereIn('status', ['active', 'confirmed'])->exists()) {
            return back()->with('error', 'Pengguna memiliki transaksi aktif dan tidak dapat dihapus.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$name} berhasil dihapus.");
    }

    // ── Reset password ───────────────────────────────────────────────────────
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password berhasil direset.');
    }
}
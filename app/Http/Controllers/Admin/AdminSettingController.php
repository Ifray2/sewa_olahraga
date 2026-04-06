<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminSettingController extends Controller
{
    public function index()
    {
        $admin = auth()->user();

        $stats = [
            'handled_rentals' => $admin->handledRentals()->count(),
            'joined'          => $admin->created_at->format('d F Y'),
            'last_updated'    => $admin->updated_at->diffForHumans(),
        ];

        return view('admin.settings.index', compact('admin', 'stats'));
    }

    // ── Update profil ────────────────────────────────────────────────────────
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // ── Ganti password ───────────────────────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($validated['current_password'], auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.'])
                         ->with('tab', 'password');
        }

        auth()->user()->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password berhasil diubah.')->with('tab', 'password');
    }
}
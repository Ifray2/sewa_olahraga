<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PetugasEquipmentcontroller extends Controller
{
    public function index(Request $request)
    {
        $equipment = Equipment::with('category')
            ->withCount('rentals')
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('brand', 'like', "%{$request->search}%")
            )
            ->when($request->category_id, fn($q) =>
                $q->where('category_id', $request->category_id)
            )
            ->when($request->condition, fn($q) =>
                $q->where('condition', $request->condition)
            )
            ->when($request->status !== null && $request->status !== '', fn($q) =>
                $q->where('is_available', $request->status)
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::active()->orderBy('name')->get();

        return view('petugas.equipment.index', compact('equipment', 'categories'));
    }

    public function create(Request $request)
    {
        $categories  = Category::active()->orderBy('name')->get();
        $selectedCat = $request->category;

        return view('petugas.equipment.create', compact('categories', 'selectedCat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:150|unique:equipment,name',
            'brand'        => 'nullable|string|max:80',
            'description'  => 'nullable|string|max:1000',
            'price_per_day'=> 'required|numeric|min:1000',
            'stock'        => 'required|integer|min:0',
            'condition'    => 'required|in:good,fair,poor',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_available' => 'boolean',
        ]);

        $validated['slug']         = Str::slug($validated['name']);
        $validated['is_available'] = $request->boolean('is_available', true);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('equipment', 'public');
        }

        Equipment::create($validated);

        return redirect()->route('petugas.equipment.index')
            ->with('success', "Alat \"{$validated['name']}\" berhasil ditambahkan.");
    }

    public function show(Equipment $equipment)
    {
        $equipment->load('category')->loadCount(['rentals', 'reviews']);
        $recentRentals = $equipment->rentals()->with('user')->latest()->take(5)->get();
        $reviews       = $equipment->reviews()->with('user')->latest()->paginate(5);

        return view('petugas.equipment.show', compact('equipment', 'recentRentals', 'reviews'));
    }

    public function edit(Equipment $equipment)
    {
        $categories = Category::active()->orderBy('name')->get();

        return view('petugas.equipment.edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:150|unique:equipment,name,' . $equipment->id,
            'brand'        => 'nullable|string|max:80',
            'description'  => 'nullable|string|max:1000',
            'price_per_day'=> 'required|numeric|min:1000',
            'stock'        => 'required|integer|min:0',
            'condition'    => 'required|in:good,fair,poor',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_available' => 'boolean',
        ]);

        $validated['slug']         = Str::slug($validated['name']);
        $validated['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('image')) {
            if ($equipment->image) {
                Storage::disk('public')->delete($equipment->image);
            }
            $validated['image'] = $request->file('image')->store('equipment', 'public');
        }

        if ($request->boolean('remove_image') && $equipment->image) {
            Storage::disk('public')->delete($equipment->image);
            $validated['image'] = null;
        }

        $equipment->update($validated);

        return redirect()->route('petugas.equipment.index')
            ->with('success', "Alat \"{$equipment->name}\" berhasil diperbarui.");
    }

    public function destroy(Equipment $equipment)
    {
        if ($equipment->rentals()->whereIn('status', ['pending','confirmed','active'])->exists()) {
            return back()->with('error', "Alat \"{$equipment->name}\" tidak dapat dihapus karena masih ada transaksi aktif.");
        }

        if ($equipment->image) {
            Storage::disk('public')->delete($equipment->image);
        }

        $name = $equipment->name;
        $equipment->delete();

        return redirect()->route('petugas.equipment.index')
            ->with('success', "Alat \"{$name}\" berhasil dihapus.");
    }

    public function toggleAvailability(Equipment $equipment)
    {
        $equipment->update(['is_available' => ! $equipment->is_available]);
        $status = $equipment->is_available ? 'tersedia' : 'tidak tersedia';

        return back()->with('success', "Alat \"{$equipment->name}\" sekarang {$status}.");
    }
}
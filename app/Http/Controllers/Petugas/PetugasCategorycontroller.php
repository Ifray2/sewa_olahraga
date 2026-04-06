<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PetugasCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('equipment')
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
            )
            ->when($request->status !== null && $request->status !== '', fn($q) =>
                $q->where('is_active', $request->status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('petugas.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('petugas.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'icon'        => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $validated['slug']      = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Category::create($validated);

        return redirect()->route('petugas.categories.index')
            ->with('success', "Kategori \"{$validated['name']}\" berhasil ditambahkan.");
    }

    public function show(Category $category)
    {
        $category->loadCount('equipment');
        $equipment = $category->equipment()->latest()->paginate(8);

        return view('petugas.categories.show', compact('category', 'equipment'));
    }

    public function edit(Category $category)
    {
        return view('petugas.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . $category->id,
            'icon'        => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $validated['slug']      = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()->route('petugas.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil diperbarui.");
    }

    public function destroy(Category $category)
    {
        if ($category->equipment()->exists()) {
            return back()->with('error', "Kategori \"{$category->name}\" tidak dapat dihapus karena masih memiliki alat terdaftar.");
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('petugas.categories.index')
            ->with('success', "Kategori \"{$name}\" berhasil dihapus.");
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => ! $category->is_active]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Kategori \"{$category->name}\" berhasil {$status}.");
    }
}
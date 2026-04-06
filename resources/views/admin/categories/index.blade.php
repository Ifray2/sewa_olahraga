<x-app-layout>
    <x-slot name="heading">Kategori</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <span>Kategori</span>
    </x-slot>

    {{-- Stats mini --}}
    <div class="stats-grid mb-6" style="grid-template-columns: repeat(3,1fr);">
        <div class="stat-card">
            <div class="stat-label">Total Kategori</div>
            <div class="stat-value">{{ $categories->total() }}</div>
            <div class="stat-bg-icon">◈</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Aktif</div>
            <div class="stat-value acid">{{ \App\Models\Category::where('is_active',true)->count() }}</div>
            <div class="stat-bg-icon">✓</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Nonaktif</div>
            <div class="stat-value">{{ \App\Models\Category::where('is_active',false)->count() }}</div>
            <div class="stat-bg-icon">✕</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Daftar Kategori</div>
            <div class="table-card-actions">
                {{-- Search & filter --}}
                <form method="GET" style="display:flex;gap:.5rem;flex-wrap:wrap;">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="search-input" placeholder="Cari kategori…">
                    <select name="status" class="search-input" style="width:130px;">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <button type="submit" class="btn-outline">Cari</button>
                    @if(request('search') || request('status') !== null)
                        <a href="{{ route('admin.categories.index') }}" class="btn-outline">Reset</a>
                    @endif
                </form>
                <a href="{{ route('admin.categories.create') }}" class="btn-add">+ Tambah Kategori</a>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Alat</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $i => $category)
                    <tr>
                        <td>{{ $categories->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.6rem;">
                                @if($category->icon)
                                    <span style="font-size:1.4rem;line-height:1;">{{ $category->icon }}</span>
                                @else
                                    <span style="width:1.8rem;height:1.8rem;background:var(--surface);border-radius:2px;display:flex;align-items:center;justify-content:center;color:var(--mid);font-size:.7rem;">N/A</span>
                                @endif
                                <div>
                                    <div class="td-name">{{ $category->name }}</div>
                                    <div style="font-size:.72rem;color:var(--mid);margin-top:.1rem;">{{ $category->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="max-width:220px;">
                            <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ $category->description ?: '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-active">{{ $category->equipment_count }} alat</span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge badge-active">Aktif</span>
                            @else
                                <span class="badge badge-cancelled">Nonaktif</span>
                            @endif
                        </td>
                        <td>{{ $category->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.categories.show', $category) }}" class="btn-action btn-view">Lihat</a>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn-action btn-edit">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.toggleStatus', $category) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-action"
                                        style="border-color:rgba(251,191,36,.3);color:#fbbf24;background:transparent;cursor:pointer;">
                                        {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" style="display:inline;"
                                      onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:3rem;color:var(--mid);">
                            <div style="font-size:2rem;margin-bottom:.75rem;">◈</div>
                            <div>Belum ada kategori ditemukan.</div>
                            <a href="{{ route('admin.categories.create') }}" class="btn-add" style="display:inline-flex;margin-top:1rem;">+ Tambah Kategori</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-footer">
            <div class="table-info">
                Menampilkan {{ $categories->firstItem() }}–{{ $categories->lastItem() }} dari {{ $categories->total() }} kategori
            </div>
            <div class="pagination">
                @if($categories->onFirstPage())
                    <span class="page-btn" style="opacity:.3;">‹</span>
                @else
                    <a href="{{ $categories->previousPageUrl() }}" class="page-btn">‹</a>
                @endif
                @foreach($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="page-btn {{ $page == $categories->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach
                @if($categories->hasMorePages())
                    <a href="{{ $categories->nextPageUrl() }}" class="page-btn">›</a>
                @else
                    <span class="page-btn" style="opacity:.3;">›</span>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
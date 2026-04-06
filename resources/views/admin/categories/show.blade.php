<x-app-layout>
    <x-slot name="heading">Detail Kategori</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.categories.index') }}">Kategori</a>
        <span>/</span>
        <span>{{ $category->name }}</span>
    </x-slot>

    <div style="display:grid;grid-template-columns:340px 1fr;gap:1.5rem;align-items:start;">

        {{-- ── LEFT: Info card ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Main info --}}
            <div class="table-card">
                <div style="padding:2rem;text-align:center;border-bottom:1px solid var(--border);">
                    <div style="font-size:4rem;line-height:1;margin-bottom:1rem;">
                        {{ $category->icon ?: '◈' }}
                    </div>
                    <div style="font-family:var(--font-display);font-size:1.6rem;letter-spacing:.04em;text-transform:uppercase;color:var(--white);margin-bottom:.4rem;">
                        {{ $category->name }}
                    </div>
                    <div style="font-size:.78rem;font-family:monospace;color:var(--acid);margin-bottom:.75rem;">
                        {{ $category->slug }}
                    </div>
                    @if($category->is_active)
                        <span class="badge badge-active">Aktif</span>
                    @else
                        <span class="badge badge-cancelled">Nonaktif</span>
                    @endif
                </div>

                <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
                    @if($category->description)
                        <div>
                            <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.4rem;">Deskripsi</div>
                            <div style="font-size:.85rem;line-height:1.7;color:var(--muted);">{{ $category->description }}</div>
                        </div>
                    @endif

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;padding-top:.75rem;border-top:1px solid var(--border);">
                        <div>
                            <div style="font-size:.62rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Total Alat</div>
                            <div style="font-family:var(--font-display);font-size:2rem;color:var(--acid);">{{ $category->equipment_count }}</div>
                        </div>
                        <div>
                            <div style="font-size:.62rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Dibuat</div>
                            <div style="font-size:.8rem;color:var(--muted);">{{ $category->created_at->format('d M Y') }}</div>
                            <div style="font-size:.72rem;color:var(--mid);">{{ $category->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>

                <div style="padding:1rem 1.5rem;border-top:1px solid var(--border);display:flex;gap:.5rem;">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn-add" style="flex:1;justify-content:center;">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.categories.toggleStatus', $category) }}" style="flex:1;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-outline" style="width:100%;justify-content:center;">
                            {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- ── RIGHT: Equipment list ── --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">Alat dalam Kategori Ini</div>
                <a href="{{ route('admin.equipment.create') }}?category={{ $category->id }}" class="btn-add">
                    + Tambah Alat
                </a>
            </div>

            @if($equipment->count())
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Alat</th>
                            <th>Merek</th>
                            <th>Harga/Hari</th>
                            <th>Stok</th>
                            <th>Kondisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipment as $i => $item)
                            <tr>
                                <td>{{ $equipment->firstItem() + $i }}</td>
                                <td>
                                    <div class="td-name">{{ $item->name }}</div>
                                    <div style="font-size:.72rem;color:var(--mid);">{{ $item->slug }}</div>
                                </td>
                                <td>{{ $item->brand ?: '—' }}</td>
                                <td style="color:var(--acid);font-family:var(--font-display);font-size:1rem;">
                                    Rp {{ number_format($item->price_per_day, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span style="color:{{ $item->stock > 5 ? 'var(--acid)' : ($item->stock > 0 ? '#fbbf24' : '#f87171') }}">
                                        {{ $item->stock }} unit
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $item->condition === 'good' ? 'badge-active' : ($item->condition === 'fair' ? 'badge-pending' : 'badge-cancelled') }}">
                                        {{ match($item->condition) { 'good' => 'Baik', 'fair' => 'Cukup', 'poor' => 'Perlu Perbaikan', default => $item->condition } }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->is_available)
                                        <span class="badge badge-active">Tersedia</span>
                                    @else
                                        <span class="badge badge-cancelled">Tidak Tersedia</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-group">
                                        <a href="{{ route('admin.equipment.edit', $item) }}" class="btn-action btn-edit">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

                <div class="table-footer">
                    <div class="table-info">
                        Menampilkan {{ $equipment->firstItem() }}–{{ $equipment->lastItem() }} dari {{ $equipment->total() }} alat
                    </div>
                    <div class="pagination">
                        @if($equipment->onFirstPage())
                            <span class="page-btn" style="opacity:.3;">‹</span>
                        @else
                            <a href="{{ $equipment->previousPageUrl() }}" class="page-btn">‹</a>
                        @endif
                        @foreach($equipment->getUrlRange(1, $equipment->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="page-btn {{ $page == $equipment->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach
                        @if($equipment->hasMorePages())
                            <a href="{{ $equipment->nextPageUrl() }}" class="page-btn">›</a>
                        @else
                            <span class="page-btn" style="opacity:.3;">›</span>
                        @endif
                    </div>
                </div>

            @else
                <div style="padding:3rem;text-align:center;color:var(--mid);">
                    <div style="font-size:2.5rem;margin-bottom:.75rem;">🏄</div>
                    <div style="margin-bottom:1rem;">Belum ada alat di kategori ini.</div>
                    <a href="{{ route('admin.equipment.create') }}?category={{ $category->id }}" class="btn-add" style="display:inline-flex;">
                        + Tambah Alat Pertama
                    </a>
                </div>
            @endif
        </div>

    </div>

    <style>
        @media (max-width: 900px) {
            div[style*="grid-template-columns:340px"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
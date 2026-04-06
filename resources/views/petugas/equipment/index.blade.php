@extends('layouts.petugas')

@section('content')

    {{-- Stats --}}
    <div class="stats-grid mb-6">
        <div class="stat-card">
            <div class="stat-label">Total Alat</div>
            <div class="stat-value">{{ \App\Models\Equipment::count() }}</div>
            <div class="stat-bg-icon">🏄</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tersedia</div>
            <div class="stat-value acid">{{ \App\Models\Equipment::available()->count() }}</div>
            <div class="stat-bg-icon">✓</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Stok Menipis</div>
            <div class="stat-value" style="color:#fbbf24;">{{ \App\Models\Equipment::where('stock','<=',3)->where('stock','>',0)->count() }}</div>
            <div class="stat-bg-icon">⚠</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tidak Tersedia</div>
            <div class="stat-value" style="color:#f87171;">{{ \App\Models\Equipment::where('is_available',false)->count() }}</div>
            <div class="stat-bg-icon">✕</div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Daftar Alat</div>
            <div class="table-card-actions">
                <form method="GET" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="search-input" placeholder="Cari nama / merek…">

                    <select name="category_id" class="search-input" style="width:150px;">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected':'' }}>
                                {{ $cat->icon }} {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="condition" class="search-input" style="width:130px;">
                        <option value="">Semua Kondisi</option>
                        <option value="good"  {{ request('condition')==='good'  ? 'selected':'' }}>Baik</option>
                        <option value="fair"  {{ request('condition')==='fair'  ? 'selected':'' }}>Cukup</option>
                        <option value="poor"  {{ request('condition')==='poor'  ? 'selected':'' }}>Perlu Perbaikan</option>
                    </select>

                    <select name="status" class="search-input" style="width:130px;">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status')==='1' ? 'selected':'' }}>Tersedia</option>
                        <option value="0" {{ request('status')==='0' ? 'selected':'' }}>Tidak Tersedia</option>
                    </select>

                    <button type="submit" class="btn-outline">Cari</button>
                    @if(request('search') || request('category_id') || request('condition') || request('status') !== null)
                        <a href="{{ route('petugas.equipment.index') }}" class="btn-outline">Reset</a>
                    @endif
                </form>
                <a href="{{ route('petugas.equipment.create') }}" class="btn-add">+ Tambah Alat</a>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Alat</th>
                    <th>Kategori</th>
                    <th>Harga/Hari</th>
                    <th>Stok</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th>Total Sewa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipment as $i => $item)
                    <tr>
                        <td>{{ $equipment->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                {{-- Thumbnail --}}
                                <div style="width:3rem;height:3rem;border-radius:2px;background:var(--surface);border:1px solid var(--border);overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                                    @if($item->image)
                                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                                             style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        {{ $item->category->icon ?? '🏄' }}
                                    @endif
                                </div>
                                <div>
                                    <div class="td-name">{{ $item->name }}</div>
                                    @if($item->brand)
                                        <div style="font-size:.72rem;color:var(--mid);">{{ $item->brand }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.78rem;color:var(--muted);">
                                {{ $item->category->icon ?? '' }} {{ $item->category->name }}
                            </span>
                        </td>
                        <td>
                            <span style="font-family:var(--font-display);font-size:1.1rem;color:var(--acid);">
                                Rp {{ number_format($item->price_per_day, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            @php $stockColor = $item->stock > 5 ? 'var(--acid)' : ($item->stock > 0 ? '#fbbf24' : '#f87171'); @endphp
                            <span style="color:{{ $stockColor }};font-weight:500;">{{ $item->stock }}</span>
                            <span style="color:var(--mid);font-size:.72rem;"> unit</span>
                        </td>
                        <td>
                            @php
                                $condClass = match($item->condition) {
                                    'good' => 'badge-active',
                                    'fair' => 'badge-pending',
                                    default => 'badge-cancelled',
                                };
                                $condLabel = match($item->condition) {
                                    'good' => 'Baik',
                                    'fair' => 'Cukup',
                                    default => 'Perlu Perbaikan',
                                };
                            @endphp
                            <span class="badge {{ $condClass }}">{{ $condLabel }}</span>
                        </td>
                        <td>
                            @if($item->is_available && $item->stock > 0)
                                <span class="badge badge-active">Tersedia</span>
                            @elseif($item->stock == 0)
                                <span class="badge badge-cancelled">Stok Habis</span>
                            @else
                                <span class="badge badge-cancelled">Tidak Tersedia</span>
                            @endif
                        </td>
                        <td style="color:var(--muted);">{{ $item->rentals_count }}×</td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('petugas.equipment.show', $item) }}" class="btn-action btn-view">Lihat</a>
                                <a href="{{ route('petugas.equipment.edit', $item) }}" class="btn-action btn-edit">Edit</a>
                                <form method="POST" action="{{ route('petugas.equipment.toggle', $item) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-action"
                                        style="border-color:rgba(251,191,36,.3);color:#fbbf24;background:transparent;cursor:pointer;font-family:var(--font-body);letter-spacing:.05em;font-size:.7rem;">
                                        {{ $item->is_available ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('petugas.equipment.destroy', $item) }}" style="display:inline;"
                                      onsubmit="return confirm('Hapus alat ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:3rem;color:var(--mid);">
                            <div style="font-size:2rem;margin-bottom:.75rem;">🏄</div>
                            <div>Belum ada alat ditemukan.</div>
                            <a href="{{ route('petugas.equipment.create') }}" class="btn-add" style="display:inline-flex;margin-top:1rem;">+ Tambah Alat</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
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
    </div>
@endsection
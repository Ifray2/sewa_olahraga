<x-app-layout>
    <x-slot name="heading">Ulasan</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <span>Ulasan</span>
    </x-slot>

    {{-- Stats + Distribution --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr) 280px;gap:1.25rem;margin-bottom:1.5rem;align-items:stretch;">

        {{-- 4 stat cards --}}
        <div class="stat-card">
            <div class="stat-label">Total Ulasan</div>
            <div class="stat-value acid">{{ $summary['total'] }}</div>
            <div class="stat-bg-icon">💬</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Rata-rata</div>
            <div class="stat-value acid" style="display:flex;align-items:baseline;gap:.3rem;">
                {{ $summary['avg'] }}
                <span style="font-size:1rem;color:#fbbf24;">★</span>
            </div>
            <div class="stat-bg-icon">⭐</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Bintang 5</div>
            <div class="stat-value" style="color:#fbbf24;">{{ $summary['five'] }}</div>
            <div class="stat-bg-icon">🌟</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Rating Rendah (≤2)</div>
            <div class="stat-value" style="color:#f87171;">{{ $summary['low'] }}</div>
            <div class="stat-bg-icon">⚠</div>
        </div>

        {{-- Distribution bar --}}
        <div class="table-card" style="margin:0;padding:0;">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--border);">
                <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);">Distribusi Rating</div>
            </div>
            <div style="padding:1rem 1.25rem;display:flex;flex-direction:column;gap:.55rem;">
                @for($r = 5; $r >= 1; $r--)
                    @php $cnt = $distribution[$r] ?? 0; $pct = $summary['total'] ? round($cnt / $summary['total'] * 100) : 0; @endphp
                    <div style="display:flex;align-items:center;gap:.6rem;">
                        <span style="width:.9rem;font-size:.72rem;color:#fbbf24;text-align:right;flex-shrink:0;">{{ $r }}</span>
                        <span style="font-size:.65rem;color:#fbbf24;">★</span>
                        <div style="flex:1;height:6px;background:var(--border);border-radius:3px;overflow:hidden;">
                            <div style="height:100%;width:{{ $pct }}%;background:{{ $r >= 4 ? 'var(--acid)' : ($r === 3 ? '#fbbf24' : '#f87171') }};border-radius:3px;transition:width .5s;"></div>
                        </div>
                        <span style="font-size:.68rem;color:var(--mid);width:1.8rem;text-align:right;flex-shrink:0;">{{ $cnt }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Daftar Ulasan</div>
            <div class="table-card-actions">
                <form method="GET" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="search-input" placeholder="Nama / alat / kode…">

                    <select name="rating" class="search-input" style="width:130px;">
                        <option value="">Semua Rating</option>
                        @for($r = 5; $r >= 1; $r--)
                            <option value="{{ $r }}" {{ request('rating') == $r ? 'selected':'' }}>
                                {{ str_repeat('★', $r) }}{{ str_repeat('☆', 5-$r) }}
                            </option>
                        @endfor
                    </select>

                    <select name="equipment_id" class="search-input" style="width:160px;">
                        <option value="">Semua Alat</option>
                        @foreach($equipmentList as $eq)
                            <option value="{{ $eq->id }}" {{ request('equipment_id') == $eq->id ? 'selected':'' }}>
                                {{ $eq->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn-outline">Cari</button>
                    @if(request()->hasAny(['search','rating','equipment_id']))
                        <a href="{{ route('admin.reviews.index') }}" class="btn-outline">Reset</a>
                    @endif
                </form>
                <a href="{{ route('admin.reviews.create') }}" class="btn-add">+ Tambah Ulasan</a>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Penyewa</th>
                    <th>Alat</th>
                    <th>Kode Transaksi</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $i => $review)
                    <tr>
                        <td>{{ $reviews->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.6rem;">
                                <div style="width:1.9rem;height:1.9rem;border-radius:50%;background:var(--acid);color:var(--black);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.75rem;flex-shrink:0;">
                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="td-name">{{ $review->user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:.82rem;color:var(--white);max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $review->equipment->name }}
                            </div>
                            <div style="font-size:.7rem;color:var(--mid);">{{ $review->equipment->category->name }}</div>
                        </td>
                        <td>
                            <a href="{{ route('admin.rentals.show', $review->rental) }}"
                               style="font-family:monospace;font-size:.78rem;color:var(--acid);text-decoration:none;white-space:nowrap;">
                                {{ $review->rental->rental_code }}
                            </a>
                        </td>
                        <td>
                            <div style="display:flex;flex-direction:column;gap:.1rem;">
                                {{-- Big star visual --}}
                                <div style="display:flex;gap:1px;">
                                    @for($s = 1; $s <= 5; $s++)
                                        <span style="font-size:.85rem;color:{{ $s <= $review->rating ? '#fbbf24' : 'var(--border)' }};">★</span>
                                    @endfor
                                </div>
                                <div style="font-size:.7rem;color:var(--mid);">{{ $review->rating }}/5</div>
                            </div>
                        </td>
                        <td style="max-width:200px;">
                            @if($review->comment)
                                <div style="font-size:.78rem;color:var(--muted);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    {{ $review->comment }}
                                </div>
                            @else
                                <span style="font-size:.75rem;color:var(--mid);font-style:italic;">Tanpa komentar</span>
                            @endif
                        </td>
                        <td style="font-size:.75rem;color:var(--mid);white-space:nowrap;">
                            {{ $review->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.reviews.show', $review) }}" class="btn-action btn-view">Lihat</a>
                                <a href="{{ route('admin.reviews.edit', $review) }}" class="btn-action btn-edit">Edit</a>
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" style="display:inline;"
                                      onsubmit="return confirm('Hapus ulasan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:3rem;color:var(--mid);">
                            <div style="font-size:2rem;margin-bottom:.75rem;">💬</div>
                            <div>Belum ada ulasan.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-footer">
            <div class="table-info">
                Menampilkan {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} dari {{ $reviews->total() }} ulasan
            </div>
            <div class="pagination">
                @if($reviews->onFirstPage())
                    <span class="page-btn" style="opacity:.3;">‹</span>
                @else
                    <a href="{{ $reviews->previousPageUrl() }}" class="page-btn">‹</a>
                @endif
                @foreach($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="page-btn {{ $page == $reviews->currentPage() ? 'active':'' }}">{{ $page }}</a>
                @endforeach
                @if($reviews->hasMorePages())
                    <a href="{{ $reviews->nextPageUrl() }}" class="page-btn">›</a>
                @else
                    <span class="page-btn" style="opacity:.3;">›</span>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 1100px) {
            div[style*="grid-template-columns:repeat(4,1fr) 280px"] {
                grid-template-columns: repeat(2,1fr)!important;
            }
        }
        @media (max-width: 600px) {
            div[style*="grid-template-columns:repeat(4,1fr) 280px"] {
                grid-template-columns: 1fr!important;
            }
        }
    </style>
</x-app-layout>
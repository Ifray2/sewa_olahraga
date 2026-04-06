<x-app-layout>
    <x-slot name="heading">Detail Alat</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.equipment.index') }}">Alat Olahraga</a>
        <span>/</span>
        <span>{{ $equipment->name }}</span>
    </x-slot>

    <div style="display:grid;grid-template-columns:360px 1fr;gap:1.5rem;align-items:start;">

        {{-- ── LEFT ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Image card --}}
            <div class="table-card" style="overflow:hidden;">
                <div style="aspect-ratio:4/3;background:var(--surface);position:relative;display:flex;align-items:center;justify-content:center;">
                    @if($equipment->image)
                        <img src="{{ Storage::url($equipment->image) }}" alt="{{ $equipment->name }}"
                             style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <div style="font-size:5rem;opacity:.3;">
                            {{ $equipment->category->icon ?? '🏄' }}
                        </div>
                    @endif

                    {{-- Status overlay --}}
                    <div style="position:absolute;top:.75rem;left:.75rem;display:flex;flex-direction:column;gap:.3rem;">
                        @if($equipment->is_available && $equipment->stock > 0)
                            <span class="badge badge-active">Tersedia</span>
                        @elseif($equipment->stock == 0)
                            <span class="badge badge-cancelled">Stok Habis</span>
                        @else
                            <span class="badge badge-cancelled">Tidak Tersedia</span>
                        @endif
                        @php
                            $condClass = match($equipment->condition){ 'good'=>'badge-active','fair'=>'badge-pending',default=>'badge-cancelled' };
                            $condLabel = match($equipment->condition){ 'good'=>'Baik','fair'=>'Cukup',default=>'Perlu Perbaikan' };
                        @endphp
                        <span class="badge {{ $condClass }}">{{ $condLabel }}</span>
                    </div>
                </div>

                <div style="padding:1.5rem;">
                    <div style="font-size:.72rem;color:var(--acid);margin-bottom:.3rem;display:flex;align-items:center;gap:.4rem;">
                        {{ $equipment->category->icon ?? '' }} {{ $equipment->category->name }}
                    </div>
                    <div style="font-family:var(--font-display);font-size:1.5rem;letter-spacing:.03em;text-transform:uppercase;color:var(--white);margin-bottom:.25rem;">
                        {{ $equipment->name }}
                    </div>
                    @if($equipment->brand)
                        <div style="font-size:.8rem;color:var(--mid);margin-bottom:1rem;">{{ $equipment->brand }}</div>
                    @endif

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.75rem;margin-bottom:1.25rem;">
                        <div style="text-align:center;padding:.75rem .5rem;background:var(--surface);border-radius:2px;">
                            <div style="font-family:var(--font-display);font-size:1.6rem;color:var(--acid);line-height:1;">{{ $equipment->stock }}</div>
                            <div style="font-size:.62rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;margin-top:.2rem;">Stok</div>
                        </div>
                        <div style="text-align:center;padding:.75rem .5rem;background:var(--surface);border-radius:2px;">
                            <div style="font-family:var(--font-display);font-size:1.6rem;color:var(--acid);line-height:1;">{{ $equipment->rentals_count }}</div>
                            <div style="font-size:.62rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;margin-top:.2rem;">Total Sewa</div>
                        </div>
                        <div style="text-align:center;padding:.75rem .5rem;background:var(--surface);border-radius:2px;">
                            <div style="font-family:var(--font-display);font-size:1.6rem;color:var(--acid);line-height:1;">{{ $equipment->reviews_count }}</div>
                            <div style="font-size:.62rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;margin-top:.2rem;">Ulasan</div>
                        </div>
                    </div>

                    <div style="padding:.85rem 1rem;background:var(--surface);border-radius:2px;margin-bottom:1.25rem;">
                        <div style="font-size:.62rem;color:var(--mid);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.3rem;">Harga Sewa</div>
                        <div style="font-family:var(--font-display);font-size:1.8rem;color:var(--acid);">
                            Rp {{ number_format($equipment->price_per_day, 0, ',', '.') }}
                        </div>
                        <div style="font-size:.72rem;color:var(--mid);">per hari</div>
                    </div>

                    @if($equipment->description)
                        <div style="font-size:.83rem;line-height:1.7;color:var(--muted);">
                            {{ $equipment->description }}
                        </div>
                    @endif
                </div>

                <div style="padding:1rem 1.5rem;border-top:1px solid var(--border);display:flex;gap:.5rem;">
                    <a href="{{ route('admin.equipment.edit', $equipment) }}" class="btn-add" style="flex:1;justify-content:center;">Edit</a>
                    <form method="POST" action="{{ route('admin.equipment.toggle', $equipment) }}" style="flex:1;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-outline" style="width:100%;justify-content:center;">
                            {{ $equipment->is_available ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- ── RIGHT ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Recent Rentals --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Riwayat Sewa Terbaru</div>
                    <a href="{{ route('admin.rentals.index') }}?equipment_id={{ $equipment->id }}" class="btn-outline">Lihat Semua</a>
                </div>

                @if($recentRentals->count())
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Penyewa</th>
                                <th>Durasi</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentRentals as $rental)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.rentals.show', $rental) }}"
                                           style="font-family:monospace;font-size:.8rem;color:var(--acid);text-decoration:none;">
                                            {{ $rental->rental_code }}
                                        </a>
                                    </td>
                                    <td class="td-name">{{ $rental->user->name }}</td>
                                    <td style="color:var(--muted);">{{ $rental->duration_days }} hari</td>
                                    <td style="color:var(--acid);font-family:var(--font-display);font-size:1rem;">
                                        Rp {{ number_format($rental->total_price, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @php
                                            $badgeMap = [
                                                'pending'   => 'badge-pending',
                                                'confirmed' => 'badge-confirmed',
                                                'active'    => 'badge-active',
                                                'returned'  => 'badge-returned',
                                                'cancelled' => 'badge-cancelled',
                                            ];
                                        @endphp
                                        <span class="badge {{ $badgeMap[$rental->status] ?? 'badge-returned' }}">
                                            {{ $rental->status_label }}
                                        </span>
                                    </td>
                                    <td style="color:var(--mid);font-size:.78rem;">{{ $rental->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="padding:2.5rem;text-align:center;color:var(--mid);">
                        <div style="font-size:2rem;margin-bottom:.5rem;">📋</div>
                        <div>Belum ada riwayat sewa.</div>
                    </div>
                @endif
            </div>

            {{-- Reviews --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Ulasan Pelanggan</div>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <span style="font-family:var(--font-display);font-size:1.4rem;color:var(--acid);">
                            {{ number_format($equipment->average_rating, 1) }}
                        </span>
                        <span style="color:#fbbf24;font-size:.85rem;">★</span>
                        <span style="font-size:.75rem;color:var(--mid);">({{ $equipment->reviews_count }} ulasan)</span>
                    </div>
                </div>

                @if($reviews->count())
                    <div style="padding:0 1.5rem;">
                        @foreach($reviews as $review)
                            <div style="padding:1.25rem 0;border-bottom:1px solid var(--border);">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.5rem;">
                                    <div style="display:flex;align-items:center;gap:.6rem;">
                                        <div style="width:2rem;height:2rem;border-radius:50%;background:var(--acid);color:var(--black);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0;">
                                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-size:.82rem;font-weight:500;color:var(--white);">{{ $review->user->name }}</div>
                                            <div style="font-size:.7rem;color:var(--mid);">{{ $review->created_at->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                    <span style="color:#fbbf24;font-size:.85rem;letter-spacing:.05em;">
                                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                    </span>
                                </div>
                                @if($review->comment)
                                    <p style="font-size:.83rem;line-height:1.7;color:var(--muted);font-style:italic;margin-left:2.6rem;">
                                        "{{ $review->comment }}"
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="table-footer">
                        <div class="table-info">{{ $reviews->total() }} ulasan</div>
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
                @else
                    <div style="padding:2.5rem;text-align:center;color:var(--mid);">
                        <div style="font-size:2rem;margin-bottom:.5rem;">★</div>
                        <div>Belum ada ulasan untuk alat ini.</div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <style>
        @media (max-width: 960px) {
            div[style*="grid-template-columns:360px"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
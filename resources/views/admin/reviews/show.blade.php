<x-app-layout>
    <x-slot name="heading">Detail Ulasan</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.reviews.index') }}">Ulasan</a>
        <span>/</span>
        <span>#{{ $review->id }}</span>
    </x-slot>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">

        {{-- ── LEFT ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Review card --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div style="display:flex;align-items:center;gap:.85rem;">
                        <div style="width:3rem;height:3rem;border-radius:50%;background:var(--acid);color:var(--black);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:1.4rem;flex-shrink:0;">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:.95rem;font-weight:600;color:var(--white);">{{ $review->user->name }}</div>
                            <div style="font-size:.75rem;color:var(--mid);">{{ $review->user->email }}</div>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:1.5rem;color:#fbbf24;letter-spacing:.05em;">
                            @for($s = 1; $s <= 5; $s++)
                                <span style="color:{{ $s <= $review->rating ? '#fbbf24' : 'var(--border)' }};">★</span>
                            @endfor
                        </div>
                        <div style="font-family:var(--font-display);font-size:2rem;color:{{ $review->rating >= 4 ? 'var(--acid)' : ($review->rating === 3 ? '#fbbf24' : '#f87171') }};">
                            {{ $review->rating }}<span style="font-size:.9rem;color:var(--mid);">/5</span>
                        </div>
                    </div>
                </div>

                <div style="padding:1.5rem;">
                    {{-- Comment --}}
                    @if($review->comment)
                        <div style="padding:1.5rem;background:var(--surface);border-radius:4px;border-left:3px solid {{ $review->rating >= 4 ? 'var(--acid)' : ($review->rating === 3 ? '#fbbf24' : '#f87171') }};margin-bottom:1.5rem;position:relative;">
                            <span style="position:absolute;top:.75rem;left:1rem;font-size:2.5rem;color:var(--border);line-height:1;font-family:serif;">"</span>
                            <p style="font-size:.9rem;line-height:1.8;color:var(--muted);padding-left:1.5rem;margin:0;font-style:italic;">
                                {{ $review->comment }}
                            </p>
                        </div>
                    @else
                        <div style="padding:1.5rem;text-align:center;background:var(--surface);border-radius:4px;border:1px dashed var(--border);margin-bottom:1.5rem;">
                            <div style="font-size:.82rem;color:var(--mid);font-style:italic;">Tidak ada komentar ditulis.</div>
                        </div>
                    @endif

                    {{-- Meta grid --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--border);border:1px solid var(--border);border-radius:4px;overflow:hidden;">
                        <div style="background:var(--card);padding:.85rem 1.1rem;">
                            <div style="font-size:.6rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.25rem;">ID Ulasan</div>
                            <div style="font-size:.88rem;color:var(--white);">#{{ $review->id }}</div>
                        </div>
                        <div style="background:var(--card);padding:.85rem 1.1rem;">
                            <div style="font-size:.6rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.25rem;">Tanggal</div>
                            <div style="font-size:.88rem;color:var(--white);">{{ $review->created_at->format('d F Y, H:i') }}</div>
                        </div>
                        <div style="background:var(--card);padding:.85rem 1.1rem;">
                            <div style="font-size:.6rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.25rem;">Kode Transaksi</div>
                            <a href="{{ route('admin.rentals.show', $review->rental) }}"
                               style="font-family:monospace;font-size:.88rem;color:var(--acid);text-decoration:none;">
                                {{ $review->rental->rental_code }}
                            </a>
                        </div>
                        <div style="background:var(--card);padding:.85rem 1.1rem;">
                            <div style="font-size:.6rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.25rem;">Terakhir Diperbarui</div>
                            <div style="font-size:.88rem;color:var(--white);">{{ $review->updated_at->format('d F Y, H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Equipment detail --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Alat yang Diulas</div>
                    <a href="{{ route('admin.equipment.show', $review->equipment) }}" class="btn-outline">Lihat Alat</a>
                </div>
                <div style="padding:1.5rem;">
                    <div style="display:flex;gap:1rem;padding:1.25rem;background:var(--surface);border-radius:4px;border:1px solid var(--border);">
                        <div style="width:4.5rem;height:4.5rem;border-radius:2px;background:var(--card);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:2.2rem;flex-shrink:0;overflow:hidden;">
                            @if($review->equipment->image)
                                <img src="{{ Storage::url($review->equipment->image) }}" alt=""
                                     style="width:100%;height:100%;object-fit:cover;">
                            @else
                                {{ $review->equipment->category->icon ?? '🏄' }}
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-family:var(--font-display);font-size:1.1rem;text-transform:uppercase;color:var(--white);">
                                {{ $review->equipment->name }}
                            </div>
                            <div style="font-size:.78rem;color:var(--mid);margin:.2rem 0;">
                                {{ $review->equipment->category->name }}
                                @if($review->equipment->brand) · {{ $review->equipment->brand }} @endif
                            </div>
                            <div style="font-size:.78rem;color:var(--acid);">
                                Rp {{ number_format($review->equipment->price_per_day, 0, ',', '.') }}/hari
                            </div>
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            @php $avgRating = $review->equipment->average_rating; @endphp
                            <div style="font-family:var(--font-display);font-size:1.4rem;color:#fbbf24;">
                                {{ number_format($avgRating, 1) }}
                            </div>
                            <div style="font-size:.62rem;color:var(--mid);text-transform:uppercase;letter-spacing:.08em;">Avg Rating</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rental detail --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Detail Transaksi</div>
                    <a href="{{ route('admin.rentals.show', $review->rental) }}" class="btn-outline">Lihat Transaksi</a>
                </div>
                <div style="padding:1.5rem;">
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
                        @php
                            $cells = [
                                ['Durasi', $review->rental->duration_days . ' hari'],
                                ['Jumlah Unit', $review->rental->quantity . ' unit'],
                                ['Total Sewa', 'Rp ' . number_format($review->rental->total_price, 0, ',', '.')],
                                ['Mulai', $review->rental->start_date->format('d M Y')],
                                ['Selesai', $review->rental->end_date->format('d M Y')],
                                ['Pembayaran', $review->rental->payment?->status === 'paid' ? 'Lunas' : 'Belum'],
                            ];
                        @endphp
                        @foreach($cells as $cell)
                            <div style="padding:.85rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                                <div style="font-size:.6rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.3rem;">{{ $cell[0] }}</div>
                                <div style="font-size:.88rem;color:var(--white);">{{ $cell[1] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        {{-- ── RIGHT ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Actions --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Aksi</div>
                </div>
                <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.5rem;">
                    <a href="{{ route('admin.reviews.edit', $review) }}" class="btn-add" style="justify-content:center;padding:.75rem;">
                        ✎ Edit Ulasan
                    </a>
                    <a href="{{ route('admin.reviews.index') }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                        ← Kembali ke Daftar
                    </a>
                </div>
            </div>

            {{-- Rating visual --}}
            <div class="table-card">
                <div style="padding:1.5rem;text-align:center;">
                    <div style="font-size:.62rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.75rem;">Rating Diberikan</div>
                    <div style="font-family:var(--font-display);font-size:5rem;line-height:1;color:{{ $review->rating >= 4 ? 'var(--acid)' : ($review->rating === 3 ? '#fbbf24' : '#f87171') }};">
                        {{ $review->rating }}
                    </div>
                    <div style="font-size:1.8rem;letter-spacing:.1em;color:#fbbf24;margin:.5rem 0;">
                        @for($s = 1; $s <= 5; $s++)
                            <span style="color:{{ $s <= $review->rating ? '#fbbf24' : 'var(--border)' }};">★</span>
                        @endfor
                    </div>
                    <div style="font-size:.75rem;color:var(--mid);">
                        @if($review->rating === 5) Sangat Puas
                        @elseif($review->rating === 4) Puas
                        @elseif($review->rating === 3) Cukup
                        @elseif($review->rating === 2) Kurang Puas
                        @else Tidak Puas
                        @endif
                    </div>
                </div>
            </div>

            {{-- Penyewa --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Penyewa</div>
                </div>
                <div style="padding:1.25rem;">
                    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.85rem;">
                        <div style="width:2.8rem;height:2.8rem;border-radius:50%;background:var(--acid);color:var(--black);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:1.3rem;flex-shrink:0;">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:.9rem;font-weight:500;color:var(--white);">{{ $review->user->name }}</div>
                            <div style="font-size:.72rem;color:var(--mid);">{{ $review->user->email }}</div>
                        </div>
                    </div>
                    @if($review->user->phone)
                        <div style="font-size:.8rem;color:var(--muted);margin-bottom:.3rem;">📞 {{ $review->user->phone }}</div>
                    @endif
                </div>
            </div>

            {{-- Danger zone --}}
            <div class="table-card" style="border-color:rgba(248,113,113,.2);">
                <div class="table-card-header" style="border-color:rgba(248,113,113,.2);">
                    <div class="table-card-title" style="color:#f87171;">Zona Berbahaya</div>
                </div>
                <div style="padding:1.25rem;">
                    <div style="font-size:.78rem;color:var(--mid);margin-bottom:.75rem;">
                        Hapus ulasan ini secara permanen. Tindakan tidak dapat dibatalkan.
                    </div>
                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                          onsubmit="return confirm('Hapus ulasan ini? Tindakan tidak dapat dibatalkan.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-action btn-delete"
                                style="width:100%;justify-content:center;padding:.6rem;font-size:.78rem;border-width:1px;">
                            Hapus Ulasan
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <style>
        @media (max-width: 900px) {
            div[style*="grid-template-columns:1fr 300px"] { grid-template-columns:1fr!important; }
            div[style*="grid-template-columns:repeat(3,1fr)"] { grid-template-columns:1fr 1fr!important; }
            div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns:1fr!important; }
        }
    </style>
</x-app-layout>
<x-app-layout>
    <x-slot name="heading">Detail Transaksi</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.rentals.index') }}">Transaksi</a>
        <span>/</span>
        <span>{{ $rental->rental_code }}</span>
    </x-slot>

    @php
        $badgeMap = [
            'pending'   => 'badge-pending',
            'confirmed' => 'badge-confirmed',
            'active'    => 'badge-active',
            'returned'  => 'badge-returned',
            'cancelled' => 'badge-cancelled',
        ];
        $statusFlow = ['pending','confirmed','active','returned'];
        $currentStep = array_search($rental->status, $statusFlow);
    @endphp

    {{-- ── Status progress bar ── --}}
    @if($rental->status !== 'cancelled')
    <div class="table-card mb-6">
        <div style="padding:1.5rem 2rem;">
            <div style="display:flex;align-items:center;position:relative;">
                {{-- Line --}}
                <div style="position:absolute;top:1rem;left:1.5rem;right:1.5rem;height:2px;background:var(--border);z-index:0;"></div>
                <div style="position:absolute;top:1rem;left:1.5rem;height:2px;background:var(--acid);z-index:1;
                    width:{{ $currentStep === false ? '0' : ($currentStep / (count($statusFlow)-1) * 100) }}%;
                    transition:width .5s;"></div>

                @foreach($statusFlow as $i => $step)
                    @php
                        $done    = $currentStep !== false && $i <= $currentStep;
                        $current = $currentStep !== false && $i === $currentStep;
                        $labels  = ['pending'=>'Menunggu','confirmed'=>'Dikonfirmasi','active'=>'Aktif','returned'=>'Dikembalikan'];
                    @endphp
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;position:relative;z-index:2;">
                        <div style="width:2rem;height:2rem;border-radius:50%;
                            background:{{ $done ? 'var(--acid)' : 'var(--surface)' }};
                            border:2px solid {{ $done ? 'var(--acid)' : 'var(--border)' }};
                            display:flex;align-items:center;justify-content:center;
                            font-size:.75rem;color:{{ $done ? 'var(--black)' : 'var(--mid)' }};
                            font-weight:700;transition:all .3s;">
                            {{ $done ? '✓' : ($i + 1) }}
                        </div>
                        <div style="font-size:.65rem;letter-spacing:.08em;text-transform:uppercase;
                            color:{{ $current ? 'var(--acid)' : ($done ? 'var(--muted)' : 'var(--mid)') }};
                            margin-top:.5rem;text-align:center;white-space:nowrap;">
                            {{ $labels[$step] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-error mb-6">✕ Transaksi ini telah dibatalkan.</div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start;">

        {{-- ── LEFT ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Rental info --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <div style="font-family:monospace;font-size:.85rem;color:var(--acid);margin-bottom:.3rem;">
                            {{ $rental->rental_code }}
                        </div>
                        <div class="table-card-title">Informasi Transaksi</div>
                    </div>
                    <span class="badge {{ $badgeMap[$rental->status] ?? 'badge-returned' }}" style="font-size:.75rem;padding:.3rem .85rem;">
                        {{ $rental->status_label }}
                    </span>
                </div>
                <div style="padding:1.5rem;">

                    {{-- Equipment --}}
                    <div style="display:flex;gap:1rem;padding:1.25rem;background:var(--surface);border-radius:4px;margin-bottom:1.5rem;border:1px solid var(--border);">
                        <div style="width:4rem;height:4rem;border-radius:2px;background:var(--card);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0;">
                            @if($rental->equipment->image)
                                <img src="{{ Storage::url($rental->equipment->image) }}" alt=""
                                     style="width:100%;height:100%;object-fit:cover;border-radius:2px;">
                            @else
                                {{ $rental->equipment->category->icon ?? '🏄' }}
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-family:var(--font-display);font-size:1.1rem;text-transform:uppercase;color:var(--white);margin-bottom:.2rem;">
                                {{ $rental->equipment->name }}
                            </div>
                            <div style="font-size:.75rem;color:var(--mid);">
                                {{ $rental->equipment->category->name }}
                                @if($rental->equipment->brand) · {{ $rental->equipment->brand }} @endif
                            </div>
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            <div style="font-family:var(--font-display);font-size:1.1rem;color:var(--acid);">
                                Rp {{ number_format($rental->equipment->price_per_day, 0, ',', '.') }}
                            </div>
                            <div style="font-size:.7rem;color:var(--mid);">per hari</div>
                        </div>
                    </div>

                    {{-- Detail rows --}}
                    @php
                        $rows = [
                            ['Jumlah Unit',     $rental->quantity . ' unit'],
                            ['Tanggal Mulai',   $rental->start_date->format('d F Y')],
                            ['Tanggal Selesai', $rental->end_date->format('d F Y')],
                            ['Durasi',          $rental->duration_days . ' hari'],
                        ];
                    @endphp
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--border);border:1px solid var(--border);border-radius:4px;margin-bottom:1.25rem;">
                        @foreach($rows as $row)
                            <div style="background:var(--card);padding:1rem 1.25rem;">
                                <div style="font-size:.62rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.3rem;">{{ $row[0] }}</div>
                                <div style="font-size:.9rem;color:var(--white);">{{ $row[1] }}</div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Total --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem;background:rgba(200,245,66,.06);border:1px solid rgba(200,245,66,.2);border-radius:4px;margin-bottom:1.25rem;">
                        <div>
                            <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Total Pembayaran</div>
                            <div style="font-size:.75rem;color:var(--mid);">
                                Rp {{ number_format($rental->equipment->price_per_day, 0, ',', '.') }} × {{ $rental->duration_days }} hari × {{ $rental->quantity }} unit
                            </div>
                        </div>
                        <div style="font-family:var(--font-display);font-size:2rem;color:var(--acid);">
                            Rp {{ number_format($rental->total_price, 0, ',', '.') }}
                        </div>
                    </div>

                    @if($rental->notes)
                        <div style="padding:1rem;background:var(--surface);border-radius:4px;border-left:2px solid var(--acid);">
                            <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.4rem;">Catatan Penyewa</div>
                            <div style="font-size:.85rem;color:var(--muted);line-height:1.7;">{{ $rental->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Riwayat Status</div>
                </div>
                <div style="padding:1.5rem;">
                    <div style="display:flex;flex-direction:column;gap:0;">

                        @php
                            $timeline = [
                                ['icon'=>'📝','label'=>'Transaksi Dibuat','time'=>$rental->created_at,'show'=>true],
                                ['icon'=>'✅','label'=>'Dikonfirmasi','time'=>$rental->confirmed_at,'show'=>!!$rental->confirmed_at],
                                ['icon'=>'🏄','label'=>'Alat Diambil / Aktif','time'=>$rental->confirmed_at,'show'=>in_array($rental->status,['active','returned'])],
                                ['icon'=>'📦','label'=>'Alat Dikembalikan','time'=>$rental->returned_at,'show'=>!!$rental->returned_at],
                                ['icon'=>'✕','label'=>'Dibatalkan','time'=>$rental->updated_at,'show'=>$rental->status==='cancelled'],
                            ];
                        @endphp

                        @foreach($timeline as $i => $event)
                            @if($event['show'])
                                <div style="display:flex;gap:1rem;position:relative;
                                    {{ !$loop->last ? 'padding-bottom:1.25rem;' : '' }}">
                                    @if(!$loop->last)
                                        <div style="position:absolute;left:1rem;top:2rem;bottom:0;width:1px;background:var(--border);"></div>
                                    @endif
                                    <div style="width:2rem;height:2rem;border-radius:50%;background:var(--surface);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;position:relative;z-index:1;">
                                        {{ $event['icon'] }}
                                    </div>
                                    <div>
                                        <div style="font-size:.85rem;font-weight:500;color:var(--white);">{{ $event['label'] }}</div>
                                        @if($event['time'])
                                            <div style="font-size:.72rem;color:var(--mid);margin-top:.1rem;">
                                                {{ $event['time']->format('d M Y, H:i') }}
                                            </div>
                                        @endif
                                        @if($rental->handler && in_array($event['label'], ['Dikonfirmasi','Alat Dikembalikan']))
                                            <div style="font-size:.7rem;color:var(--mid);margin-top:.1rem;">
                                                oleh {{ $rental->handler->name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach

                    </div>
                </div>
            </div>

            {{-- Review --}}
            @if($rental->review)
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Ulasan Pelanggan</div>
                    </div>
                    <div style="padding:1.5rem;">
                        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
                            <div style="width:2.2rem;height:2.2rem;border-radius:50%;background:var(--acid);color:var(--black);display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($rental->review->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size:.85rem;font-weight:500;color:var(--white);">{{ $rental->review->user->name ?? '—' }}</div>
                                <div style="color:#fbbf24;font-size:.9rem;letter-spacing:.05em;">
                                    {{ str_repeat('★', $rental->review->rating) }}{{ str_repeat('☆', 5 - $rental->review->rating) }}
                                </div>
                            </div>
                        </div>
                        @if($rental->review->comment)
                            <p style="font-size:.85rem;line-height:1.7;color:var(--muted);font-style:italic;">
                                "{{ $rental->review->comment }}"
                            </p>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        {{-- ── RIGHT SIDEBAR ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Quick Actions --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Aksi Cepat</div>
                </div>
                <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.5rem;">

                    @if($rental->status === 'pending')
                        <form method="POST" action="{{ route('admin.rentals.confirm', $rental) }}">
                            @csrf
                            <button type="submit" class="btn-add" style="width:100%;justify-content:center;padding:.7rem;">
                                ✅ Konfirmasi Transaksi
                            </button>
                        </form>
                    @endif

                    @if($rental->status === 'confirmed')
                        <form method="POST" action="{{ route('admin.rentals.activate', $rental) }}">
                            @csrf
                            <button type="submit" class="btn-add" style="width:100%;justify-content:center;padding:.7rem;">
                                🏄 Tandai Aktif (Alat Diambil)
                            </button>
                        </form>
                    @endif

                    @if($rental->status === 'active')
                        <form method="POST" action="{{ route('admin.rentals.return', $rental) }}"
                              onsubmit="return confirm('Konfirmasi alat sudah dikembalikan?')">
                            @csrf
                            <button type="submit" class="btn-add" style="width:100%;justify-content:center;padding:.7rem;">
                                📦 Tandai Dikembalikan
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.rentals.edit', $rental) }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                        ✎ Edit Transaksi
                    </a>

                    @if(!in_array($rental->status, ['returned','cancelled']))
                        <form method="POST" action="{{ route('admin.rentals.cancel', $rental) }}"
                              onsubmit="return confirm('Yakin ingin membatalkan transaksi ini?')">
                            @csrf
                            <button type="submit" class="btn-action btn-delete"
                                    style="width:100%;justify-content:center;padding:.65rem;font-size:.8rem;">
                                ✕ Batalkan Transaksi
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Penyewa --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Data Penyewa</div>
                </div>
                <div style="padding:1.25rem;">
                    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                        <div style="width:2.8rem;height:2.8rem;border-radius:50%;background:var(--acid);color:var(--black);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:1.3rem;flex-shrink:0;">
                            {{ strtoupper(substr($rental->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:.9rem;font-weight:500;color:var(--white);">{{ $rental->user->name }}</div>
                            <div style="font-size:.72rem;color:var(--mid);">{{ $rental->user->email }}</div>
                        </div>
                    </div>
                    @if($rental->user->phone)
                        <div style="font-size:.8rem;color:var(--muted);margin-bottom:.4rem;">📞 {{ $rental->user->phone }}</div>
                    @endif
                    @if($rental->user->address)
                        <div style="font-size:.8rem;color:var(--muted);">📍 {{ $rental->user->address }}</div>
                    @endif
                </div>
            </div>

            {{-- Pembayaran --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Pembayaran</div>
                </div>
                <div style="padding:1.25rem;">
                    @if($rental->payment)
                        <div style="display:flex;flex-direction:column;gap:.6rem;">
                            <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                                <span style="color:var(--mid);">Metode</span>
                                <span style="color:var(--white);">{{ $rental->payment->method_label }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                                <span style="color:var(--mid);">Jumlah</span>
                                <span style="color:var(--acid);font-family:var(--font-display);font-size:1.1rem;">
                                    {{ $rental->payment->formatted_amount }}
                                </span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                                <span style="color:var(--mid);">Status</span>
                                @if($rental->payment->status === 'paid')
                                    <span class="badge badge-active">Lunas</span>
                                @elseif($rental->payment->status === 'refunded')
                                    <span class="badge badge-returned">Refund</span>
                                @else
                                    <span class="badge badge-pending">Belum Bayar</span>
                                @endif
                            </div>
                            @if($rental->payment->paid_at)
                                <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                                    <span style="color:var(--mid);">Dibayar</span>
                                    <span style="color:var(--muted);">{{ $rental->payment->paid_at->format('d M Y, H:i') }}</span>
                                </div>
                            @endif
                            @if($rental->payment->proof_image)
                                <div style="margin-top:.75rem;">
                                    <div style="font-size:.65rem;letter-spacing:.12em;text-transform:uppercase;color:var(--mid);margin-bottom:.4rem;">Bukti Pembayaran</div>
                                    <img src="{{ Storage::url($rental->payment->proof_image) }}" alt="Bukti"
                                         style="width:100%;border-radius:2px;border:1px solid var(--border);">
                                </div>
                            @endif
                        </div>
                    @else
                        <div style="text-align:center;padding:1rem 0;color:var(--mid);font-size:.82rem;">
                            Belum ada data pembayaran.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Handled by --}}
            @if($rental->handler)
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Diproses Oleh</div>
                    </div>
                    <div style="padding:1.25rem;display:flex;align-items:center;gap:.75rem;">
                        <div style="width:2.2rem;height:2.2rem;border-radius:50%;background:rgba(96,165,250,.2);color:#60a5fa;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;">
                            {{ strtoupper(substr($rental->handler->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:.85rem;color:var(--white);">{{ $rental->handler->name }}</div>
                            <div style="font-size:.7rem;color:var(--mid);text-transform:uppercase;letter-spacing:.08em;">{{ ucfirst($rental->handler->role) }}</div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <style>
        @media (max-width: 900px) {
            div[style*="grid-template-columns:1fr 340px"] { grid-template-columns:1fr!important; }
            div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns:1fr!important; }
        }
    </style>
</x-app-layout>
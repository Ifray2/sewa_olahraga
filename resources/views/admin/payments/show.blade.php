<x-app-layout>
    <x-slot name="heading">Detail Pembayaran</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.payments.index') }}">Pembayaran</a>
        <span>/</span>
        <span>#{{ $payment->id }}</span>
    </x-slot>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start;">

        {{-- ── LEFT ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Status banner --}}
            @if($payment->status === 'paid')
                <div style="padding:1.25rem 1.5rem;background:rgba(200,245,66,.07);border:1px solid rgba(200,245,66,.25);border-radius:4px;display:flex;align-items:center;gap:1rem;">
                    <span style="font-size:1.8rem;">✅</span>
                    <div>
                        <div style="font-family:var(--font-display);font-size:1.2rem;color:var(--acid);letter-spacing:.04em;">PEMBAYARAN LUNAS</div>
                        <div style="font-size:.8rem;color:var(--mid);">
                            Dibayar pada {{ $payment->paid_at?->format('d F Y, H:i') ?? '—' }}
                        </div>
                    </div>
                    <div style="margin-left:auto;text-align:right;">
                        <div style="font-family:var(--font-display);font-size:2rem;color:var(--acid);">{{ $payment->formatted_amount }}</div>
                        <div style="font-size:.72rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;">Total Dibayar</div>
                    </div>
                </div>
            @elseif($payment->status === 'refunded')
                <div style="padding:1.25rem 1.5rem;background:rgba(248,113,113,.07);border:1px solid rgba(248,113,113,.25);border-radius:4px;display:flex;align-items:center;gap:1rem;">
                    <span style="font-size:1.8rem;">↩</span>
                    <div>
                        <div style="font-family:var(--font-display);font-size:1.2rem;color:#f87171;letter-spacing:.04em;">PEMBAYARAN DIREFUND</div>
                        <div style="font-size:.8rem;color:var(--mid);">Dana telah dikembalikan kepada penyewa.</div>
                    </div>
                    <div style="margin-left:auto;">
                        <div style="font-family:var(--font-display);font-size:2rem;color:#f87171;">{{ $payment->formatted_amount }}</div>
                    </div>
                </div>
            @else
                <div style="padding:1.25rem 1.5rem;background:rgba(251,191,36,.07);border:1px solid rgba(251,191,36,.25);border-radius:4px;display:flex;align-items:center;gap:1rem;">
                    <span style="font-size:1.8rem;">⏳</span>
                    <div>
                        <div style="font-family:var(--font-display);font-size:1.2rem;color:#fbbf24;letter-spacing:.04em;">MENUNGGU PEMBAYARAN</div>
                        <div style="font-size:.8rem;color:var(--mid);">Pembayaran belum dikonfirmasi.</div>
                    </div>
                    <div style="margin-left:auto;">
                        <div style="font-family:var(--font-display);font-size:2rem;color:#fbbf24;">{{ $payment->formatted_amount }}</div>
                    </div>
                </div>
            @endif

            {{-- Detail pembayaran --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Detail Pembayaran</div>
                    <a href="{{ route('admin.payments.edit', $payment) }}" class="btn-outline">Edit</a>
                </div>
                <div style="padding:1.5rem;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--border);border:1px solid var(--border);border-radius:4px;overflow:hidden;margin-bottom:1.5rem;">
                        @php
                            $methodIcons = ['transfer'=>'🏦','cash'=>'💵','ewallet'=>'📱'];
                            $details = [
                                ['ID Pembayaran',  '#' . $payment->id],
                                ['Metode',         ($methodIcons[$payment->method] ?? '💳') . ' ' . $payment->method_label],
                                ['Jumlah',         $payment->formatted_amount],
                                ['Status',         $payment->status],
                                ['Dibuat',         $payment->created_at->format('d M Y, H:i')],
                                ['Dibayar',        $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : '—'],
                            ];
                        @endphp
                        @foreach($details as $row)
                            <div style="background:var(--card);padding:1rem 1.25rem;">
                                <div style="font-size:.62rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.3rem;">{{ $row[0] }}</div>
                                @if($row[0] === 'Status')
                                    @if($payment->status === 'paid')
                                        <span class="badge badge-active">Lunas</span>
                                    @elseif($payment->status === 'refunded')
                                        <span class="badge badge-returned">Refunded</span>
                                    @else
                                        <span class="badge badge-pending">Belum Bayar</span>
                                    @endif
                                @elseif($row[0] === 'Jumlah')
                                    <div style="font-family:var(--font-display);font-size:1.2rem;color:var(--acid);">{{ $row[1] }}</div>
                                @else
                                    <div style="font-size:.88rem;color:var(--white);">{{ $row[1] }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Bukti pembayaran --}}
                    @if($payment->proof_image)
                        <div>
                            <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.75rem;">Bukti Pembayaran</div>
                            <div style="position:relative;display:inline-block;">
                                <a href="{{ Storage::url($payment->proof_image) }}" target="_blank">
                                    <img src="{{ Storage::url($payment->proof_image) }}" alt="Bukti Pembayaran"
                                         style="max-width:100%;max-height:400px;border-radius:4px;border:1px solid var(--border);display:block;cursor:zoom-in;">
                                </a>
                                <a href="{{ Storage::url($payment->proof_image) }}" target="_blank"
                                   style="position:absolute;top:.5rem;right:.5rem;background:rgba(0,0,0,.6);color:var(--white);padding:.3rem .6rem;border-radius:2px;font-size:.7rem;text-decoration:none;letter-spacing:.06em;">
                                    ↗ Buka
                                </a>
                            </div>
                        </div>
                    @else
                        <div style="padding:2rem;text-align:center;background:var(--surface);border-radius:4px;border:1px dashed var(--border);">
                            <div style="font-size:2rem;margin-bottom:.5rem;opacity:.3;">🖼</div>
                            <div style="font-size:.82rem;color:var(--mid);">Belum ada bukti pembayaran diunggah.</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info transaksi --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Transaksi Terkait</div>
                    <a href="{{ route('admin.rentals.show', $payment->rental) }}" class="btn-outline">Lihat Transaksi</a>
                </div>
                <div style="padding:1.5rem;">
                    <div style="display:flex;gap:1rem;padding:1.25rem;background:var(--surface);border-radius:4px;border:1px solid var(--border);margin-bottom:1rem;">
                        <div style="width:3.5rem;height:3.5rem;border-radius:2px;background:var(--card);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:1.8rem;flex-shrink:0;">
                            {{ $payment->rental->equipment->category->icon ?? '🏄' }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-family:monospace;font-size:.82rem;color:var(--acid);">{{ $payment->rental->rental_code }}</div>
                            <div style="font-size:.9rem;font-weight:500;color:var(--white);margin:.2rem 0;">
                                {{ $payment->rental->equipment->name }}
                            </div>
                            <div style="font-size:.75rem;color:var(--mid);">
                                {{ $payment->rental->duration_days }} hari ·
                                {{ $payment->rental->start_date->format('d M') }} – {{ $payment->rental->end_date->format('d M Y') }}
                            </div>
                        </div>
                        @php
                            $rBadge = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','active'=>'badge-active','returned'=>'badge-returned','cancelled'=>'badge-cancelled'];
                        @endphp
                        <span class="badge {{ $rBadge[$payment->rental->status] ?? 'badge-returned' }}" style="align-self:flex-start;">
                            {{ $payment->rental->status_label }}
                        </span>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                        <div style="padding:.85rem;background:var(--surface);border-radius:2px;text-align:center;">
                            <div style="font-family:var(--font-display);font-size:1.3rem;color:var(--acid);">{{ $payment->rental->quantity }}</div>
                            <div style="font-size:.62rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;">Unit</div>
                        </div>
                        <div style="padding:.85rem;background:var(--surface);border-radius:2px;text-align:center;">
                            <div style="font-family:var(--font-display);font-size:1.3rem;color:var(--acid);">{{ $payment->rental->duration_days }}</div>
                            <div style="font-size:.62rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;">Hari</div>
                        </div>
                        <div style="padding:.85rem;background:var(--surface);border-radius:2px;text-align:center;">
                            <div style="font-family:var(--font-display);font-size:.9rem;color:var(--acid);">
                                Rp {{ number_format($payment->rental->total_price, 0, ',', '.') }}
                            </div>
                            <div style="font-size:.62rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;">Total Sewa</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── RIGHT SIDEBAR ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Actions --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Aksi</div>
                </div>
                <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.5rem;">

                    @if($payment->status === 'unpaid')
                        <form method="POST" action="{{ route('admin.payments.verify', $payment) }}"
                              onsubmit="return confirm('Verifikasi pembayaran ini sebagai lunas?')">
                            @csrf
                            <button type="submit" class="btn-add" style="width:100%;justify-content:center;padding:.7rem;">
                                ✅ Verifikasi Lunas
                            </button>
                        </form>
                    @endif

                    @if($payment->status === 'paid')
                        <form method="POST" action="{{ route('admin.payments.refund', $payment) }}"
                              onsubmit="return confirm('Tandai pembayaran ini sebagai refund?')">
                            @csrf
                            <button type="submit" class="btn-action btn-delete" style="width:100%;justify-content:center;padding:.65rem;font-size:.82rem;border-width:1px;">
                                ↩ Proses Refund
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.payments.edit', $payment) }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                        ✎ Edit Pembayaran
                    </a>

                    <a href="{{ route('admin.rentals.show', $payment->rental) }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                        📋 Lihat Transaksi
                    </a>
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
                            {{ strtoupper(substr($payment->rental->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:.9rem;font-weight:500;color:var(--white);">{{ $payment->rental->user->name }}</div>
                            <div style="font-size:.75rem;color:var(--mid);">{{ $payment->rental->user->email }}</div>
                        </div>
                    </div>
                    @if($payment->rental->user->phone)
                        <div style="font-size:.8rem;color:var(--muted);margin-bottom:.3rem;">📞 {{ $payment->rental->user->phone }}</div>
                    @endif
                    @if($payment->rental->user->address)
                        <div style="font-size:.8rem;color:var(--muted);">📍 {{ $payment->rental->user->address }}</div>
                    @endif
                </div>
            </div>

            {{-- Danger zone --}}
            @if($payment->status !== 'paid')
                <div class="table-card" style="border-color:rgba(248,113,113,.2);">
                    <div class="table-card-header" style="border-color:rgba(248,113,113,.2);">
                        <div class="table-card-title" style="color:#f87171;">Zona Berbahaya</div>
                    </div>
                    <div style="padding:1.25rem;">
                        <div style="font-size:.78rem;color:var(--mid);margin-bottom:.75rem;">
                            Hapus data pembayaran ini. Hanya bisa jika belum lunas.
                        </div>
                        <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}"
                              onsubmit="return confirm('Hapus data pembayaran ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-action btn-delete"
                                    style="width:100%;justify-content:center;padding:.6rem;font-size:.78rem;border-width:1px;">
                                Hapus Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <style>
        @media (max-width: 900px) {
            div[style*="grid-template-columns:1fr 320px"] { grid-template-columns:1fr!important; }
            div[style*="grid-template-columns:1fr 1fr 1fr"] { grid-template-columns:1fr 1fr!important; }
            div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns:1fr!important; }
        }
    </style>
</x-app-layout>
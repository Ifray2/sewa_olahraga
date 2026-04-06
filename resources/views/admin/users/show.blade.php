<x-app-layout>
    <x-slot name="heading">Detail Pengguna</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.users.index') }}">Pengguna</a>
        <span>/</span>
        <span>{{ $user->name }}</span>
    </x-slot>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">

        {{-- ── LEFT ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Profile header --}}
            <div class="table-card">
                <div style="padding:2rem;display:flex;align-items:center;gap:1.5rem;">
                    {{-- Avatar --}}
                    <div style="width:5rem;height:5rem;border-radius:50%;flex-shrink:0;
                        background:{{ $user->isAdmin() ? 'var(--acid)' : ($user->isPetugas() ? 'rgba(96,165,250,.15)' : 'var(--surface)') }};
                        color:{{ $user->isAdmin() ? 'var(--black)' : ($user->isPetugas() ? '#60a5fa' : 'var(--mid)') }};
                        border:2px solid {{ $user->isAdmin() ? 'var(--acid)' : ($user->isPetugas() ? 'rgba(96,165,250,.4)' : 'var(--border)') }};
                        display:flex;align-items:center;justify-content:center;
                        font-family:var(--font-display);font-size:2.5rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:.3rem;">
                            <div style="font-family:var(--font-display);font-size:1.6rem;text-transform:uppercase;color:var(--white);letter-spacing:.03em;">
                                {{ $user->name }}
                            </div>
                            @if($user->isAdmin())
                                <span class="badge badge-active">Admin</span>
                            @elseif($user->isPetugas())
                                <span class="badge badge-confirmed">Petugas</span>
                            @else
                                <span class="badge badge-returned">User</span>
                            @endif
                            @if($user->id === auth()->id())
                                <span style="font-size:.7rem;color:var(--acid);border:1px solid rgba(200,245,66,.3);padding:.1rem .5rem;border-radius:2px;">Anda</span>
                            @endif
                        </div>
                        <div style="font-size:.85rem;color:var(--mid);margin-bottom:.3rem;">{{ $user->email }}</div>
                        @if($user->phone)
                            <div style="font-size:.82rem;color:var(--muted);">📞 {{ $user->phone }}</div>
                        @endif
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-size:.65rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.3rem;">Bergabung</div>
                        <div style="font-size:.88rem;color:var(--white);">{{ $user->created_at->format('d M Y') }}</div>
                        <div style="font-size:.75rem;color:var(--mid);">{{ $user->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                @if($user->address)
                    <div style="padding:.85rem 2rem;border-top:1px solid var(--border);display:flex;align-items:flex-start;gap:.6rem;">
                        <span style="color:var(--mid);flex-shrink:0;">📍</span>
                        <div style="font-size:.82rem;color:var(--muted);line-height:1.6;">{{ $user->address }}</div>
                    </div>
                @endif
            </div>

            {{-- Stat bar --}}
            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;">
                @php
                    $stats = [
                        ['label'=>'Total Transaksi', 'val'=>$rentalStats['total'],     'color'=>'var(--white)'],
                        ['label'=>'Sedang Aktif',    'val'=>$rentalStats['active'],    'color'=>'var(--acid)'],
                        ['label'=>'Selesai',         'val'=>$rentalStats['returned'],  'color'=>'#9ca3af'],
                        ['label'=>'Dibatalkan',      'val'=>$rentalStats['cancelled'], 'color'=>'#f87171'],
                        ['label'=>'Total Ulasan',    'val'=>$user->reviews_count,      'color'=>'#fbbf24'],
                    ];
                @endphp
                @foreach($stats as $s)
                    <div style="padding:1rem;background:var(--card);border:1px solid var(--border);border-radius:4px;text-align:center;">
                        <div style="font-family:var(--font-display);font-size:1.8rem;color:{{ $s['color'] }};line-height:1;">{{ $s['val'] }}</div>
                        <div style="font-size:.6rem;letter-spacing:.1em;text-transform:uppercase;color:var(--mid);margin-top:.3rem;">{{ $s['label'] }}</div>
                    </div>
                @endforeach
            </div>

   

            {{-- Recent rentals --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Transaksi Terakhir</div>
                    <a href="{{ route('admin.rentals.index', ['search' => $user->name]) }}" class="btn-outline">
                        Semua Transaksi
                    </a>
                </div>
                @if($recentRentals->isEmpty())
                    <div style="padding:2rem;text-align:center;color:var(--mid);font-size:.82rem;">Belum ada transaksi.</div>
                @else
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Alat</th>
                                <th>Durasi</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentRentals as $rental)
                                @php
                                    $badgeMap = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','active'=>'badge-active','returned'=>'badge-returned','cancelled'=>'badge-cancelled'];
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.rentals.show', $rental) }}"
                                           style="font-family:monospace;font-size:.78rem;color:var(--acid);text-decoration:none;">
                                            {{ $rental->rental_code }}
                                        </a>
                                    </td>
                                    <td style="font-size:.82rem;color:var(--muted);max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $rental->equipment->name }}
                                    </td>
                                    <td style="font-size:.8rem;color:var(--mid);">{{ $rental->duration_days }} hari</td>
                                    <td style="font-size:.85rem;color:var(--acid);font-family:var(--font-display);">
                                        Rp {{ number_format($rental->total_price, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $badgeMap[$rental->status] ?? 'badge-returned' }}">
                                            {{ $rental->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Recent reviews --}}
            @if($recentReviews->isNotEmpty())
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Ulasan Terakhir</div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:0;">
                        @foreach($recentReviews as $review)
                            <div style="padding:1.1rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;gap:1rem;">
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:.82rem;font-weight:500;color:var(--white);margin-bottom:.2rem;">
                                        {{ $review->equipment->name }}
                                    </div>
                                    @if($review->comment)
                                        <div style="font-size:.78rem;color:var(--muted);line-height:1.5;font-style:italic;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                            "{{ $review->comment }}"
                                        </div>
                                    @endif
                                </div>
                                <div style="text-align:right;flex-shrink:0;">
                                    <div style="color:#fbbf24;font-size:.9rem;">
                                        @for($s=1;$s<=5;$s++)<span style="color:{{ $s<=$review->rating ? '#fbbf24':'var(--border)' }}">★</span>@endfor
                                    </div>
                                    <div style="font-size:.68rem;color:var(--mid);margin-top:.2rem;">{{ $review->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- ── RIGHT ── --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Actions --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Aksi</div>
                </div>
                <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.5rem;">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-add" style="justify-content:center;padding:.75rem;">
                        ✎ Edit Pengguna
                    </a>
                    <a href="{{ route('admin.rentals.index', ['search' => $user->email]) }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                        📋 Lihat Semua Transaksi
                    </a>
                </div>
            </div>

            {{-- Info --}}
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">Informasi Akun</div>
                </div>
                <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.7rem;">
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                        <span style="color:var(--mid);">ID</span>
                        <span style="color:var(--muted);">#{{ $user->id }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                        <span style="color:var(--mid);">Role</span>
                        @if($user->isAdmin())
                            <span class="badge badge-active">Admin</span>
                        @elseif($user->isPetugas())
                            <span class="badge badge-confirmed">Petugas</span>
                        @else
                            <span class="badge badge-returned">User</span>
                        @endif
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                        <span style="color:var(--mid);">Email Verified</span>
                        @if($user->email_verified_at)
                            <span style="color:var(--acid);font-size:.75rem;">✓ Verified</span>
                        @else
                            <span style="color:#fbbf24;font-size:.75rem;">Belum</span>
                        @endif
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                        <span style="color:var(--mid);">Bergabung</span>
                        <span style="color:var(--muted);">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                        <span style="color:var(--mid);">Diperbarui</span>
                        <span style="color:var(--muted);">{{ $user->updated_at->format('d M Y') }}</span>
                    </div>
                    @if($user->isStaff())
                        <div style="height:1px;background:var(--border);"></div>
                        <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:var(--mid);">Transaksi Ditangani</span>
                            <span style="color:var(--white);font-family:var(--font-display);">{{ $user->handled_rentals_count ?? 0 }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Reset password --}}
            @if($user->id !== auth()->id())
                <div class="table-card" style="border-color:rgba(251,191,36,.2);">
                    <div class="table-card-header" style="border-color:rgba(251,191,36,.2);">
                        <div class="table-card-title" style="color:#fbbf24;">Reset Password</div>
                    </div>
                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" style="padding:1.25rem;">
                        @csrf
                        <div class="form-group" style="margin-bottom:.75rem;">
                            <input type="password" name="password" class="form-control"
                                   placeholder="Password baru…" autocomplete="new-password">
                        </div>
                        <div class="form-group" style="margin-bottom:.75rem;">
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Konfirmasi password…" autocomplete="new-password">
                        </div>
                        @error('password') <div class="form-error" style="margin-bottom:.5rem;">{{ $message }}</div> @enderror
                        <button type="submit" class="btn-outline" style="width:100%;justify-content:center;padding:.65rem;color:#fbbf24;border-color:rgba(251,191,36,.35);"
                                onclick="return confirm('Reset password pengguna ini?')">
                            Reset Password
                        </button>
                    </form>
                </div>
            @endif

            {{-- Danger zone --}}
            @if($user->id !== auth()->id())
                <div class="table-card" style="border-color:rgba(248,113,113,.2);">
                    <div class="table-card-header" style="border-color:rgba(248,113,113,.2);">
                        <div class="table-card-title" style="color:#f87171;">Zona Berbahaya</div>
                    </div>
                    <div style="padding:1.25rem;">
                        <div style="font-size:.78rem;color:var(--mid);margin-bottom:.75rem;">
                            Hapus akun ini secara permanen beserta semua datanya.
                        </div>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }} secara permanen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-action btn-delete"
                                    style="width:100%;justify-content:center;padding:.6rem;font-size:.78rem;border-width:1px;">
                                Hapus Pengguna
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <style>
        @media (max-width: 1000px) {
            div[style*="grid-template-columns:1fr 300px"] { grid-template-columns:1fr!important; }
            div[style*="grid-template-columns:repeat(5,1fr)"] { grid-template-columns:repeat(3,1fr)!important; }
        }
        @media (max-width: 600px) {
            div[style*="grid-template-columns:repeat(5,1fr)"] { grid-template-columns:repeat(2,1fr)!important; }
        }
    </style>
</x-app-layout>
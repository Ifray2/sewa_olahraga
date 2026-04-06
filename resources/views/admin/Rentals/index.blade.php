<x-app-layout>
    <x-slot name="heading">Transaksi Sewa</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <span>Transaksi Sewa</span>
    </x-slot>

    {{-- Status tabs --}}
    <div style="display:flex;gap:1px;background:var(--border);border:1px solid var(--border);border-radius:4px;overflow:hidden;margin-bottom:1.5rem;">
        @php
            $tabs = [
                ''          => ['label' => 'Semua',      'count' => $summary['all'],       'color' => 'var(--white)'],
                'pending'   => ['label' => 'Menunggu',   'count' => $summary['pending'],   'color' => '#fbbf24'],
                'confirmed' => ['label' => 'Dikonfirmasi','count'=> $summary['confirmed'], 'color' => '#60a5fa'],
                'active'    => ['label' => 'Aktif',      'count' => $summary['active'],    'color' => 'var(--acid)'],
                'returned'  => ['label' => 'Dikembalikan','count'=> $summary['returned'],  'color' => '#9ca3af'],
                'cancelled' => ['label' => 'Dibatalkan', 'count' => $summary['cancelled'], 'color' => '#f87171'],
            ];
            $currentStatus = request('status', '');
        @endphp
        @foreach($tabs as $val => $tab)
            <a href="{{ route('admin.rentals.index', array_merge(request()->except('page'), ['status' => $val ?: null])) }}"
               style="flex:1;padding:.8rem .5rem;text-align:center;text-decoration:none;background:{{ $currentStatus === $val ? 'rgba(200,245,66,.07)' : 'var(--card)' }};border-bottom:2px solid {{ $currentStatus === $val ? 'var(--acid)' : 'transparent' }};transition:background .15s;">
                <div style="font-family:var(--font-display);font-size:1.3rem;color:{{ $tab['color'] }};line-height:1;">
                    {{ $tab['count'] }}
                </div>
                <div style="font-size:.65rem;letter-spacing:.1em;text-transform:uppercase;color:{{ $currentStatus === $val ? 'var(--acid)' : 'var(--mid)' }};margin-top:.2rem;">
                    {{ $tab['label'] }}
                </div>
            </a>
        @endforeach
    </div>

    {{-- Table card --}}
     <div class="table-responsive">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Daftar Transaksi</div>
            <div class="table-card-actions">
                <form method="GET" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="search-input" placeholder="Kode / nama penyewa…">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="search-input" style="width:140px;" title="Dari tanggal">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="search-input" style="width:140px;" title="Sampai tanggal">
                    <button type="submit" class="btn-outline">Cari</button>
                    @if(request('search') || request('date_from') || request('date_to'))
                        <a href="{{ route('admin.rentals.index', request('status') ? ['status' => request('status')] : []) }}" class="btn-outline">Reset</a>
                    @endif
                </form>
                <a href="{{ route('admin.rentals.create') }}" class="btn-add">+ Buat Transaksi</a>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Penyewa</th>
                    <th>Alat</th>
                    <th>Durasi</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $rental)
                    <tr>
                        <td>
                            <a href="{{ route('admin.rentals.show', $rental) }}"
                               style="font-family:monospace;font-size:.8rem;color:var(--acid);text-decoration:none;white-space:nowrap;">
                                {{ $rental->rental_code }}
                            </a>
                        </td>
                        <td>
                            <div class="td-name">{{ $rental->user->name }}</div>
                            <div style="font-size:.72rem;color:var(--mid);">{{ $rental->user->email }}</div>
                        </td>
                        <td>
                            <div style="font-size:.82rem;color:var(--white);max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $rental->equipment->name }}
                            </div>
                            <div style="font-size:.7rem;color:var(--mid);">
                                {{ $rental->quantity }}× · {{ $rental->equipment->category->name }}
                            </div>
                        </td>
                        <td style="white-space:nowrap;">
                            <div style="font-size:.82rem;color:var(--white);">{{ $rental->duration_days }} hari</div>
                            <div style="font-size:.7rem;color:var(--mid);">
                                {{ $rental->start_date->format('d/m') }} – {{ $rental->end_date->format('d/m/Y') }}
                            </div>
                        </td>
                        <td style="font-family:var(--font-display);font-size:1.05rem;color:var(--acid);white-space:nowrap;">
                            Rp {{ number_format($rental->total_price, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($rental->payment)
                                @if($rental->payment->status === 'paid')
                                    <span class="badge badge-active">Lunas</span>
                                @elseif($rental->payment->status === 'refunded')
                                    <span class="badge badge-returned">Refund</span>
                                @else
                                    <span class="badge badge-pending">Belum Bayar</span>
                                @endif
                            @else
                                <span class="badge badge-cancelled">—</span>
                            @endif
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
                        <td style="font-size:.75rem;color:var(--mid);white-space:nowrap;">
                            {{ $rental->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div class="action-group" style="flex-wrap:wrap;">
                                <a href="{{ route('admin.rentals.show', $rental) }}" class="btn-action btn-view">Lihat</a>

                                @if($rental->status === 'pending')
                                    <form method="POST" action="{{ route('admin.rentals.confirm', $rental) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-action"
                                            style="border-color:rgba(96,165,250,.4);color:#60a5fa;background:transparent;cursor:pointer;font-family:var(--font-body);font-size:.7rem;letter-spacing:.05em;">
                                            Konfirmasi
                                        </button>
                                    </form>
                                @endif

                                @if($rental->status === 'confirmed')
                                    <form method="POST" action="{{ route('admin.rentals.activate', $rental) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-action"
                                            style="border-color:rgba(200,245,66,.4);color:var(--acid);background:transparent;cursor:pointer;font-family:var(--font-body);font-size:.7rem;letter-spacing:.05em;">
                                            Aktifkan
                                        </button>
                                    </form>
                                @endif

                                @if($rental->status === 'active')
                                    <form method="POST" action="{{ route('admin.rentals.return', $rental) }}" style="display:inline;"
                                          onsubmit="return confirm('Tandai alat sudah dikembalikan?')">
                                        @csrf
                                        <button type="submit" class="btn-action"
                                            style="border-color:rgba(156,163,175,.4);color:#9ca3af;background:transparent;cursor:pointer;font-family:var(--font-body);font-size:.7rem;letter-spacing:.05em;">
                                            Kembalikan
                                        </button>
                                    </form>
                                @endif

                                @if(!in_array($rental->status, ['returned','cancelled']))
                                    <form method="POST" action="{{ route('admin.rentals.cancel', $rental) }}" style="display:inline;"
                                          onsubmit="return confirm('Batalkan transaksi ini?')">
                                        @csrf
                                        <button type="submit" class="btn-action btn-delete">Batal</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:3rem;color:var(--mid);">
                            <div style="font-size:2rem;margin-bottom:.75rem;">📋</div>
                            <div>Tidak ada transaksi ditemukan.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-footer">
            <div class="table-info">
                Menampilkan {{ $rentals->firstItem() }}–{{ $rentals->lastItem() }} dari {{ $rentals->total() }} transaksi
            </div>
            <div class="pagination">
                @if($rentals->onFirstPage())
                    <span class="page-btn" style="opacity:.3;">‹</span>
                @else
                    <a href="{{ $rentals->previousPageUrl() }}" class="page-btn">‹</a>
                @endif
                @foreach($rentals->getUrlRange(1, $rentals->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="page-btn {{ $page == $rentals->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach
                @if($rentals->hasMorePages())
                    <a href="{{ $rentals->nextPageUrl() }}" class="page-btn">›</a>
                @else
                    <span class="page-btn" style="opacity:.3;">›</span>
                @endif
            </div>
        </div>
    </div>
     </div>
</x-app-layout>
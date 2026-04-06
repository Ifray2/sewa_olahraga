@extends('layouts.petugas')

@section('content')

    {{-- Revenue cards --}}
    <div class="stats-grid mb-6">
        <div class="stat-card">
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-value acid" style="font-size:1.8rem;">
                Rp {{ number_format($summary['total_all'], 0, ',', '.') }}
            </div>
            <div class="stat-bg-icon">💰</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Sudah Lunas</div>
            <div class="stat-value acid" style="font-size:1.8rem;">
                Rp {{ number_format($summary['total_paid'], 0, ',', '.') }}
            </div>
            <div class="stat-bg-icon">✓</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Belum Bayar</div>
            <div class="stat-value" style="color:#fbbf24;">{{ $summary['count_unpaid'] }}</div>
            <div class="stat-bg-icon">⏳</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Refunded</div>
            <div class="stat-value" style="color:#f87171;">{{ $summary['count_refunded'] }}</div>
            <div class="stat-bg-icon">↩</div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Daftar Pembayaran</div>
            <div class="table-card-actions">
                <form method="GET" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="search-input" placeholder="Kode transaksi / nama…">

                    <select name="status" class="search-input" style="width:140px;">
                        <option value="">Semua Status</option>
                        <option value="unpaid"   {{ request('status')==='unpaid'   ? 'selected':'' }}>Belum Bayar</option>
                        <option value="paid"     {{ request('status')==='paid'     ? 'selected':'' }}>Lunas</option>
                        <option value="refunded" {{ request('status')==='refunded' ? 'selected':'' }}>Refunded</option>
                    </select>

                    <select name="method" class="search-input" style="width:140px;">
                        <option value="">Semua Metode</option>
                        <option value="transfer" {{ request('method')==='transfer' ? 'selected':'' }}>Transfer Bank</option>
                        <option value="cash"     {{ request('method')==='cash'     ? 'selected':'' }}>Tunai</option>
                        <option value="ewallet"  {{ request('method')==='ewallet'  ? 'selected':'' }}>E-Wallet</option>
                    </select>

                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="search-input" style="width:140px;" title="Dari tanggal">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="search-input" style="width:140px;" title="Sampai tanggal">

                    <button type="submit" class="btn-outline">Cari</button>
                    @if(request()->hasAny(['search','status','method','date_from','date_to']))
                        <a href="{{ route('petugas.payments.index') }}" class="btn-outline">Reset</a>
                    @endif
                </form>
                <a href="{{ route('petugas.payments.create') }}" class="btn-add">+ Tambah Pembayaran</a>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode Transaksi</th>
                    <th>Penyewa</th>
                    <th>Alat</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Dibayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $i => $payment)
                    <tr>
                        <td>{{ $payments->firstItem() + $i }}</td>
                        <td>
                            <a href="{{ route('petugas.rentals.show', $payment->rental) }}"
                               style="font-family:monospace;font-size:.8rem;color:var(--acid);text-decoration:none;white-space:nowrap;">
                                {{ $payment->rental->rental_code }}
                            </a>
                        </td>
                        <td>
                            <div class="td-name">{{ $payment->rental->user->name }}</div>
                            <div style="font-size:.72rem;color:var(--mid);">{{ $payment->rental->user->email }}</div>
                        </td>
                        <td style="font-size:.8rem;color:var(--muted);max-width:150px;">
                            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $payment->rental->equipment->name }}
                            </div>
                        </td>
                        <td>
                            @php
                                $methodIcons = ['transfer'=>'🏦','cash'=>'💵','ewallet'=>'📱'];
                            @endphp
                            <span style="font-size:.82rem;color:var(--muted);">
                                {{ $methodIcons[$payment->method] ?? '💳' }} {{ $payment->method_label }}
                            </span>
                        </td>
                        <td style="font-family:var(--font-display);font-size:1.05rem;color:var(--acid);white-space:nowrap;">
                            {{ $payment->formatted_amount }}
                        </td>
                        <td>
                            @if($payment->proof_image)
                                <a href="{{ Storage::url($payment->proof_image) }}" target="_blank"
                                   style="display:inline-block;width:2.5rem;height:2.5rem;border-radius:2px;overflow:hidden;border:1px solid var(--border);">
                                    <img src="{{ Storage::url($payment->proof_image) }}" alt="Bukti"
                                         style="width:100%;height:100%;object-fit:cover;">
                                </a>
                            @else
                                <span style="color:var(--mid);font-size:.78rem;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->status === 'paid')
                                <span class="badge badge-active">Lunas</span>
                            @elseif($payment->status === 'refunded')
                                <span class="badge badge-returned">Refunded</span>
                            @else
                                <span class="badge badge-pending">Belum Bayar</span>
                            @endif
                        </td>
                        <td style="font-size:.75rem;color:var(--mid);white-space:nowrap;">
                            {{ $payment->paid_at ? $payment->paid_at->format('d M Y') : '—' }}
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('petugas.payments.show', $payment) }}" class="btn-action btn-view">Lihat</a>
                                <a href="{{ route('petugas.payments.edit', $payment) }}" class="btn-action btn-edit">Edit</a>

                                @if($payment->status === 'unpaid')
                                    <form method="POST" action="{{ route('petugas.payments.verify', $payment) }}" style="display:inline;"
                                          onsubmit="return confirm('Verifikasi pembayaran ini sebagai lunas?')">
                                        @csrf
                                        <button type="submit" class="btn-action"
                                            style="border-color:rgba(200,245,66,.4);color:var(--acid);background:transparent;cursor:pointer;font-family:var(--font-body);font-size:.7rem;letter-spacing:.05em;">
                                            Verifikasi
                                        </button>
                                    </form>
                                @endif

                                @if($payment->status === 'paid')
                                    <form method="POST" action="{{ route('petugas.payments.refund', $payment) }}" style="display:inline;"
                                          onsubmit="return confirm('Tandai pembayaran ini sebagai refund?')">
                                        @csrf
                                        <button type="submit" class="btn-action"
                                            style="border-color:rgba(248,113,113,.3);color:#f87171;background:transparent;cursor:pointer;font-family:var(--font-body);font-size:.7rem;letter-spacing:.05em;">
                                            Refund
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:3rem;color:var(--mid);">
                            <div style="font-size:2rem;margin-bottom:.75rem;">💳</div>
                            <div>Belum ada data pembayaran.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-footer">
            <div class="table-info">
                Menampilkan {{ $payments->firstItem() }}–{{ $payments->lastItem() }} dari {{ $payments->total() }} pembayaran
            </div>
            <div class="pagination">
                @if($payments->onFirstPage())
                    <span class="page-btn" style="opacity:.3;">‹</span>
                @else
                    <a href="{{ $payments->previousPageUrl() }}" class="page-btn">‹</a>
                @endif
                @foreach($payments->getUrlRange(1, $payments->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="page-btn {{ $page == $payments->currentPage() ? 'active':'' }}">{{ $page }}</a>
                @endforeach
                @if($payments->hasMorePages())
                    <a href="{{ $payments->nextPageUrl() }}" class="page-btn">›</a>
                @else
                    <span class="page-btn" style="opacity:.3;">›</span>
                @endif
            </div>
        </div>
    </div>
@endsection
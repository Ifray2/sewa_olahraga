@extends('layouts.home')

@section('title', 'Transaksi Saya — SportRent')
@section('nav-class', 'always-solid')

@push('styles')
<style>
    .rentals-page { padding-top: 70px; min-height: 100vh; }

    /* Header */
    .rentals-header {
        padding: 3rem 3rem 2rem;
        border-bottom: 1px solid var(--border);
        position: relative; overflow: hidden;
    }
    .rentals-header::before {
        content: 'SEWA';
        position: absolute; right: 2rem; top: -1rem;
        font-family: var(--font-display); font-size: 9rem;
        color: rgba(200,245,66,.04); line-height: 1;
        pointer-events: none; user-select: none;
    }
    .rentals-eyebrow { font-size: .65rem; font-weight: 500; letter-spacing: .22em; text-transform: uppercase; color: var(--acid); margin-bottom: .6rem; display: flex; align-items: center; gap: .5rem; }
    .rentals-eyebrow::before { content: ''; display: block; width: 1.5rem; height: 1px; background: var(--acid); }
    .rentals-title { font-family: var(--font-display); font-size: clamp(2.2rem,4vw,3.5rem); line-height: .9; text-transform: uppercase; }

    /* Stats row */
    .rental-stats-row {
        display: flex; gap: 1px; background: var(--border);
        border: 1px solid var(--border); border-radius: 2px;
        overflow: hidden; margin: 0 3rem 0;
    }
    .rental-stat {
        flex: 1; padding: 1.1rem 1.4rem;
        background: var(--card); text-align: center;
        text-decoration: none; transition: background .15s;
        border-bottom: 2px solid transparent;
        cursor: pointer;
    }
    .rental-stat:hover { background: rgba(200,245,66,.04); }
    .rental-stat.active { background: rgba(200,245,66,.06); border-bottom-color: var(--acid); }
    .rental-stat-num { font-family: var(--font-display); font-size: 1.6rem; line-height: 1; }
    .rental-stat-num.all     { color: var(--white); }
    .rental-stat-num.pending { color: #fbbf24; }
    .rental-stat-num.active  { color: var(--acid); }
    .rental-stat-num.done    { color: #9ca3af; }
    .rental-stat-num.cancel  { color: #f87171; }
    .rental-stat-lbl { font-size: .6rem; letter-spacing: .12em; text-transform: uppercase; color: var(--mid); margin-top: .2rem; }

    /* Body */
    .rentals-body { padding: 2rem 3rem 6rem; max-width: 900px; margin: 0 auto; }

    /* Empty */
    .rentals-empty { text-align: center; padding: 5rem 2rem; }
    .rentals-empty-ico { font-size: 3.5rem; margin-bottom: 1rem; opacity: .25; }
    .rentals-empty-title { font-family: var(--font-display); font-size: 2rem; text-transform: uppercase; color: var(--muted); margin-bottom: .75rem; }
    .rentals-empty-desc { font-size: .88rem; color: var(--mid); margin-bottom: 2rem; line-height: 1.7; }
    .btn-start-rent { display: inline-flex; align-items: center; gap: .5rem; padding: .85rem 2rem; background: var(--acid); color: var(--black); border-radius: 2px; font-family: var(--font-body); font-size: .85rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; text-decoration: none; transition: transform .15s; }
    .btn-start-rent:hover { transform: translateY(-2px); }

    /* Card */
    .rental-card {
        background: var(--card); border: 1px solid var(--border); border-radius: 2px;
        margin-bottom: 1rem; overflow: hidden;
        transition: border-color .2s;
    }
    .rental-card:hover { border-color: rgba(200,245,66,.15); }

    .rental-card-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border);
        flex-wrap: wrap; gap: .75rem;
    }
    .rental-code {
        font-family: monospace; font-size: .82rem; color: var(--acid);
        letter-spacing: .05em;
    }
    .rental-date { font-size: .72rem; color: var(--mid); margin-top: .15rem; }
    .rental-status-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .62rem; font-weight: 600; letter-spacing: .1em;
        text-transform: uppercase; padding: .3rem .75rem; border-radius: 1px;
    }
    .rs-pending   { background: rgba(251,191,36,.12); color: #fbbf24; border: 1px solid rgba(251,191,36,.25); }
    .rs-confirmed { background: rgba(96,165,250,.12);  color: #60a5fa; border: 1px solid rgba(96,165,250,.25); }
    .rs-active    { background: rgba(200,245,66,.1);   color: var(--acid); border: 1px solid rgba(200,245,66,.25); }
    .rs-returned  { background: rgba(156,163,175,.1);  color: #9ca3af; border: 1px solid rgba(156,163,175,.2); }
    .rs-cancelled { background: rgba(248,113,113,.1);  color: #f87171; border: 1px solid rgba(248,113,113,.25); }

    .rental-card-body { display: flex; gap: 0; }

    .rental-eq-img {
        width: 110px; flex-shrink: 0;
        background: #1a1a1a; overflow: hidden;
    }
    .rental-eq-img img { width: 100%; height: 100%; object-fit: cover; display: block; filter: brightness(.8); }
    .rental-eq-img-ph { width: 100%; height: 100%; min-height: 90px; display: flex; align-items: center; justify-content: center; font-size: 2rem; }

    .rental-card-info { flex: 1; padding: 1.1rem 1.4rem; display: flex; flex-direction: column; gap: .5rem; }
    .rental-eq-cat { font-size: .6rem; letter-spacing: .14em; text-transform: uppercase; color: var(--mid); }
    .rental-eq-name { font-family: var(--font-display); font-size: 1.15rem; text-transform: uppercase; color: var(--white); line-height: 1.1; }
    .rental-meta-row { display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: .25rem; }
    .rental-meta-item { font-size: .75rem; color: var(--muted); }
    .rental-meta-item strong { color: var(--white); font-weight: 500; }

    .rental-card-price {
        padding: 1.1rem 1.5rem; border-left: 1px solid var(--border);
        display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between;
        flex-shrink: 0; min-width: 160px; gap: .75rem;
    }
    .rental-total-lbl { font-size: .6rem; letter-spacing: .14em; text-transform: uppercase; color: var(--mid); }
    .rental-total-val { font-family: var(--font-display); font-size: 1.6rem; color: var(--acid); line-height: 1; }
    .rental-pay-status { font-size: .65rem; color: var(--mid); }
    .rental-pay-status.paid { color: var(--acid); }

    .rental-card-foot {
        padding: .85rem 1.5rem; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: .5rem;
        background: rgba(255,255,255,.015);
    }
    .rental-actions { display: flex; gap: .5rem; flex-wrap: wrap; }

    .btn-action-sm {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .42rem .9rem; border-radius: 1px; font-family: var(--font-body);
        font-size: .72rem; font-weight: 500; letter-spacing: .06em;
        text-transform: uppercase; text-decoration: none; cursor: pointer;
        transition: all .15s; border: 1px solid transparent;
    }
    .btn-detail  { background: rgba(200,245,66,.1); border-color: rgba(200,245,66,.25); color: var(--acid); }
    .btn-detail:hover { background: rgba(200,245,66,.18); }
    .btn-pay     { background: rgba(96,165,250,.12); border-color: rgba(96,165,250,.3); color: #60a5fa; }
    .btn-pay:hover { background: rgba(96,165,250,.2); }
    .btn-cancel  { background: rgba(248,113,113,.08); border-color: rgba(248,113,113,.25); color: #f87171; }
    .btn-cancel:hover { background: rgba(248,113,113,.15); }
    .btn-review  { background: rgba(251,191,36,.1); border-color: rgba(251,191,36,.3); color: #fbbf24; }
    .btn-review:hover { background: rgba(251,191,36,.2); }

    .rental-progress { display: flex; align-items: center; gap: .4rem; font-size: .68rem; color: var(--mid); }
    .progress-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--border); }
    .progress-dot.done { background: var(--acid); }
    .progress-dot.current { background: #fbbf24; box-shadow: 0 0 0 2px rgba(251,191,36,.25); }

    /* Pagination */
    .pag { display: flex; align-items: center; justify-content: center; gap: .3rem; margin-top: 2.5rem; }
    .pag-a { width: 2.2rem; height: 2.2rem; border-radius: 1px; display: flex; align-items: center; justify-content: center; font-size: .78rem; text-decoration: none; border: 1px solid var(--border); color: var(--mid); background: transparent; transition: all .15s; }
    .pag-a:hover { border-color: var(--acid); color: var(--acid); }
    .pag-a.on { background: var(--acid); border-color: var(--acid); color: var(--black); font-weight: 700; }
    .pag-a.off { opacity: .3; pointer-events: none; }

    /* Responsive */
    @media(max-width: 768px) {
        .rentals-header { padding: 2rem 1.5rem 1.5rem; }
        .rental-stats-row { margin: 0 1.5rem 0; }
        .rentals-body { padding: 1.5rem 1.5rem 5rem; }
        .rental-card-body { flex-direction: column; }
        .rental-eq-img { width: 100%; height: 120px; }
        .rental-card-price { border-left: none; border-top: 1px solid var(--border); flex-direction: row; align-items: center; min-width: auto; padding: .85rem 1.4rem; }
        .rental-stat-lbl { display: none; }
    }
</style>
@endpush

@section('content')
<div class="rentals-page">

    {{-- Header --}}
    <div class="rentals-header">
        <div class="rentals-eyebrow">Akun Saya</div>
        <h1 class="rentals-title">Transaksi<br><span style="color:var(--acid);">Saya</span></h1>
    </div>

    {{-- Status tabs --}}
    @php
        $tabs = [
            ''          => ['num' => $summary['all'],       'class' => 'all',     'label' => 'Semua'],
            'pending'   => ['num' => $summary['pending'],   'class' => 'pending', 'label' => 'Menunggu'],
            'active'    => ['num' => $summary['active'],    'class' => 'active',  'label' => 'Aktif'],
            'returned'  => ['num' => $summary['returned'],  'class' => 'done',    'label' => 'Selesai'],
            'cancelled' => ['num' => $summary['cancelled'], 'class' => 'cancel',  'label' => 'Batal'],
        ];
        $cur = request('status', '');
    @endphp
    <div class="rental-stats-row">
        @foreach($tabs as $val => $tab)
            <a href="{{ route('rentals.index', $val ? ['status' => $val] : []) }}"
               class="rental-stat {{ $cur === $val ? 'active' : '' }}">
                <div class="rental-stat-num {{ $tab['class'] }}">{{ $tab['num'] }}</div>
                <div class="rental-stat-lbl">{{ $tab['label'] }}</div>
            </a>
        @endforeach
    </div>

    <div class="rentals-body">

        {{-- New rental CTA --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.75rem;flex-wrap:wrap;gap:.75rem;">
            <div style="font-size:.78rem;color:var(--mid);">
                {{ $rentals->total() }} transaksi
                @if($cur) <span style="color:var(--acid);">· {{ $tabs[$cur]['label'] }}</span> @endif
            </div>
            <a href="{{ route('catalog') }}" class="btn-start-rent">
                + Sewa Alat Baru
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M7 2l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>

        @forelse($rentals as $rental)
            @php
                $statusMap = [
                    'pending'   => ['class' => 'rs-pending',   'label' => '⏳ Menunggu Konfirmasi', 'step' => 0],
                    'confirmed' => ['class' => 'rs-confirmed', 'label' => '✓ Dikonfirmasi',         'step' => 1],
                    'active'    => ['class' => 'rs-active',    'label' => '● Sedang Disewa',         'step' => 2],
                    'returned'  => ['class' => 'rs-returned',  'label' => '✓ Selesai',               'step' => 3],
                    'cancelled' => ['class' => 'rs-cancelled', 'label' => '✕ Dibatalkan',            'step' => -1],
                ];
                $sm = $statusMap[$rental->status] ?? $statusMap['pending'];
            @endphp

            <div class="rental-card">
                {{-- Head --}}
                <div class="rental-card-head">
                    <div>
                        <div class="rental-code">{{ $rental->rental_code }}</div>
                        <div class="rental-date">Dibuat {{ $rental->created_at->diffForHumans() }} · {{ $rental->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <span class="rental-status-badge {{ $sm['class'] }}">{{ $sm['label'] }}</span>
                </div>

                {{-- Body --}}
                <div class="rental-card-body">
                    {{-- Foto alat --}}
                    <div class="rental-eq-img">
                        @if($rental->equipment->image)
                            <img src="{{ Storage::url($rental->equipment->image) }}" alt="{{ $rental->equipment->name }}" loading="lazy">
                        @else
                            <div class="rental-eq-img-ph">{{ $rental->equipment->category->icon ?? '🏅' }}</div>
                        @endif
                    </div>

                    {{-- Info alat --}}
                    <div class="rental-card-info">
                        <div>
                            <div class="rental-eq-cat">{{ $rental->equipment->category->name }}</div>
                            <div class="rental-eq-name">{{ $rental->equipment->name }}</div>
                        </div>
                        <div class="rental-meta-row">
                            <div class="rental-meta-item">
                                Jumlah: <strong>{{ $rental->quantity }} unit</strong>
                            </div>
                            <div class="rental-meta-item">
                                Mulai: <strong>{{ $rental->start_date->format('d M Y') }}</strong>
                            </div>
                            <div class="rental-meta-item">
                                Selesai: <strong>{{ $rental->end_date->format('d M Y') }}</strong>
                            </div>
                            <div class="rental-meta-item">
                                Durasi: <strong>{{ $rental->duration_days }} hari</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Harga --}}
                    <div class="rental-card-price">
                        <div>
                            <div class="rental-total-lbl">Total</div>
                            <div class="rental-total-val">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</div>
                        </div>
                        <div class="rental-pay-status {{ $rental->payment?->status === 'paid' ? 'paid' : '' }}">
                            @if($rental->payment?->status === 'paid') ✓ Lunas
                            @elseif($rental->payment) ⏳ Verifikasi
                            @else — Belum Bayar
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="rental-card-foot">
                    {{-- Progress dots --}}
                    @if($rental->status !== 'cancelled')
                        <div class="rental-progress">
                            @foreach(['Menunggu','Dikonfirmasi','Aktif','Selesai'] as $pi => $pl)
                                <div class="progress-dot {{ $sm['step'] > $pi ? 'done' : ($sm['step'] === $pi ? 'current' : '') }}"></div>
                                <span style="font-size:.6rem;{{ $sm['step'] === $pi ? 'color:var(--white)' : '' }}">{{ $pl }}</span>
                                @if(!$loop->last) <div style="width:.75rem;height:1px;background:var(--border);"></div> @endif
                            @endforeach
                        </div>
                    @else
                        <div style="font-size:.7rem;color:#f87171;">Transaksi dibatalkan</div>
                    @endif

                    {{-- Action buttons --}}
                    <div class="rental-actions">
                        <a href="{{ route('rentals.show', $rental) }}" class="btn-action-sm btn-detail">Detail</a>

                        @if(in_array($rental->status, ['pending','confirmed']) && (!$rental->payment || $rental->payment->status !== 'paid'))
                            <a href="{{ route('rentals.show', $rental) }}#bayar" class="btn-action-sm btn-pay">Bayar</a>
                        @endif

                        @if($rental->status === 'returned' && !$rental->review)
                            <a href="{{ route('rentals.show', $rental) }}#ulasan" class="btn-action-sm btn-review">Beri Ulasan</a>
                        @endif

                        @if(in_array($rental->status, ['pending']))
                            <form method="POST" action="{{ route('rentals.cancel', $rental) }}" style="display:inline;"
                                  onsubmit="return confirm('Batalkan transaksi {{ $rental->rental_code }}?')">
                                @csrf
                                <button type="submit" class="btn-action-sm btn-cancel">Batalkan</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rentals-empty">
                <div class="rentals-empty-ico">📋</div>
                <div class="rentals-empty-title">
                    @if($cur) Tidak Ada Transaksi @else Belum Ada Transaksi @endif
                </div>
                <p class="rentals-empty-desc">
                    @if($cur)
                        Tidak ada transaksi dengan status "{{ $tabs[$cur]['label'] }}".
                    @else
                        Kamu belum pernah menyewa alat olahraga.<br>
                        Yuk mulai petualanganmu sekarang!
                    @endif
                </p>
                <a href="{{ route('catalog') }}" class="btn-start-rent">
                    Jelajahi Katalog
                    <svg width="13" height="13" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M7 2l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($rentals->hasPages())
            <div class="pag">
                @if($rentals->onFirstPage())
                    <span class="pag-a off">‹</span>
                @else
                    <a href="{{ $rentals->previousPageUrl() }}" class="pag-a">‹</a>
                @endif
                @foreach($rentals->getUrlRange(1, $rentals->lastPage()) as $p => $url)
                    <a href="{{ $url }}" class="pag-a {{ $p == $rentals->currentPage() ? 'on' : '' }}">{{ $p }}</a>
                @endforeach
                @if($rentals->hasMorePages())
                    <a href="{{ $rentals->nextPageUrl() }}" class="pag-a">›</a>
                @else
                    <span class="pag-a off">›</span>
                @endif
            </div>
        @endif

    </div>
</div>
@endsection
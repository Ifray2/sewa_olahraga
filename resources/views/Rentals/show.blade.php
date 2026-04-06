@extends('layouts.home')

@section('title', $rental->rental_code . ' — SportRent')
@section('nav-class', 'always-solid')

@section('nav-center')
    <a href="{{ route('rentals.index') }}" class="nav-back">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M12 7H2M7 12L2 7l5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Transaksi Saya
    </a>
@endsection

@push('styles')
<style>
    .show-page { padding-top: 70px; min-height: 100vh; }
    .show-inner { max-width: 960px; margin: 0 auto; padding: 2.5rem 2rem 6rem; }

    /* Flash */
    .flash { padding: 1rem 1.25rem; border-radius: 2px; font-size: .85rem; margin-bottom: 1.75rem; display: flex; align-items: center; gap: .75rem; }
    .flash-success { background: rgba(200,245,66,.08); border: 1px solid rgba(200,245,66,.25); color: var(--acid); }
    .flash-error   { background: rgba(248,113,113,.08); border: 1px solid rgba(248,113,113,.25); color: #f87171; }

    /* Status banner */
    .status-banner {
        padding: 1.25rem 1.5rem; border-radius: 2px;
        display: flex; align-items: center; gap: 1.25rem;
        margin-bottom: 2rem; flex-wrap: wrap;
    }
    .sb-pending   { background: rgba(251,191,36,.07); border: 1px solid rgba(251,191,36,.2); }
    .sb-confirmed { background: rgba(96,165,250,.07); border: 1px solid rgba(96,165,250,.2); }
    .sb-active    { background: rgba(200,245,66,.07); border: 1px solid rgba(200,245,66,.2); }
    .sb-returned  { background: rgba(156,163,175,.07); border: 1px solid rgba(156,163,175,.2); }
    .sb-cancelled { background: rgba(248,113,113,.07); border: 1px solid rgba(248,113,113,.2); }
    .sb-icon { font-size: 1.75rem; flex-shrink: 0; }
    .sb-title { font-family: var(--font-display); font-size: 1.3rem; text-transform: uppercase; letter-spacing: .04em; }
    .sb-desc  { font-size: .8rem; color: var(--mid); margin-top: .15rem; }
    .sb-pending   .sb-title { color: #fbbf24; }
    .sb-confirmed .sb-title { color: #60a5fa; }
    .sb-active    .sb-title { color: var(--acid); }
    .sb-returned  .sb-title { color: #9ca3af; }
    .sb-cancelled .sb-title { color: #f87171; }
    .sb-code { margin-left: auto; font-family: monospace; font-size: .9rem; color: var(--mid); flex-shrink: 0; }

    /* Progress bar */
    .progress-bar {
        display: flex; align-items: center; margin-bottom: 2.5rem;
        background: var(--card); border: 1px solid var(--border); border-radius: 2px;
        padding: 1.1rem 1.5rem; gap: 0;
    }
    .pb-step { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; }
    .pb-step:not(:last-child)::after {
        content: ''; position: absolute; top: .75rem; left: 50%; right: -50%;
        height: 1px; background: var(--border); z-index: 0;
    }
    .pb-step.done::after   { background: var(--acid); }
    .pb-dot {
        width: 1.5rem; height: 1.5rem; border-radius: 50%;
        border: 2px solid var(--border); background: var(--black);
        display: flex; align-items: center; justify-content: center;
        font-size: .65rem; color: var(--mid); z-index: 1; position: relative;
        transition: all .3s;
    }
    .pb-step.done   .pb-dot { border-color: var(--acid); background: var(--acid); color: var(--black); font-weight: 700; }
    .pb-step.current .pb-dot { border-color: #fbbf24; background: rgba(251,191,36,.15); color: #fbbf24; }
    .pb-label { font-size: .6rem; letter-spacing: .1em; text-transform: uppercase; margin-top: .4rem; color: var(--mid); text-align: center; }
    .pb-step.done    .pb-label { color: var(--acid); }
    .pb-step.current .pb-label { color: #fbbf24; }

    /* Two-col layout */
    .show-grid { display: grid; grid-template-columns: 1fr 300px; gap: 1.75rem; align-items: start; }

    /* Card */
    .detail-card { background: var(--card); border: 1px solid var(--border); border-radius: 2px; margin-bottom: 1.5rem; }
    .detail-card-head { padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .detail-card-title { font-size: .62rem; font-weight: 500; letter-spacing: .2em; text-transform: uppercase; color: var(--acid); }
    .detail-card-body { padding: 1.4rem 1.5rem; }

    /* Eq detail */
    .eq-detail-row { display: flex; gap: 1rem; }
    .eq-detail-img { width: 80px; height: 80px; border-radius: 1px; overflow: hidden; flex-shrink: 0; background: #1a1a1a; display: flex; align-items: center; justify-content: center; font-size: 2rem; }
    .eq-detail-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .eq-detail-cat { font-size: .6rem; letter-spacing: .14em; text-transform: uppercase; color: var(--mid); }
    .eq-detail-name { font-family: var(--font-display); font-size: 1.3rem; text-transform: uppercase; color: var(--white); line-height: 1.1; margin: .2rem 0; }
    .eq-detail-meta { font-size: .78rem; color: var(--mid); }

    /* Spec grid */
    .spec-mini { display: grid; grid-template-columns: repeat(2,1fr); gap: 1px; background: var(--border); border: 1px solid var(--border); border-radius: 2px; margin-top: 1.25rem; overflow: hidden; }
    .spec-mini-cell { background: var(--card); padding: .8rem 1rem; }
    .spec-mini-lbl { font-size: .58rem; letter-spacing: .14em; text-transform: uppercase; color: var(--mid); margin-bottom: .2rem; }
    .spec-mini-val { font-size: .88rem; color: var(--white); }
    .spec-mini-val.acid { color: var(--acid); font-family: var(--font-display); font-size: 1.1rem; }

    /* Payment upload */
    .pay-methods { display: grid; grid-template-columns: repeat(3,1fr); gap: .5rem; margin-bottom: 1.25rem; }
    .pay-opt { padding: .75rem .5rem; text-align: center; cursor: pointer; border: 1px solid var(--border); border-radius: 1px; transition: border-color .15s,background .15s; }
    .pay-opt:hover { border-color: rgba(200,245,66,.3); }
    .pay-opt input { display: none; }
    .pay-opt input:checked ~ * { color: var(--acid) !important; }
    .pay-opt.selected { border-color: var(--acid); background: rgba(200,245,66,.06); }
    .pay-opt-icon { font-size: 1.4rem; display: block; margin-bottom: .25rem; }
    .pay-opt-lbl { font-size: .65rem; color: var(--muted); display: block; }

    .drop-zone {
        border: 2px dashed var(--border); border-radius: 2px; padding: 2rem;
        text-align: center; cursor: pointer; transition: border-color .2s,background .2s;
    }
    .drop-zone:hover, .drop-zone.drag-over { border-color: var(--acid); background: rgba(200,245,66,.04); }
    .drop-zone-ico { font-size: 2rem; margin-bottom: .5rem; opacity: .35; }
    .drop-zone-txt { font-size: .78rem; color: var(--mid); }
    .drop-zone-hint { font-size: .65rem; color: var(--mid); margin-top: .2rem; }
    #proof-preview { display: none; max-width: 100%; border-radius: 2px; border: 1px solid var(--border); margin-top: .75rem; }

    .btn-pay-submit {
        display: block; width: 100%; padding: .85rem;
        background: rgba(96,165,250,.15); color: #60a5fa;
        border: 1px solid rgba(96,165,250,.35); border-radius: 2px;
        font-family: var(--font-body); font-size: .85rem; font-weight: 600;
        letter-spacing: .1em; text-transform: uppercase; cursor: pointer;
        transition: background .2s; margin-top: 1.25rem;
    }
    .btn-pay-submit:hover { background: rgba(96,165,250,.25); }

    /* Paid proof */
    .proof-img-wrap { position: relative; }
    .proof-img-wrap img { width: 100%; border-radius: 2px; border: 1px solid var(--border); }
    .proof-img-open { position: absolute; top: .5rem; right: .5rem; background: rgba(8,8,8,.7); color: var(--white); padding: .25rem .6rem; font-size: .65rem; border-radius: 1px; text-decoration: none; letter-spacing: .06em; }

    /* Review form */
    .star-picker { display: flex; gap: .35rem; margin: .75rem 0; cursor: pointer; }
    .star-btn { font-size: 2.2rem; color: var(--border); transition: color .1s; user-select: none; line-height: 1; }
    .star-btn.on { color: #fbbf24; }
    .review-textarea { width: 100%; padding: .75rem .9rem; background: #1a1a1a; border: 1px solid var(--border); border-radius: 1px; color: var(--white); font-family: var(--font-body); font-size: .85rem; resize: none; transition: border-color .2s; }
    .review-textarea:focus { outline: none; border-color: rgba(200,245,66,.4); }
    .review-textarea::placeholder { color: var(--mid); }
    .btn-review-submit {
        padding: .7rem 1.75rem; background: rgba(251,191,36,.12); color: #fbbf24;
        border: 1px solid rgba(251,191,36,.3); border-radius: 2px;
        font-family: var(--font-body); font-size: .82rem; font-weight: 600;
        letter-spacing: .08em; text-transform: uppercase; cursor: pointer;
        transition: background .2s; margin-top: .75rem;
    }
    .btn-review-submit:hover { background: rgba(251,191,36,.22); }

    /* Existing review */
    .review-display { padding: 1.25rem; background: rgba(251,191,36,.06); border: 1px solid rgba(251,191,36,.15); border-radius: 2px; }
    .review-stars { color: #fbbf24; font-size: 1.1rem; letter-spacing: .05em; }
    .review-comment { font-size: .85rem; line-height: 1.75; color: rgba(244,242,237,.65); font-style: italic; margin-top: .6rem; }

    /* Sidebar actions */
    .sidebar-card { background: var(--card); border: 1px solid var(--border); border-radius: 2px; margin-bottom: 1rem; }
    .sidebar-card-title { font-size: .6rem; font-weight: 500; letter-spacing: .2em; text-transform: uppercase; color: var(--acid); padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); }
    .sidebar-card-body { padding: 1.1rem 1.25rem; }
    .side-info-row { display: flex; justify-content: space-between; font-size: .8rem; margin-bottom: .5rem; }
    .side-info-row:last-child { margin-bottom: 0; }
    .side-info-row .lbl { color: var(--mid); }
    .side-info-row .val { color: var(--white); text-align: right; }
    .side-info-row .val.acid { color: var(--acid); font-family: var(--font-display); font-size: 1rem; }

    .btn-side {
        display: block; width: 100%; padding: .65rem; text-align: center;
        border-radius: 1px; font-family: var(--font-body); font-size: .78rem;
        font-weight: 500; letter-spacing: .08em; text-transform: uppercase;
        text-decoration: none; cursor: pointer; transition: all .15s;
        margin-bottom: .5rem; border: 1px solid transparent;
    }
    .btn-side:last-child { margin-bottom: 0; }
    .btn-side-catalog { background: var(--acid); color: var(--black); border-color: var(--acid); }
    .btn-side-catalog:hover { background: #b8e035; }
    .btn-side-outline { background: transparent; color: var(--muted); border-color: var(--border); }
    .btn-side-outline:hover { border-color: var(--mid); color: var(--white); }
    .btn-side-danger { background: transparent; color: #f87171; border-color: rgba(248,113,113,.25); }
    .btn-side-danger:hover { background: rgba(248,113,113,.08); }

    /* Handler info */
    .handler-row { display: flex; align-items: center; gap: .65rem; }
    .handler-av { width: 2rem; height: 2rem; border-radius: 50%; background: rgba(96,165,250,.2); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: .78rem; font-weight: 700; flex-shrink: 0; }
    .handler-name { font-size: .85rem; color: var(--white); }
    .handler-role { font-size: .65rem; color: var(--mid); text-transform: uppercase; letter-spacing: .08em; }

    /* Notes */
    .notes-box { padding: 1rem 1.1rem; background: rgba(255,255,255,.03); border-left: 2px solid rgba(200,245,66,.3); border-radius: 0 2px 2px 0; font-size: .84rem; color: var(--muted); line-height: 1.7; font-style: italic; }

    @media(max-width: 900px) {
        .show-grid { grid-template-columns: 1fr; }
        .progress-bar { overflow-x: auto; gap: 0; }
        .spec-mini { grid-template-columns: 1fr; }
        .pay-methods { grid-template-columns: 1fr 1fr 1fr; }
    }
    @media(max-width: 640px) {
        .show-inner { padding: 1.5rem 1.5rem 5rem; }
    }
</style>
@endpush

@section('content')
@php
    $statusConfig = [
        'pending'   => ['banner' => 'sb-pending',   'icon' => '⏳', 'title' => 'Menunggu Konfirmasi', 'desc' => 'Transaksi kamu sedang menunggu konfirmasi dari tim kami. Silakan lakukan pembayaran.', 'step' => 0],
        'confirmed' => ['banner' => 'sb-confirmed', 'icon' => '✓',  'title' => 'Dikonfirmasi',         'desc' => 'Transaksi sudah dikonfirmasi. Alat akan segera disiapkan.', 'step' => 1],
        'active'    => ['banner' => 'sb-active',    'icon' => '🏄', 'title' => 'Sedang Disewa',         'desc' => 'Alat sedang kamu gunakan. Pastikan dikembalikan sebelum ' . $rental->end_date->format('d M Y') . '.', 'step' => 2],
        'returned'  => ['banner' => 'sb-returned',  'icon' => '✓',  'title' => 'Selesai',               'desc' => 'Alat sudah dikembalikan. Terima kasih telah menggunakan SportRent!', 'step' => 3],
        'cancelled' => ['banner' => 'sb-cancelled', 'icon' => '✕',  'title' => 'Dibatalkan',            'desc' => 'Transaksi ini telah dibatalkan.', 'step' => -1],
    ];
    $sc = $statusConfig[$rental->status] ?? $statusConfig['pending'];
@endphp

<div class="show-page">
<div class="show-inner">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flash flash-success">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    {{-- Status banner --}}
    <div class="status-banner {{ $sc['banner'] }}">
        <span class="sb-icon">{{ $sc['icon'] }}</span>
        <div>
            <div class="sb-title">{{ $sc['title'] }}</div>
            <div class="sb-desc">{{ $sc['desc'] }}</div>
        </div>
        <div class="sb-code">{{ $rental->rental_code }}</div>
    </div>

    {{-- Progress (hide for cancelled) --}}
    @if($rental->status !== 'cancelled')
        <div class="progress-bar">
            @foreach(['Menunggu','Dikonfirmasi','Aktif','Selesai'] as $pi => $pl)
                @php
                    $stepClass = $sc['step'] > $pi ? 'done' : ($sc['step'] === $pi ? 'current' : '');
                @endphp
                <div class="pb-step {{ $stepClass }}">
                    <div class="pb-dot">{{ $sc['step'] > $pi ? '✓' : ($pi + 1) }}</div>
                    <div class="pb-label">{{ $pl }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="show-grid">

        {{-- ── LEFT ── --}}
        <div>

            {{-- Alat --}}
            <div class="detail-card">
                <div class="detail-card-head">
                    <div class="detail-card-title">Alat yang Disewa</div>
                    <a href="{{ route('equipment.show', $rental->equipment) }}" style="font-size:.7rem;color:var(--mid);text-decoration:none;">Lihat Alat ↗</a>
                </div>
                <div class="detail-card-body">
                    <div class="eq-detail-row">
                        <div class="eq-detail-img">
                            @if($rental->equipment->image)
                                <img src="{{ Storage::url($rental->equipment->image) }}" alt="{{ $rental->equipment->name }}">
                            @else
                                {{ $rental->equipment->category->icon ?? '🏅' }}
                            @endif
                        </div>
                        <div>
                            <div class="eq-detail-cat">{{ $rental->equipment->category->name }}</div>
                            <div class="eq-detail-name">{{ $rental->equipment->name }}</div>
                            <div class="eq-detail-meta">
                                Rp {{ number_format($rental->equipment->price_per_day, 0, ',', '.') }}/hari
                                @if($rental->equipment->brand) · {{ $rental->equipment->brand }} @endif
                            </div>
                        </div>
                    </div>
                    <div class="spec-mini">
                        <div class="spec-mini-cell">
                            <div class="spec-mini-lbl">Jumlah Unit</div>
                            <div class="spec-mini-val">{{ $rental->quantity }} unit</div>
                        </div>
                        <div class="spec-mini-cell">
                            <div class="spec-mini-lbl">Durasi</div>
                            <div class="spec-mini-val">{{ $rental->duration_days }} hari</div>
                        </div>
                        <div class="spec-mini-cell">
                            <div class="spec-mini-lbl">Tanggal Mulai</div>
                            <div class="spec-mini-val">{{ $rental->start_date->format('d M Y') }}</div>
                        </div>
                        <div class="spec-mini-cell">
                            <div class="spec-mini-lbl">Tanggal Selesai</div>
                            <div class="spec-mini-val">{{ $rental->end_date->format('d M Y') }}</div>
                        </div>
                        <div class="spec-mini-cell" style="grid-column: span 2;">
                            <div class="spec-mini-lbl">Total Pembayaran</div>
                            <div class="spec-mini-val acid">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @if($rental->notes)
                        <div style="margin-top:1.1rem;">
                            <div style="font-size:.6rem;letter-spacing:.14em;text-transform:uppercase;color:var(--mid);margin-bottom:.4rem;">Catatan</div>
                            <div class="notes-box">{{ $rental->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Pembayaran --}}
            @php $payment = $rental->payment; @endphp
            <div class="detail-card" id="bayar">
                <div class="detail-card-head">
                    <div class="detail-card-title">Pembayaran</div>
                    @if($payment?->status === 'paid')
                        <span style="font-size:.65rem;color:var(--acid);">✓ Lunas</span>
                    @elseif($payment)
                        <span style="font-size:.65rem;color:#fbbf24;">⏳ Menunggu Verifikasi</span>
                    @else
                        <span style="font-size:.65rem;color:#f87171;">Belum Bayar</span>
                    @endif
                </div>
                <div class="detail-card-body">
                    @if($payment?->status === 'paid')
                        {{-- Already paid --}}
                        <div style="display:flex;flex-direction:column;gap:.75rem;">
                            <div class="side-info-row"><span class="lbl">Metode</span><span class="val">{{ $payment->method_label }}</span></div>
                            <div class="side-info-row"><span class="lbl">Jumlah</span><span class="val acid">{{ $payment->formatted_amount }}</span></div>
                            <div class="side-info-row"><span class="lbl">Lunas Pada</span><span class="val">{{ $payment->paid_at?->format('d M Y, H:i') }}</span></div>
                            @if($payment->proof_image)
                                <div>
                                    <div style="font-size:.6rem;letter-spacing:.14em;text-transform:uppercase;color:var(--mid);margin-bottom:.5rem;">Bukti Pembayaran</div>
                                    <div class="proof-img-wrap">
                                        <img src="{{ Storage::url($payment->proof_image) }}" alt="Bukti">
                                        <a href="{{ Storage::url($payment->proof_image) }}" target="_blank" class="proof-img-open">↗ Buka</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif($payment && $payment->status !== 'paid')
                        {{-- Pending verification --}}
                        <div style="padding:1.1rem;background:rgba(251,191,36,.06);border:1px solid rgba(251,191,36,.2);border-radius:2px;font-size:.84rem;color:#fbbf24;margin-bottom:1.25rem;">
                            ⏳ Bukti pembayaran sudah dikirim, menunggu verifikasi admin.
                        </div>
                        @if($payment->proof_image)
                            <div>
                                <div style="font-size:.6rem;letter-spacing:.14em;text-transform:uppercase;color:var(--mid);margin-bottom:.5rem;">Bukti yang Dikirim</div>
                                <div class="proof-img-wrap">
                                    <img src="{{ Storage::url($payment->proof_image) }}" alt="Bukti">
                                    <a href="{{ Storage::url($payment->proof_image) }}" target="_blank" class="proof-img-open">↗ Buka</a>
                                </div>
                            </div>
                        @endif
                    @elseif($rental->status !== 'cancelled' && $rental->status !== 'returned')
                        {{-- Upload form --}}
                        <div style="margin-bottom:1.1rem;padding:1rem;background:rgba(96,165,250,.06);border:1px solid rgba(96,165,250,.15);border-radius:2px;">
                            <div style="font-size:.78rem;color:var(--muted);line-height:1.7;">
                                Silakan transfer ke rekening kami:<br>
                                <strong style="color:var(--white);">BCA · 1234567890 · a.n. SportRent Indonesia</strong><br>
                                Total: <strong style="color:var(--acid);">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('rentals.pay', $rental) }}" enctype="multipart/form-data">
                            @csrf
                            <div style="font-size:.6rem;letter-spacing:.14em;text-transform:uppercase;color:var(--mid);margin-bottom:.6rem;">Metode Pembayaran</div>
                            <div class="pay-methods">
                                @foreach(['transfer' => ['🏦','Transfer Bank'], 'ewallet' => ['📱','E-Wallet'], 'cash' => ['💵','Tunai']] as $val => $opt)
                                    <label class="pay-opt {{ old('method','transfer') === $val ? 'selected' : '' }}" onclick="selectPay(this)">
                                        <input type="radio" name="method" value="{{ $val }}" {{ old('method','transfer') === $val ? 'checked' : '' }}>
                                        <span class="pay-opt-icon">{{ $opt[0] }}</span>
                                        <span class="pay-opt-lbl">{{ $opt[1] }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div style="margin-top:1.1rem;">
                                <div style="font-size:.6rem;letter-spacing:.14em;text-transform:uppercase;color:var(--mid);margin-bottom:.6rem;">Upload Bukti Pembayaran</div>
                                <div class="drop-zone" id="drop-zone" onclick="document.getElementById('proof-file').click()"
                                     ondragover="event.preventDefault();this.classList.add('drag-over')"
                                     ondragleave="this.classList.remove('drag-over')"
                                     ondrop="handleDrop(event)">
                                    <div class="drop-zone-ico">🖼</div>
                                    <div class="drop-zone-txt" id="dz-txt">Klik atau seret foto bukti transfer</div>
                                    <div class="drop-zone-hint">JPG, PNG, WebP · Maks 3MB</div>
                                </div>
                                <input type="file" name="proof_image" id="proof-file" accept="image/*" style="display:none;" onchange="previewProof(this)">
                                <img id="proof-preview" src="" alt="Preview">
                            </div>

                            <button type="submit" class="btn-pay-submit">📤 Kirim Bukti Pembayaran</button>
                        </form>
                    @else
                        <div style="text-align:center;padding:1.5rem;color:var(--mid);font-size:.82rem;">
                            @if($rental->status === 'returned') Transaksi sudah selesai.
                            @else Transaksi dibatalkan, tidak perlu pembayaran.
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Ulasan --}}
            <div class="detail-card" id="ulasan">
                <div class="detail-card-head">
                    <div class="detail-card-title">Ulasan</div>
                </div>
                <div class="detail-card-body">
                    @if($rental->review)
                        <div class="review-display">
                            <div class="review-stars">
                                @for($s=1;$s<=5;$s++)<span style="color:{{ $s<=$rental->review->rating?'#fbbf24':'rgba(251,191,36,.2)' }}">★</span>@endfor
                                <span style="font-size:.78rem;color:var(--mid);margin-left:.4rem;">{{ $rental->review->rating }}/5</span>
                            </div>
                            @if($rental->review->comment)
                                <div class="review-comment">"{{ $rental->review->comment }}"</div>
                            @endif
                            <div style="font-size:.7rem;color:var(--mid);margin-top:.65rem;">Dikirim {{ $rental->review->created_at->diffForHumans() }}</div>
                        </div>
                    @elseif($canReview)
                        <p style="font-size:.82rem;color:var(--mid);margin-bottom:1.25rem;line-height:1.7;">
                            Bagaimana pengalaman kamu menyewa <strong style="color:var(--white);">{{ $rental->equipment->name }}</strong>? Ulasanmu membantu pengguna lain.
                        </p>
                        <form method="POST" action="{{ route('rentals.review', $rental) }}">
                            @csrf
                            <div>
                                <div style="font-size:.6rem;letter-spacing:.14em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Rating</div>
                                <div class="star-picker" id="star-picker">
                                    @for($s=1;$s<=5;$s++)
                                        <span class="star-btn" data-val="{{ $s }}">★</span>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-input">
                                <div id="rating-lbl" style="font-size:.72rem;color:var(--mid);min-height:1em;margin-bottom:1rem;"></div>
                                @error('rating') <div style="font-size:.72rem;color:#f87171;margin-bottom:.75rem;">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label style="display:block;font-size:.6rem;letter-spacing:.14em;text-transform:uppercase;color:var(--mid);margin-bottom:.4rem;">Komentar (Opsional)</label>
                                <textarea name="comment" class="review-textarea" rows="4"
                                          placeholder="Ceritakan pengalamanmu…" maxlength="1000">{{ old('comment') }}</textarea>
                                @error('comment') <div style="font-size:.72rem;color:#f87171;margin-top:.25rem;">{{ $message }}</div> @enderror
                            </div>
                            <button type="submit" class="btn-review-submit">★ Kirim Ulasan</button>
                        </form>
                    @else
                        <div style="text-align:center;padding:1.5rem;font-size:.82rem;color:var(--mid);">
                            @if($rental->status === 'returned')
                                Ulasan sudah dikirim.
                            @else
                                Ulasan hanya dapat diberikan setelah alat dikembalikan.
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── RIGHT SIDEBAR ── --}}
        <div>

            {{-- Total & info --}}
            <div class="sidebar-card">
                <div class="sidebar-card-title">Ringkasan</div>
                <div class="sidebar-card-body">
                    <div class="side-info-row"><span class="lbl">Kode Transaksi</span><span class="val" style="font-family:monospace;font-size:.78rem;color:var(--acid);">{{ $rental->rental_code }}</span></div>
                    <div class="side-info-row"><span class="lbl">Status</span><span class="val">{{ $sc['title'] }}</span></div>
                    <div class="side-info-row"><span class="lbl">Dibuat</span><span class="val" style="font-size:.78rem;">{{ $rental->created_at->format('d M Y, H:i') }}</span></div>
                    <div style="height:1px;background:var(--border);margin:.65rem 0;"></div>
                    <div class="side-info-row"><span class="lbl">Subtotal</span><span class="val acid">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</span></div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="sidebar-card">
                <div class="sidebar-card-title">Aksi</div>
                <div class="sidebar-card-body">
                    @if(in_array($rental->status,['pending','confirmed']) && (!$payment || $payment->status !== 'paid'))
                        <a href="#bayar" class="btn-side" style="background:rgba(96,165,250,.12);color:#60a5fa;border:1px solid rgba(96,165,250,.3);">
                            💳 Bayar Sekarang
                        </a>
                    @endif
                    @if($canReview)
                        <a href="#ulasan" class="btn-side" style="background:rgba(251,191,36,.1);color:#fbbf24;border:1px solid rgba(251,191,36,.25);">
                            ★ Beri Ulasan
                        </a>
                    @endif
                    <a href="{{ route('catalog') }}" class="btn-side btn-side-catalog">+ Sewa Alat Lagi</a>
                    <a href="{{ route('rentals.index') }}" class="btn-side btn-side-outline">← Semua Transaksi</a>
                    @if($rental->status === 'pending')
                        <form method="POST" action="{{ route('rentals.cancel', $rental) }}"
                              onsubmit="return confirm('Yakin ingin membatalkan transaksi ini?')" style="margin-top:.25rem;">
                            @csrf
                            <button type="submit" class="btn-side btn-side-danger">✕ Batalkan Transaksi</button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Diproses oleh --}}
            @if($rental->handler)
                <div class="sidebar-card">
                    <div class="sidebar-card-title">Diproses Oleh</div>
                    <div class="sidebar-card-body">
                        <div class="handler-row">
                            <div class="handler-av">{{ strtoupper(substr($rental->handler->name,0,1)) }}</div>
                            <div>
                                <div class="handler-name">{{ $rental->handler->name }}</div>
                                <div class="handler-role">{{ ucfirst($rental->handler->role) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Timeline --}}
            <div class="sidebar-card">
                <div class="sidebar-card-title">Riwayat</div>
                <div class="sidebar-card-body">
                    @php
                        $timeline = [
                            ['label'=>'Transaksi Dibuat', 'time'=>$rental->created_at, 'show'=>true],
                            ['label'=>'Dikonfirmasi',     'time'=>$rental->confirmed_at,'show'=>!!$rental->confirmed_at],
                            ['label'=>'Alat Dikembalikan','time'=>$rental->returned_at, 'show'=>!!$rental->returned_at],
                            ['label'=>'Dibatalkan',       'time'=>$rental->updated_at,  'show'=>$rental->status==='cancelled'],
                        ];
                    @endphp
                    <div style="display:flex;flex-direction:column;gap:0;">
                        @foreach(array_filter($timeline, fn($t) => $t['show']) as $tl)
                            <div style="display:flex;gap:.75rem;padding-bottom:{{ !$loop->last?'1rem':'0' }};position:relative;">
                                @if(!$loop->last)
                                    <div style="position:absolute;left:.45rem;top:1.1rem;bottom:0;width:1px;background:var(--border);"></div>
                                @endif
                                <div style="width:.9rem;height:.9rem;border-radius:50%;background:var(--acid);border:2px solid var(--black);flex-shrink:0;margin-top:.1rem;"></div>
                                <div>
                                    <div style="font-size:.8rem;color:var(--white);">{{ $tl['label'] }}</div>
                                    <div style="font-size:.68rem;color:var(--mid);">{{ $tl['time']->format('d M Y, H:i') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
    // ── Payment method selection ─────────────────────────────────────────────
    function selectPay(lbl) {
        document.querySelectorAll('.pay-opt').forEach(l => l.classList.remove('selected'));
        lbl.classList.add('selected');
        lbl.querySelector('input').checked = true;
    }

    // ── Proof upload ─────────────────────────────────────────────────────────
    function previewProof(input) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('proof-preview');
            const dz      = document.getElementById('drop-zone');
            preview.src   = e.target.result;
            preview.style.display = 'block';
            dz.style.borderColor = 'var(--acid)';
            document.getElementById('dz-txt').textContent = file.name;
        };
        reader.readAsDataURL(file);
    }

    function handleDrop(e) {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        document.getElementById('drop-zone').classList.remove('drag-over');
        if (file && file.type.startsWith('image/')) {
            const dt = new DataTransfer(); dt.items.add(file);
            const inp = document.getElementById('proof-file');
            inp.files  = dt.files;
            previewProof(inp);
        }
    }

    // ── Star rating ──────────────────────────────────────────────────────────
    const ratingLabels = ['','Tidak Puas','Kurang Puas','Cukup','Puas','Sangat Puas'];
    let currentRating  = 0;
    const starBtns     = document.querySelectorAll('.star-btn');

    function paintStars(val) {
        starBtns.forEach(s => s.classList.toggle('on', parseInt(s.dataset.val) <= val));
    }

    starBtns.forEach(s => {
        s.addEventListener('mouseover', () => paintStars(parseInt(s.dataset.val)));
        s.addEventListener('mouseleave', () => paintStars(currentRating));
        s.addEventListener('click', () => {
            currentRating = parseInt(s.dataset.val);
            document.getElementById('rating-input').value = currentRating;
            document.getElementById('rating-lbl').textContent = ratingLabels[currentRating];
            document.getElementById('rating-lbl').style.color = currentRating >= 4 ? 'var(--acid)' : (currentRating === 3 ? '#fbbf24' : '#f87171');
        });
    });

    // ── Scroll to anchor ─────────────────────────────────────────────────────
    if (window.location.hash === '#bayar')  document.getElementById('bayar')?.scrollIntoView({behavior:'smooth'});
    if (window.location.hash === '#ulasan') document.getElementById('ulasan')?.scrollIntoView({behavior:'smooth'});
</script>
@endpush
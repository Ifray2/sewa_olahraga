@extends('layouts.home')

@section('title', 'Buat Transaksi Sewa — SportRent')
@section('nav-class', 'always-solid')

@section('nav-center')
    <a href="{{ $equipment ? route('equipment.show', $equipment) : route('catalog') }}" class="nav-back">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M12 7H2M7 12L2 7l5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        {{ $equipment ? 'Kembali ke Detail Alat' : 'Kembali ke Katalog' }}
    </a>
@endsection

@push('styles')
<style>
    .create-page { padding-top: 70px; min-height: 100vh; }
    .create-inner { max-width: 960px; margin: 0 auto; padding: 3rem 2rem 6rem; }

    .create-heading { font-family: var(--font-display); font-size: clamp(2rem,4vw,3.5rem); text-transform: uppercase; line-height: .9; margin-bottom: .5rem; }
    .create-heading span { color: var(--acid); }
    .create-sub { font-size: .82rem; color: var(--mid); margin-bottom: 2.5rem; }

    .create-grid { display: grid; grid-template-columns: 1fr 340px; gap: 2rem; align-items: start; }

    /* Shared card */
    .form-card { background: var(--card); border: 1px solid var(--border); border-radius: 2px; }
    .form-card-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
    .form-card-title { font-size: .65rem; font-weight: 500; letter-spacing: .2em; text-transform: uppercase; color: var(--acid); }
    .form-card-body { padding: 1.5rem; }

    /* Equipment preview */
    .eq-preview { display: flex; gap: 1rem; padding: 1.25rem; background: rgba(200,245,66,.04); border: 1px solid rgba(200,245,66,.15); border-radius: 2px; margin-bottom: 1.5rem; }
    .eq-preview-img { width: 72px; height: 72px; border-radius: 1px; overflow: hidden; flex-shrink: 0; background: #1a1a1a; display: flex; align-items: center; justify-content: center; font-size: 2rem; }
    .eq-preview-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .eq-preview-cat { font-size: .6rem; letter-spacing: .14em; text-transform: uppercase; color: var(--mid); }
    .eq-preview-name { font-family: var(--font-display); font-size: 1.2rem; text-transform: uppercase; color: var(--white); line-height: 1.1; margin: .2rem 0 .4rem; }
    .eq-preview-price { font-size: .85rem; color: var(--acid); }

    /* Equipment selector (when no equipment pre-selected) */
    .eq-selector { display: flex; flex-direction: column; gap: .5rem; }
    .eq-selector-item { display: flex; align-items: center; gap: .85rem; padding: .9rem 1rem; border: 1px solid var(--border); border-radius: 1px; cursor: pointer; transition: border-color .2s,background .2s; }
    .eq-selector-item:hover { border-color: rgba(200,245,66,.25); background: rgba(200,245,66,.04); }
    .eq-selector-item.selected { border-color: var(--acid); background: rgba(200,245,66,.07); }
    .eq-selector-item-img { width: 2.8rem; height: 2.8rem; border-radius: 1px; overflow: hidden; flex-shrink: 0; background: #1a1a1a; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .eq-selector-item-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .eq-selector-item-name { font-size: .85rem; color: var(--white); }
    .eq-selector-item-price { font-size: .72rem; color: var(--acid); }

    /* Form inputs */
    .form-group { margin-bottom: 1.25rem; }
    .form-group:last-child { margin-bottom: 0; }
    .form-label { display: block; font-size: .6rem; letter-spacing: .16em; text-transform: uppercase; color: var(--mid); margin-bottom: .4rem; }
    .form-req { color: #f87171; }
    .form-control {
        width: 100%; padding: .7rem .9rem;
        background: #1a1a1a; border: 1px solid var(--border); border-radius: 1px;
        color: var(--white); font-family: var(--font-body); font-size: .88rem;
        transition: border-color .2s; color-scheme: dark;
    }
    .form-control:focus { outline: none; border-color: rgba(200,245,66,.4); }
    .form-control::placeholder { color: var(--mid); }
    .form-error { font-size: .72rem; color: #f87171; margin-top: .3rem; }
    .form-hint { font-size: .7rem; color: var(--mid); margin-top: .3rem; }

    .date-row { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }

    /* Qty control */
    .qty-row { display: flex; align-items: center; justify-content: space-between; }
    .qty-ctrl { display: flex; align-items: center; gap: .75rem; }
    .qty-btn { width: 2.2rem; height: 2.2rem; background: #1a1a1a; border: 1px solid var(--border); border-radius: 1px; color: var(--white); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.1rem; transition: border-color .2s,color .2s; }
    .qty-btn:hover { border-color: var(--acid); color: var(--acid); }
    .qty-num { font-family: var(--font-display); font-size: 1.6rem; color: var(--white); min-width: 1.75rem; text-align: center; }

    /* Summary card */
    .summary-rows { display: flex; flex-direction: column; gap: .6rem; }
    .sum-row { display: flex; justify-content: space-between; font-size: .82rem; }
    .sum-row .lbl { color: var(--mid); }
    .sum-row .val { color: var(--white); }
    .sum-divider { height: 1px; background: var(--border); margin: .25rem 0; }
    .sum-total-row { display: flex; justify-content: space-between; align-items: baseline; }
    .sum-total-lbl { font-size: .65rem; letter-spacing: .14em; text-transform: uppercase; color: var(--mid); }
    .sum-total-val { font-family: var(--font-display); font-size: 2rem; color: var(--acid); }

    /* Payment info */
    .payment-methods { display: grid; grid-template-columns: repeat(3,1fr); gap: .5rem; }
    .pay-method {
        padding: .7rem .5rem; text-align: center; cursor: pointer;
        border: 1px solid var(--border); border-radius: 1px;
        transition: border-color .15s,background .15s;
    }
    .pay-method:hover { border-color: rgba(200,245,66,.3); }
    .pay-method.selected { border-color: var(--acid); background: rgba(200,245,66,.08); }
    .pay-method input { display: none; }
    .pay-method-icon { font-size: 1.3rem; display: block; margin-bottom: .25rem; }
    .pay-method-lbl { font-size: .65rem; color: var(--muted); display: block; }

    /* CTA */
    .btn-submit {
        display: block; width: 100%; padding: .95rem;
        background: var(--acid); color: var(--black); border: none; border-radius: 2px;
        font-family: var(--font-body); font-size: .88rem; font-weight: 700;
        letter-spacing: .1em; text-transform: uppercase; cursor: pointer;
        transition: transform .15s, box-shadow .15s;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(200,245,66,.3); }
    .btn-submit:disabled { background: #2a2a2a; color: var(--mid); transform: none; box-shadow: none; cursor: not-allowed; }

    .trust-mini { display: flex; flex-direction: column; gap: .4rem; margin-top: .75rem; }
    .trust-mini-item { display: flex; align-items: center; gap: .5rem; font-size: .72rem; color: var(--mid); }

    /* Flash */
    .flash-error { padding: 1rem 1.25rem; background: rgba(248,113,113,.08); border: 1px solid rgba(248,113,113,.25); border-radius: 2px; color: #f87171; font-size: .85rem; margin-bottom: 1.5rem; }

    @media(max-width: 900px) {
        .create-grid { grid-template-columns: 1fr; }
        .date-row { grid-template-columns: 1fr; }
    }
    @media(max-width: 640px) {
        .create-inner { padding: 2rem 1.5rem 5rem; }
    }
</style>
@endpush

@section('content')
<div class="create-page">
    <div class="create-inner">

        <h1 class="create-heading">Buat <span>Transaksi</span></h1>
        <p class="create-sub">Isi detail penyewaan di bawah. Pastikan tanggal dan jumlah sudah benar sebelum mengkonfirmasi.</p>

        @if(session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('rentals.store') }}" id="rental-form">
            @csrf
            <div class="create-grid">

                {{-- ── LEFT ── --}}
                <div style="display:flex;flex-direction:column;gap:1.5rem;">

                    {{-- Pilih / tampil alat --}}
                    <div class="form-card">
                        <div class="form-card-head">
                            <div class="form-card-title">{{ $equipment ? 'Alat yang Disewa' : 'Pilih Alat' }}</div>
                        </div>
                        <div class="form-card-body">
                            @if($equipment)
                                <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
                                <div class="eq-preview">
                                    <div class="eq-preview-img">
                                        @if($equipment->image)
                                            <img src="{{ Storage::url($equipment->image) }}" alt="{{ $equipment->name }}">
                                        @else
                                            {{ $equipment->category->icon ?? '🏅' }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="eq-preview-cat">{{ $equipment->category->name }}</div>
                                        <div class="eq-preview-name">{{ $equipment->name }}</div>
                                        <div class="eq-preview-price">
                                            Rp {{ number_format($equipment->price_per_day, 0, ',', '.') }}/hari
                                            · Stok: {{ $equipment->stock }} unit
                                            @if($equipment->brand) · {{ $equipment->brand }} @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('catalog') }}" style="margin-left:auto;font-size:.68rem;color:var(--mid);text-decoration:none;white-space:nowrap;flex-shrink:0;">Ganti →</a>
                                </div>
                            @else
                                <div class="form-group">
                                    <label class="form-label">Alat <span class="form-req">*</span></label>
                                    <select name="equipment_id" class="form-control" required>
                                        <option value="">— Pilih Alat —</option>
                                        @foreach(\App\Models\Equipment::available()->with('category')->orderBy('name')->get() as $eq)
                                            <option value="{{ $eq->id }}"
                                                data-price="{{ $eq->price_per_day }}"
                                                data-stock="{{ $eq->stock }}"
                                                {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                                                {{ $eq->category->icon ?? '' }} {{ $eq->name }} — Rp {{ number_format($eq->price_per_day,0,',','.') }}/hari
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('equipment_id') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Tanggal & durasi --}}
                    <div class="form-card">
                        <div class="form-card-head">
                            <div class="form-card-title">Periode Sewa</div>
                        </div>
                        <div class="form-card-body">
                            <div class="date-row" style="margin-bottom:1.25rem;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Tanggal Mulai <span class="form-req">*</span></label>
                                    <input type="date" name="start_date" id="start_date"
                                           value="{{ old('start_date', $defaults['start_date']) }}"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           class="form-control" onchange="recalc()">
                                    @error('start_date') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Tanggal Selesai <span class="form-req">*</span></label>
                                    <input type="date" name="end_date" id="end_date"
                                           value="{{ old('end_date', $defaults['end_date']) }}"
                                           class="form-control" onchange="recalc()">
                                    @error('end_date') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom:0;">
                                <div class="qty-row">
                                    <label class="form-label" style="margin-bottom:0;">Jumlah Unit <span class="form-req">*</span></label>
                                    <div class="qty-ctrl">
                                        <div class="qty-btn" onclick="changeQty(-1)">−</div>
                                        <div class="qty-num" id="qty-display">{{ $defaults['quantity'] }}</div>
                                        <div class="qty-btn" onclick="changeQty(1)">+</div>
                                    </div>
                                </div>
                                <input type="hidden" name="quantity" id="qty-input" value="{{ $defaults['quantity'] }}">
                                <div id="stock-hint" class="form-hint">
                                    @if($equipment) Stok tersedia: {{ $equipment->stock }} unit @endif
                                </div>
                                @error('quantity') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="form-card">
                        <div class="form-card-head">
                            <div class="form-card-title">Catatan (Opsional)</div>
                        </div>
                        <div class="form-card-body">
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Permintaan khusus, alamat pengiriman, atau informasi tambahan…"
                                      maxlength="500">{{ old('notes') }}</textarea>
                            @error('notes') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- ── RIGHT ── --}}
                <div style="display:flex;flex-direction:column;gap:1.25rem;position:sticky;top:calc(70px + 1.5rem);">

                    {{-- Ringkasan --}}
                    <div class="form-card">
                        <div class="form-card-head">
                            <div class="form-card-title">Ringkasan Pesanan</div>
                        </div>
                        <div class="form-card-body">
                            <div class="summary-rows">
                                <div class="sum-row">
                                    <span class="lbl">Harga/hari</span>
                                    <span class="val" id="sum-price">
                                        @if($equipment) Rp {{ number_format($equipment->price_per_day, 0, ',', '.') }} @else — @endif
                                    </span>
                                </div>
                                <div class="sum-row">
                                    <span class="lbl">Jumlah unit</span>
                                    <span class="val" id="sum-qty">{{ $defaults['quantity'] }} unit</span>
                                </div>
                                <div class="sum-row">
                                    <span class="lbl">Durasi</span>
                                    <span class="val" id="sum-duration">— hari</span>
                                </div>
                                <div class="sum-divider"></div>
                                <div class="sum-total-row">
                                    <span class="sum-total-lbl">Estimasi Total</span>
                                    <span class="sum-total-val" id="sum-total">Rp —</span>
                                </div>
                            </div>
                        </div>
                        <div style="padding:1rem 1.5rem;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:.5rem;">
                            <button type="submit" class="btn-submit" id="submit-btn">
                                Konfirmasi Sewa
                            </button>
                            <div class="trust-mini">
                                <div class="trust-mini-item"><span>✓</span> Gratis batal jika belum dikonfirmasi</div>
                                <div class="trust-mini-item"><span>🛡</span> Alat dijamin bersih & terawat</div>
                                <div class="trust-mini-item"><span>💬</span> Bantuan 24/7</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const priceMap  = {};
    const stockMap  = {};
    let pricePerDay = {{ $equipment ? (float)$equipment->price_per_day : 0 }};
    let maxStock    = {{ $equipment ? $equipment->stock : 99 }};
    let qty         = {{ (int)$defaults['quantity'] }};

    // Populate price/stock map if no pre-selected equipment
    document.querySelectorAll('select[name="equipment_id"] option[data-price]').forEach(opt => {
        priceMap[opt.value]  = parseFloat(opt.dataset.price);
        stockMap[opt.value]  = parseInt(opt.dataset.stock);
    });

    const eqSelect = document.querySelector('select[name="equipment_id"]');
    if (eqSelect) {
        eqSelect.addEventListener('change', function() {
            pricePerDay = priceMap[this.value] || 0;
            maxStock    = stockMap[this.value] || 99;
            document.getElementById('sum-price').textContent =
                pricePerDay ? 'Rp ' + pricePerDay.toLocaleString('id-ID') : '—';
            document.getElementById('stock-hint').textContent =
                maxStock ? 'Stok tersedia: ' + maxStock + ' unit' : '';
            recalc();
        });
    }

    function changeQty(d) {
        qty = Math.max(1, Math.min(maxStock, qty + d));
        document.getElementById('qty-display').textContent = qty;
        document.getElementById('qty-input').value = qty;
        document.getElementById('sum-qty').textContent = qty + ' unit';
        recalc();
    }

    function recalc() {
        const s   = document.getElementById('start_date').value;
        const e   = document.getElementById('end_date').value;
        if (e && s && e <= s) {
            const next = new Date(s); next.setDate(next.getDate() + 1);
            document.getElementById('end_date').value = next.toISOString().split('T')[0];
        }
        const days  = s && e ? Math.max(0, Math.round((new Date(e) - new Date(s)) / 86400000)) : 0;
        const total = pricePerDay * days * qty;

        document.getElementById('sum-duration').textContent = days ? days + ' hari' : '— hari';
        document.getElementById('sum-total').textContent    = total ? 'Rp ' + total.toLocaleString('id-ID') : 'Rp —';
        document.getElementById('submit-btn').disabled      = !days || !pricePerDay;
    }

    recalc();
</script>
@endpush
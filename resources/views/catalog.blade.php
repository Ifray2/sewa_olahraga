@extends('layouts.home')

@section('title', 'Katalog Alat — SportRent')
@section('description', 'Temukan ratusan peralatan olahraga premium. Filter berdasarkan kategori, harga, dan kondisi.')
@section('nav-class', 'always-solid')

@push('styles')
<style>
    /* ── PAGE WRAPPER ── */
    .catalog-page { padding-top: 70px; min-height: 100vh; }

    /* ── CATALOG HEADER ── */
    .catalog-header {
        padding: 3.5rem 3rem 0;
        position: relative;
        overflow: hidden;
    }
    .catalog-header::before {
        content: 'KATALOG';
        position: absolute;
        right: 2rem; top: -1rem;
        font-family: var(--font-display);
        font-size: 10rem; letter-spacing: -.02em;
        color: rgba(200,245,66,.04);
        line-height: 1; pointer-events: none;
        user-select: none;
    }
    .catalog-header-inner {
        display: flex; align-items: flex-end; justify-content: space-between;
        flex-wrap: wrap; gap: 1.5rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--border);
    }
    .catalog-eyebrow {
        font-size: .65rem; font-weight: 500; letter-spacing: .22em;
        text-transform: uppercase; color: var(--acid); margin-bottom: .6rem;
        display: flex; align-items: center; gap: .5rem;
    }
    .catalog-eyebrow::before { content: ''; display: block; width: 1.5rem; height: 1px; background: var(--acid); }
    .catalog-heading {
        font-family: var(--font-display);
        font-size: clamp(2.5rem, 5vw, 4.5rem);
        line-height: .9; letter-spacing: .02em; text-transform: uppercase;
    }
    .catalog-result-count {
        font-size: .75rem; color: var(--mid); letter-spacing: .05em;
    }
    .catalog-result-count strong { color: var(--acid); font-family: var(--font-display); font-size: 1.2rem; }

    /* ── MAIN LAYOUT ── */
    .catalog-body {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 0;
        padding: 0 3rem 6rem;
        align-items: start;
    }

    /* ── SIDEBAR ── */
    .catalog-sidebar {
        padding: 2rem 2rem 2rem 0;
        position: sticky;
        top: 70px;
        max-height: calc(100vh - 70px);
        overflow-y: auto;
    }
    .catalog-sidebar::-webkit-scrollbar { width: 3px; }
    .catalog-sidebar::-webkit-scrollbar-track { background: transparent; }
    .catalog-sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

    .filter-section { margin-bottom: 2rem; }
    .filter-title {
        font-size: .62rem; font-weight: 500; letter-spacing: .2em;
        text-transform: uppercase; color: var(--acid);
        padding-bottom: .65rem; border-bottom: 1px solid var(--border);
        margin-bottom: 1rem;
    }

    /* Search */
    .filter-search {
        position: relative;
    }
    .filter-search input {
        width: 100%; padding: .65rem .85rem .65rem 2.25rem;
        background: #111; border: 1px solid var(--border); border-radius: 1px;
        color: var(--white); font-family: var(--font-body); font-size: .82rem;
        transition: border-color .2s;
    }
    .filter-search input:focus { outline: none; border-color: rgba(200,245,66,.4); }
    .filter-search input::placeholder { color: var(--mid); }
    .filter-search::before {
        content: '🔍';
        position: absolute; left: .75rem; top: 50%; transform: translateY(-50%);
        font-size: .75rem; pointer-events: none;
    }

    /* Category list */
    .cat-filter-list { display: flex; flex-direction: column; gap: .2rem; }
    .cat-filter-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: .55rem .75rem; border-radius: 1px;
        text-decoration: none; font-size: .8rem; color: var(--muted);
        transition: background .15s, color .15s; cursor: pointer; border: none;
        background: transparent; width: 100%; text-align: left; font-family: var(--font-body);
    }
    .cat-filter-item:hover { background: rgba(200,245,66,.06); color: var(--white); }
    .cat-filter-item.active { background: rgba(200,245,66,.1); color: var(--acid); }
    .cat-filter-item .cat-filter-icon { font-size: 1rem; margin-right: .5rem; }
    .cat-filter-item .cat-filter-count {
        font-size: .65rem; color: var(--mid);
        background: rgba(255,255,255,.06); padding: .1rem .4rem;
        border-radius: 8px; flex-shrink: 0;
    }
    .cat-filter-item.active .cat-filter-count { background: rgba(200,245,66,.15); color: var(--acid); }

    /* Price range */
    .price-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; }
    .price-input-wrap label {
        display: block; font-size: .58rem; letter-spacing: .14em;
        text-transform: uppercase; color: var(--mid); margin-bottom: .3rem;
    }
    .price-input-wrap input {
        width: 100%; padding: .55rem .7rem;
        background: #111; border: 1px solid var(--border); border-radius: 1px;
        color: var(--white); font-family: var(--font-body); font-size: .78rem;
        transition: border-color .2s;
    }
    .price-input-wrap input:focus { outline: none; border-color: rgba(200,245,66,.4); }

    /* Sort & condition */
    .filter-select {
        width: 100%; padding: .6rem .85rem;
        background: #111; border: 1px solid var(--border); border-radius: 1px;
        color: var(--white); font-family: var(--font-body); font-size: .8rem;
        cursor: pointer; transition: border-color .2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%235a5a5a' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .85rem center;
        padding-right: 2.25rem;
    }
    .filter-select:focus { outline: none; border-color: rgba(200,245,66,.4); }
    .filter-select option { background: #1a1a1a; }

    /* Filter apply / reset */
    .filter-actions { display: flex; flex-direction: column; gap: .5rem; margin-top: 1.5rem; }
    .btn-filter-apply {
        width: 100%; padding: .65rem;
        background: var(--acid); color: var(--black); border: none; border-radius: 1px;
        font-family: var(--font-body); font-size: .78rem; font-weight: 700;
        letter-spacing: .1em; text-transform: uppercase; cursor: pointer;
        transition: background .2s;
    }
    .btn-filter-apply:hover { background: #b8e035; }
    .btn-filter-reset {
        width: 100%; padding: .6rem;
        background: transparent; color: var(--mid); border: 1px solid var(--border);
        border-radius: 1px; font-family: var(--font-body); font-size: .78rem;
        letter-spacing: .1em; text-transform: uppercase; cursor: pointer;
        text-decoration: none; text-align: center;
        transition: border-color .2s, color .2s;
    }
    .btn-filter-reset:hover { border-color: var(--mid); color: var(--white); }

    /* Active filters chips */
    .active-filters { display: flex; flex-wrap: wrap; gap: .4rem; margin-bottom: 1.5rem; }
    .filter-chip {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .3rem .65rem; background: rgba(200,245,66,.1);
        border: 1px solid rgba(200,245,66,.25); border-radius: 10px;
        font-size: .68rem; color: var(--acid); letter-spacing: .05em;
        text-decoration: none;
    }
    .filter-chip .chip-x { font-size: .75rem; opacity: .7; }
    .filter-chip:hover { background: rgba(200,245,66,.18); }

    /* ── GRID CONTENT ── */
    .catalog-main { padding: 2rem 0 2rem 2rem; border-left: 1px solid var(--border); }

    /* Top bar */
    .catalog-topbar {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;
    }
    .view-toggle { display: flex; gap: .25rem; }
    .view-btn {
        width: 2rem; height: 2rem; border-radius: 1px;
        background: transparent; border: 1px solid var(--border);
        color: var(--mid); cursor: pointer; display: flex; align-items: center;
        justify-content: center; font-size: .75rem; transition: all .15s;
    }
    .view-btn.active { background: rgba(200,245,66,.1); border-color: rgba(200,245,66,.3); color: var(--acid); }

    /* Grid view */
    .eq-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
    .eq-grid.two-col { grid-template-columns: repeat(2, 1fr); }
    .eq-grid.list-view { grid-template-columns: 1fr; gap: .75rem; }

    /* Equipment card — grid */
    .eq-card {
        background: var(--card); border-radius: 2px; overflow: hidden;
        display: block; text-decoration: none; border: 1px solid var(--border);
        transition: transform .25s, border-color .25s;
    }
    .eq-card:hover { transform: translateY(-4px); border-color: rgba(200,245,66,.2); }

    .eq-card-img { position: relative; aspect-ratio: 4/3; overflow: hidden; }
    .eq-card-img img {
        width: 100%; height: 100%; object-fit: cover; display: block;
        transition: transform .5s; filter: brightness(.82);
    }
    .eq-card:hover .eq-card-img img { transform: scale(1.06); }
    .eq-card-img-ph {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem; background: linear-gradient(145deg, #1c1c1c, #111);
    }
    .eq-card-grad { position: absolute; inset: 0; background: linear-gradient(180deg, transparent 50%, rgba(8,8,8,.55) 100%); }
    .eq-badge {
        position: absolute; top: .75rem; left: .75rem; z-index: 2;
        font-size: .58rem; font-weight: 700; letter-spacing: .1em;
        text-transform: uppercase; padding: .2rem .5rem; border-radius: 1px;
    }
    .eq-badge.popular { background: var(--acid); color: var(--black); }
    .eq-badge.new     { background: #60a5fa; color: var(--black); }
    .eq-badge.hot     { background: #f87171; color: var(--white); }

    .eq-card-body { padding: 1.1rem 1.2rem; }
    .eq-card-cat { font-size: .58rem; letter-spacing: .14em; text-transform: uppercase; color: var(--mid); margin-bottom: .3rem; }
    .eq-card-name { font-family: var(--font-display); font-size: 1.1rem; letter-spacing: .03em; text-transform: uppercase; color: var(--white); margin-bottom: .65rem; line-height: 1.1; }
    .eq-card-meta { display: flex; align-items: flex-end; justify-content: space-between; gap: .5rem; }
    .eq-card-price-lbl { font-size: .58rem; color: var(--mid); }
    .eq-card-price { font-family: var(--font-display); font-size: 1.2rem; color: var(--acid); line-height: 1; }
    .eq-card-rating { font-size: .65rem; color: #fbbf24; display: flex; align-items: center; gap: .2rem; }
    .eq-card-rating span { color: var(--mid); font-size: .6rem; }
    .eq-card-footer { padding: .75rem 1.2rem; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .eq-card-brand { font-size: .65rem; color: var(--mid); }
    .eq-card-condition {
        font-size: .6rem; letter-spacing: .08em; text-transform: uppercase;
        padding: .18rem .45rem; border-radius: 1px;
    }
    .eq-card-condition.good { color: var(--acid); background: rgba(200,245,66,.08); }
    .eq-card-condition.fair { color: #fbbf24; background: rgba(251,191,36,.08); }
    .eq-card-condition.poor { color: #f87171; background: rgba(248,113,113,.08); }

    /* List view card */
    .eq-grid.list-view .eq-card { display: grid; grid-template-columns: 200px 1fr; }
    .eq-grid.list-view .eq-card-img { aspect-ratio: auto; height: 100%; min-height: 130px; }
    .eq-grid.list-view .eq-card-body { padding: 1.25rem 1.4rem; display: flex; flex-direction: column; justify-content: space-between; }
    .eq-grid.list-view .eq-card-name { font-size: 1.25rem; margin-bottom: .5rem; }
    .eq-grid.list-view .eq-card-footer { border-top: none; padding: 0 1.4rem 1.25rem; }

    /* Empty state */
    .catalog-empty {
        grid-column: 1 / -1; text-align: center;
        padding: 5rem 2rem;
    }
    .catalog-empty-ico { font-size: 3rem; margin-bottom: 1rem; opacity: .3; }
    .catalog-empty-title { font-family: var(--font-display); font-size: 2rem; text-transform: uppercase; color: var(--muted); margin-bottom: .75rem; }
    .catalog-empty-desc { font-size: .85rem; color: var(--mid); margin-bottom: 1.5rem; }

    /* Pagination */
    .catalog-pagination {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: 2.5rem; flex-wrap: wrap; gap: 1rem;
    }
    .pag-info { font-size: .75rem; color: var(--mid); }
    .pag-links { display: flex; gap: .3rem; }
    .pag-btn {
        width: 2.2rem; height: 2.2rem; border-radius: 1px;
        display: flex; align-items: center; justify-content: center;
        font-size: .78rem; text-decoration: none;
        border: 1px solid var(--border); color: var(--mid);
        background: transparent; transition: all .15s;
    }
    .pag-btn:hover { border-color: var(--acid); color: var(--acid); }
    .pag-btn.active { background: var(--acid); border-color: var(--acid); color: var(--black); font-weight: 700; }
    .pag-btn.disabled { opacity: .3; pointer-events: none; cursor: default; }

    /* Mobile filter toggle */
    .mobile-filter-toggle {
        display: none; align-items: center; gap: .5rem;
        padding: .6rem 1.2rem; background: transparent;
        border: 1px solid var(--border); border-radius: 1px;
        color: var(--muted); font-family: var(--font-body);
        font-size: .78rem; letter-spacing: .08em; text-transform: uppercase;
        cursor: pointer; transition: border-color .2s, color .2s;
    }
    .mobile-filter-toggle:hover { border-color: var(--acid); color: var(--acid); }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
        .eq-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 900px) {
        .catalog-body { grid-template-columns: 1fr; padding: 0 1.5rem 5rem; }
        .catalog-header { padding: 2.5rem 1.5rem 0; }
        .catalog-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 300px; background: #0d0d0d; z-index: 300;
            padding: 5rem 1.5rem 2rem;
            transform: translateX(-100%);
            transition: transform .3s ease;
            max-height: 100vh; overflow-y: auto;
            border-right: 1px solid var(--border);
        }
        .catalog-sidebar.open { transform: translateX(0); }
        .catalog-main { padding: 1.5rem 0; border-left: none; border-top: 1px solid var(--border); padding-top: 1.5rem; }
        .mobile-filter-toggle { display: flex; }
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(8,8,8,.7); z-index: 299;
            backdrop-filter: blur(4px);
        }
        .sidebar-overlay.open { display: block; }
        .sidebar-close {
            position: absolute; top: 1.5rem; right: 1.25rem;
            background: none; border: none; color: var(--mid);
            font-size: 1.25rem; cursor: pointer; padding: .25rem;
        }
        .eq-grid { grid-template-columns: repeat(2, 1fr); }
        .eq-grid.list-view .eq-card { grid-template-columns: 140px 1fr; }
    }
    @media (max-width: 540px) {
        .eq-grid { grid-template-columns: 1fr; }
        .eq-grid.list-view .eq-card { grid-template-columns: 1fr; }
        .eq-grid.list-view .eq-card-img { min-height: 200px; }
    }
</style>
@endpush

@section('content')
<div class="catalog-page">

    {{-- ── HEADER ── --}}
    <div class="catalog-header">
        <div class="catalog-header-inner">
            <div>
                <div class="catalog-eyebrow">Semua Produk</div>
                <h1 class="catalog-heading">
                    Katalog<br>
                    <span style="color:var(--acid);">Alat Olahraga</span>
                </h1>
            </div>
            <div class="catalog-result-count">
                <strong>{{ $equipment->total() }}</strong> alat ditemukan
                @if(request('category') || request('search'))
                    <div style="margin-top:.25rem;font-size:.7rem;">
                        {{ request('search') ? 'untuk "'.request('search').'"' : '' }}
                        {{ request('category') ? 'di kategori '.request('category') : '' }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar overlay --}}
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

    <div class="catalog-body">

        {{-- ── SIDEBAR ── --}}
        <aside class="catalog-sidebar" id="catalog-sidebar">
            <button class="sidebar-close" onclick="closeSidebar()">✕</button>

            <form method="GET" action="{{ route('catalog') }}" id="filter-form">

                {{-- Search --}}
                <div class="filter-section">
                    <div class="filter-title">Cari Alat</div>
                    <div class="filter-search">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Nama alat, brand…"
                               autocomplete="off">
                    </div>
                </div>

                {{-- Category --}}
                <div class="filter-section">
                    <div class="filter-title">Kategori</div>
                    <div class="cat-filter-list">
                        <button type="button" class="cat-filter-item {{ !request('category') ? 'active' : '' }}"
                                onclick="setCategory('')">
                            <span>
                                <span class="cat-filter-icon">🏅</span>
                                Semua Kategori
                            </span>
                        </button>
                        @foreach($categories as $cat)
                            <button type="button"
                                    class="cat-filter-item {{ request('category') === $cat->slug ? 'active' : '' }}"
                                    onclick="setCategory('{{ $cat->slug }}')">
                                <span>
                                    <span class="cat-filter-icon">{{ $cat->icon ?: '📦' }}</span>
                                    {{ $cat->name }}
                                </span>
                                <span class="cat-filter-count">{{ $cat->equipment_count }}</span>
                            </button>
                        @endforeach
                        <input type="hidden" name="category" id="cat-input" value="{{ request('category') }}">
                    </div>
                </div>

                {{-- Price range --}}
                <div class="filter-section">
                    <div class="filter-title">Harga per Hari (Rp)</div>
                    <div class="price-inputs">
                        <div class="price-input-wrap">
                            <label>Minimum</label>
                            <input type="number" name="price_min" value="{{ request('price_min') }}"
                                   placeholder="0" min="0" step="5000">
                        </div>
                        <div class="price-input-wrap">
                            <label>Maksimum</label>
                            <input type="number" name="price_max" value="{{ request('price_max') }}"
                                   placeholder="∞" min="0" step="5000">
                        </div>
                    </div>
                </div>

                {{-- Sort --}}
                <div class="filter-section">
                    <div class="filter-title">Urutkan</div>
                    <select name="sort" class="filter-select">
                        <option value="popular"    {{ request('sort','popular') === 'popular'    ? 'selected':'' }}>Paling Populer</option>
                        <option value="newest"     {{ request('sort') === 'newest'     ? 'selected':'' }}>Terbaru</option>
                        <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected':'' }}>Harga: Rendah ke Tinggi</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected':'' }}>Harga: Tinggi ke Rendah</option>
                        <option value="rating"     {{ request('sort') === 'rating'     ? 'selected':'' }}>Rating Tertinggi</option>
                    </select>
                </div>

                {{-- Condition --}}
                <div class="filter-section">
                    <div class="filter-title">Kondisi Alat</div>
                    <select name="condition" class="filter-select">
                        <option value="">Semua Kondisi</option>
                        <option value="good" {{ request('condition') === 'good' ? 'selected':'' }}>✓ Baik / Prima</option>
                        <option value="fair" {{ request('condition') === 'fair' ? 'selected':'' }}>⚡ Cukup Baik</option>
                        <option value="poor" {{ request('condition') === 'poor' ? 'selected':'' }}>⚠ Perlu Perhatian</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter-apply">Terapkan Filter</button>
                    @if(request()->hasAny(['search','category','price_min','price_max','sort','condition']))
                        <a href="{{ route('catalog') }}" class="btn-filter-reset">Reset Semua</a>
                    @endif
                </div>

            </form>
        </aside>

        {{-- ── MAIN CONTENT ── --}}
        <div class="catalog-main">

            {{-- Active filters chips --}}
            @if(request()->hasAny(['search','category','price_min','price_max','condition']))
                <div class="active-filters">
                    @if(request('search'))
                        <a href="{{ route('catalog', array_merge(request()->except('search','page'))) }}" class="filter-chip">
                            🔍 "{{ request('search') }}" <span class="chip-x">✕</span>
                        </a>
                    @endif
                    @if(request('category'))
                        <a href="{{ route('catalog', array_merge(request()->except('category','page'))) }}" class="filter-chip">
                            📁 {{ request('category') }} <span class="chip-x">✕</span>
                        </a>
                    @endif
                    @if(request('price_min') || request('price_max'))
                        <a href="{{ route('catalog', array_merge(request()->except('price_min','price_max','page'))) }}" class="filter-chip">
                            💰 Rp {{ number_format(request('price_min'),0,',','.') }} – {{ request('price_max') ? number_format(request('price_max'),0,',','.') : '∞' }}
                            <span class="chip-x">✕</span>
                        </a>
                    @endif
                    @if(request('condition'))
                        <a href="{{ route('catalog', array_merge(request()->except('condition','page'))) }}" class="filter-chip">
                            {{ ['good'=>'✓ Baik','fair'=>'⚡ Cukup','poor'=>'⚠ Perlu Perhatian'][request('condition')] ?? request('condition') }}
                            <span class="chip-x">✕</span>
                        </a>
                    @endif
                </div>
            @endif

            {{-- Top bar --}}
            <div class="catalog-topbar">
                <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                    <button class="mobile-filter-toggle" onclick="openSidebar()">
                        ⚙ Filter & Urutkan
                    </button>
                    <div style="font-size:.78rem;color:var(--mid);">
                        {{ $equipment->firstItem() }}–{{ $equipment->lastItem() }} dari {{ $equipment->total() }} hasil
                    </div>
                </div>
                <div class="view-toggle">
                    <button class="view-btn active" id="btn-grid" onclick="setView('grid')" title="Grid 3 kolom">
                        ⊞
                    </button>
                    <button class="view-btn" id="btn-two" onclick="setView('two')" title="Grid 2 kolom">
                        ▦
                    </button>
                    <button class="view-btn" id="btn-list" onclick="setView('list')" title="List">
                        ☰
                    </button>
                </div>
            </div>

            {{-- Equipment Grid --}}
            @php
                $fallbackImgs = [
                    'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=600&q=75',
                    'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=600&q=75',
                    'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=600&q=75',
                    'https://images.unsplash.com/photo-1519046904884-53103b34b206?w=600&q=75',
                    'https://images.unsplash.com/photo-1452573992436-6d508f200b30?w=600&q=75',
                    'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=75',
                    'https://images.unsplash.com/photo-1522163182402-834f871fd851?w=600&q=75',
                    'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=75',
                ];
            @endphp

            <div class="eq-grid" id="eq-grid">
                @forelse($equipment as $i => $eq)
                    @php
                        $img = $eq->image
                            ? Storage::url($eq->image)
                            : $fallbackImgs[($eq->id + $i) % count($fallbackImgs)];
                        $avgRating = $eq->reviews_avg_rating;
                        $condLabel = ['good'=>'Baik','fair'=>'Cukup','poor'=>'Perlu Perhatian'][$eq->condition] ?? $eq->condition;
                    @endphp
                    <a href="{{ route('equipment.show', $eq) }}" class="eq-card">
                        <div class="eq-card-img">
                            <img src="{{ $img }}" alt="{{ $eq->name }}" loading="lazy">
                            <div class="eq-card-grad"></div>
                            @if($eq->rentals_count >= 10)
                                <div class="eq-badge popular">Populer</div>
                            @elseif($eq->created_at->gt(now()->subDays(14)))
                                <div class="eq-badge new">Baru</div>
                            @elseif($avgRating >= 4.5 && $eq->reviews_count >= 3)
                                <div class="eq-badge hot">Top Rated</div>
                            @endif
                        </div>
                        <div class="eq-card-body">
                            <div class="eq-card-cat">{{ $eq->category->name }}</div>
                            <div class="eq-card-name">{{ $eq->name }}</div>
                            <div class="eq-card-meta">
                                <div>
                                    <div class="eq-card-price-lbl">per hari</div>
                                    <div class="eq-card-price">Rp {{ number_format($eq->price_per_day, 0, ',', '.') }}</div>
                                </div>
                                @if($avgRating)
                                    <div class="eq-card-rating">
                                        ★ {{ number_format($avgRating, 1) }}
                                        <span>({{ $eq->reviews_count }})</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="eq-card-footer">
                            <div class="eq-card-brand">{{ $eq->brand ?: '—' }}</div>
                            <div class="eq-card-condition {{ $eq->condition }}">{{ $condLabel }}</div>
                        </div>
                    </a>
                @empty
                    <div class="catalog-empty">
                        <div class="catalog-empty-ico">🔍</div>
                        <div class="catalog-empty-title">Tidak Ditemukan</div>
                        <p class="catalog-empty-desc">
                            Tidak ada alat yang cocok dengan filter yang kamu pilih.<br>
                            Coba ubah filter atau kata kunci pencarian.
                        </p>
                        <a href="{{ route('catalog') }}" class="btn-filter-apply" style="display:inline-block;padding:.65rem 1.75rem;text-decoration:none;border-radius:1px;">
                            Reset Filter
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($equipment->hasPages())
                <div class="catalog-pagination">
                    <div class="pag-info">
                        Halaman {{ $equipment->currentPage() }} dari {{ $equipment->lastPage() }}
                    </div>
                    <div class="pag-links">
                        @if($equipment->onFirstPage())
                            <span class="pag-btn disabled">‹</span>
                        @else
                            <a href="{{ $equipment->previousPageUrl() }}" class="pag-btn">‹</a>
                        @endif

                        @foreach($equipment->getUrlRange(1, $equipment->lastPage()) as $page => $url)
                            @if(abs($page - $equipment->currentPage()) <= 2 || $page === 1 || $page === $equipment->lastPage())
                                @if($loop->index > 0 && abs($page - array_keys($equipment->getUrlRange(1,$equipment->lastPage()))[$loop->index - 1]) > 1)
                                    <span class="pag-btn disabled" style="border:none;">…</span>
                                @endif
                                <a href="{{ $url }}" class="pag-btn {{ $page == $equipment->currentPage() ? 'active' : '' }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if($equipment->hasMorePages())
                            <a href="{{ $equipment->nextPageUrl() }}" class="pag-btn">›</a>
                        @else
                            <span class="pag-btn disabled">›</span>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // ── Category filter ──────────────────────────────────────────────────────
    function setCategory(slug) {
        document.getElementById('cat-input').value = slug;
        document.querySelectorAll('.cat-filter-item').forEach(btn => btn.classList.remove('active'));
        event.currentTarget.classList.add('active');
    }

    // ── View toggle ──────────────────────────────────────────────────────────
    const grid = document.getElementById('eq-grid');
    const savedView = localStorage.getItem('catalog-view') || 'grid';

    function setView(mode) {
        grid.className = 'eq-grid' + (mode === 'two' ? ' two-col' : mode === 'list' ? ' list-view' : '');
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('btn-' + mode).classList.add('active');
        localStorage.setItem('catalog-view', mode);
    }
    setView(savedView);

    // ── Mobile sidebar ───────────────────────────────────────────────────────
    function openSidebar() {
        document.getElementById('catalog-sidebar').classList.add('open');
        document.getElementById('sidebar-overlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        document.getElementById('catalog-sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('open');
        document.body.style.overflow = '';
    }

    // ── Auto-submit on sort/condition change ─────────────────────────────────
    document.querySelectorAll('.filter-select').forEach(sel => {
        sel.addEventListener('change', () => document.getElementById('filter-form').submit());
    });

    // ── Preserve scroll position ─────────────────────────────────────────────
    window.addEventListener('load', () => {
        const saved = sessionStorage.getItem('catalog-scroll');
        if (saved) { window.scrollTo(0, parseInt(saved)); sessionStorage.removeItem('catalog-scroll'); }
    });
    document.querySelectorAll('.eq-card').forEach(card => {
        card.addEventListener('click', () => sessionStorage.setItem('catalog-scroll', window.scrollY));
    });
</script>
@endpush
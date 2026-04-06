@extends('layouts.home')
@section('content')

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $equipment->name }} — SportRent</title>
    <meta name="description" content="{{ Str::limit($equipment->description, 155) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,300&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        :root {
            --black: #080808; --white: #f4f2ed; --acid: #c8f542;
            --gray: #141414; --card: #111; --mid: #5a5a5a; --muted: #9a9a9a;
            --border: rgba(255,255,255,.07);
            --font-display: 'Bebas Neue', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }
        html { scroll-behavior:smooth; }
        body { background:var(--black); color:var(--white); font-family:var(--font-body); font-weight:300; overflow-x:hidden; }

        /* ── NAV ── */
        nav { position:fixed; top:0; left:0; right:0; z-index:200; display:flex; align-items:center; justify-content:space-between; padding:1.25rem 3rem; background:rgba(8,8,8,.93); backdrop-filter:blur(18px); border-bottom:1px solid var(--border); }
        .nav-logo { font-family:var(--font-display); font-size:2rem; letter-spacing:.06em; color:var(--white); text-decoration:none; }
        .nav-logo span { color:var(--acid); }
        .nav-back { display:flex; align-items:center; gap:.5rem; font-size:.75rem; letter-spacing:.1em; text-transform:uppercase; color:var(--mid); text-decoration:none; transition:color .2s; }
        .nav-back:hover { color:var(--acid); }
        .nav-back svg { transition:transform .2s; }
        .nav-back:hover svg { transform:translateX(-3px); }
        .nav-auth { display:flex; align-items:center; gap:.75rem; }
        .btn-ghost { padding:.45rem 1.1rem; border:1px solid rgba(244,242,237,.2); border-radius:2px; background:transparent; color:var(--white); font-family:var(--font-body); font-size:.78rem; letter-spacing:.06em; text-decoration:none; transition:border-color .2s,color .2s; }
        .btn-ghost:hover { border-color:var(--acid); color:var(--acid); }
        .btn-solid { padding:.45rem 1.3rem; border:1px solid var(--acid); border-radius:2px; background:var(--acid); color:var(--black); font-family:var(--font-body); font-size:.78rem; font-weight:700; letter-spacing:.06em; text-decoration:none; transition:background .2s,color .2s; }
        .btn-solid:hover { background:transparent; color:var(--acid); }

        /* ── HERO BANNER ── */
        .eq-hero {
            position:relative; height:70vh; min-height:520px; overflow:hidden;
            display:flex; align-items:flex-end;
            margin-top:0; /* nav is fixed */
        }
        .eq-hero-img {
            position:absolute; inset:0;
            background-size:cover; background-position:center;
            transition:transform 8s ease;
        }
        .eq-hero:hover .eq-hero-img { transform:scale(1.03); }
        .eq-hero-grad {
            position:absolute; inset:0; z-index:1;
            background:linear-gradient(to top, rgba(8,8,8,1) 0%, rgba(8,8,8,.7) 40%, rgba(8,8,8,.2) 80%, rgba(8,8,8,.05) 100%);
        }
        .eq-hero-grain {
            position:absolute; inset:0; z-index:2; opacity:.18;
            background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            background-size:180px; pointer-events:none;
        }
        .eq-hero-content {
            position:relative; z-index:10;
            width:100%; padding:0 3rem 3.5rem;
        }
        .eq-breadcrumb {
            display:flex; align-items:center; gap:.5rem;
            font-size:.65rem; letter-spacing:.14em; text-transform:uppercase;
            color:rgba(244,242,237,.3); margin-bottom:1.25rem;
        }
        .eq-breadcrumb a { color:rgba(244,242,237,.3); text-decoration:none; transition:color .2s; }
        .eq-breadcrumb a:hover { color:var(--acid); }
        .eq-breadcrumb span { color:rgba(244,242,237,.15); }
        .eq-cat-badge {
            display:inline-flex; align-items:center; gap:.4rem;
            font-size:.62rem; letter-spacing:.18em; text-transform:uppercase;
            color:var(--acid); margin-bottom:.75rem;
        }
        .eq-cat-badge::before { content:''; display:block; width:1.5rem; height:1px; background:var(--acid); }
        .eq-title {
            font-family:var(--font-display);
            font-size:clamp(3rem,7vw,6.5rem);
            line-height:.88; letter-spacing:.02em; text-transform:uppercase;
            margin-bottom:1rem;
        }
        .eq-brand-row {
            display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;
        }
        .eq-brand { font-size:.82rem; color:var(--mid); }
        .eq-brand strong { color:var(--muted); }
        .eq-rating-inline {
            display:flex; align-items:center; gap:.4rem;
            font-size:.82rem; color:#fbbf24;
        }
        .eq-rating-inline span { color:var(--mid); }

        /* ── MAIN LAYOUT ── */
        .main-wrap {
            max-width:1400px; margin:0 auto;
            padding:0 3rem 6rem;
            display:grid;
            grid-template-columns:1fr 380px;
            gap:3rem; align-items:start;
        }

        /* ── LEFT: Gallery + Info ── */
        .eq-gallery { margin-bottom:2.5rem; }
        .gallery-main {
            position:relative; aspect-ratio:16/9;
            overflow:hidden; border-radius:2px;
            background:#111; border:1px solid var(--border);
            cursor:zoom-in; margin-bottom:.75rem;
        }
        .gallery-main img {
            width:100%; height:100%; object-fit:cover; display:block;
            transition:transform .6s ease;
        }
        .gallery-main:hover img { transform:scale(1.03); }
        .gallery-main-ph {
            width:100%; height:100%; display:flex; align-items:center; justify-content:center;
            font-size:6rem; background:linear-gradient(145deg,#1a1a1a,#111);
        }
        .gallery-thumbs {
            display:flex; gap:.6rem; overflow-x:auto; padding-bottom:.25rem;
        }
        .gallery-thumbs::-webkit-scrollbar { height:3px; }
        .gallery-thumbs::-webkit-scrollbar-track { background:transparent; }
        .gallery-thumbs::-webkit-scrollbar-thumb { background:var(--border); border-radius:2px; }
        .g-thumb {
            width:90px; height:65px; flex-shrink:0;
            border-radius:1px; overflow:hidden; cursor:pointer;
            border:1px solid transparent; transition:border-color .2s,opacity .2s;
            opacity:.55;
        }
        .g-thumb.active { border-color:var(--acid); opacity:1; }
        .g-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
        .g-thumb-ph { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:1.5rem; background:#1a1a1a; }

        /* Description */
        .eq-section { margin-bottom:2.5rem; }
        .eq-section-title {
            font-size:.65rem; font-weight:500; letter-spacing:.2em; text-transform:uppercase;
            color:var(--acid); margin-bottom:1rem;
            padding-bottom:.65rem; border-bottom:1px solid var(--border);
        }
        .eq-desc {
            font-size:.92rem; line-height:1.85; color:rgba(244,242,237,.65);
        }

        /* Specs */
        .specs-grid {
            display:grid; grid-template-columns:1fr 1fr; gap:1px;
            background:var(--border); border:1px solid var(--border); border-radius:2px;
        }
        .spec-cell {
            background:var(--card); padding:.9rem 1.1rem;
        }
        .spec-label { font-size:.6rem; letter-spacing:.14em; text-transform:uppercase; color:var(--mid); margin-bottom:.3rem; }
        .spec-val { font-size:.88rem; color:var(--white); }
        .spec-val.good { color:var(--acid); }
        .spec-val.fair { color:#fbbf24; }
        .spec-val.poor { color:#f87171; }

        /* Reviews */
        .reviews-list { display:flex; flex-direction:column; gap:1.25rem; }
        .review-item {
            padding:1.25rem; background:var(--card); border-radius:2px;
            border:1px solid var(--border);
        }
        .review-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.75rem; }
        .review-user { display:flex; align-items:center; gap:.7rem; }
        .review-av {
            width:2.1rem; height:2.1rem; border-radius:50%;
            background:var(--acid); color:var(--black);
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:.82rem; flex-shrink:0;
        }
        .review-name { font-size:.85rem; font-weight:500; color:var(--white); }
        .review-date { font-size:.68rem; color:var(--mid); margin-top:.1rem; }
        .review-stars { color:#fbbf24; font-size:.85rem; letter-spacing:.04em; }
        .review-text { font-size:.84rem; line-height:1.75; color:rgba(244,242,237,.6); font-style:italic; }
        .reviews-empty {
            padding:2.5rem; text-align:center; background:var(--card);
            border:1px dashed var(--border); border-radius:2px;
        }
        .reviews-empty-ico { font-size:2rem; margin-bottom:.5rem; opacity:.3; }
        .reviews-empty-txt { font-size:.82rem; color:var(--mid); }

        /* Related */
        .related-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; }
        .related-card {
            background:var(--card); border-radius:2px; overflow:hidden;
            display:block; text-decoration:none; border:1px solid var(--border);
            transition:border-color .2s,transform .2s;
        }
        .related-card:hover { border-color:rgba(200,245,66,.25); transform:translateY(-3px); }
        .related-img { aspect-ratio:16/9; overflow:hidden; position:relative; }
        .related-img img { width:100%; height:100%; object-fit:cover; display:block; filter:brightness(.75); transition:transform .4s; }
        .related-card:hover .related-img img { transform:scale(1.05); }
        .related-img-ph { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:2.2rem; background:#1a1a1a; }
        .related-info { padding:1rem 1.1rem; }
        .related-cat { font-size:.58rem; letter-spacing:.14em; text-transform:uppercase; color:var(--mid); margin-bottom:.25rem; }
        .related-name { font-family:var(--font-display); font-size:1.1rem; text-transform:uppercase; color:var(--white); margin-bottom:.35rem; line-height:1.1; }
        .related-price { font-size:.75rem; color:var(--acid); }

        /* ── RIGHT: Booking Card (sticky) ── */
        .booking-card {
            position:sticky; top:calc(70px + 1.5rem);
            background:var(--card); border:1px solid var(--border); border-radius:2px;
        }
        .booking-header {
            padding:1.5rem; border-bottom:1px solid var(--border);
        }
        .booking-price-lbl { font-size:.6rem; letter-spacing:.15em; text-transform:uppercase; color:var(--mid); margin-bottom:.2rem; }
        .booking-price {
            font-family:var(--font-display); font-size:2.8rem;
            color:var(--acid); line-height:1; letter-spacing:.02em;
        }
        .booking-price sub { font-size:1rem; color:var(--mid); font-family:var(--font-body); font-weight:300; }
        .booking-rating {
            display:flex; align-items:center; gap:.5rem; margin-top:.6rem;
        }
        .booking-stars { color:#fbbf24; font-size:.85rem; }
        .booking-rating-val { font-size:.82rem; font-weight:500; color:var(--white); }
        .booking-rating-cnt { font-size:.75rem; color:var(--mid); }

        /* Stock badge */
        .stock-badge {
            display:inline-flex; align-items:center; gap:.4rem;
            font-size:.68rem; font-weight:500; letter-spacing:.08em; text-transform:uppercase;
            padding:.3rem .7rem; border-radius:1px; margin-top:.65rem;
        }
        .stock-badge.in { background:rgba(200,245,66,.1); color:var(--acid); border:1px solid rgba(200,245,66,.2); }
        .stock-badge.low { background:rgba(251,191,36,.1); color:#fbbf24; border:1px solid rgba(251,191,36,.2); }
        .stock-badge.out { background:rgba(248,113,113,.1); color:#f87171; border:1px solid rgba(248,113,113,.2); }

        .booking-body { padding:1.5rem; display:flex; flex-direction:column; gap:1rem; }

        /* Date inputs */
        .date-row { display:grid; grid-template-columns:1fr 1fr; gap:.6rem; }
        .date-field label {
            display:block; font-size:.6rem; letter-spacing:.14em; text-transform:uppercase;
            color:var(--mid); margin-bottom:.35rem;
        }
        .date-field input {
            width:100%; padding:.65rem .85rem;
            background:#1a1a1a; border:1px solid var(--border); border-radius:1px;
            color:var(--white); font-family:var(--font-body); font-size:.82rem;
            transition:border-color .2s;
            color-scheme:dark;
        }
        .date-field input:focus { outline:none; border-color:rgba(200,245,66,.4); }

        /* Qty */
        .qty-row { display:flex; align-items:center; justify-content:space-between; }
        .qty-label { font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:var(--mid); }
        .qty-ctrl { display:flex; align-items:center; gap:.75rem; }
        .qty-btn {
            width:2rem; height:2rem; border-radius:1px;
            background:#1a1a1a; border:1px solid var(--border); color:var(--white);
            display:flex; align-items:center; justify-content:center;
            cursor:pointer; font-size:1rem; transition:border-color .2s,color .2s;
        }
        .qty-btn:hover { border-color:var(--acid); color:var(--acid); }
        .qty-num { font-family:var(--font-display); font-size:1.4rem; color:var(--white); min-width:1.5rem; text-align:center; }

        /* Summary */
        .booking-summary {
            padding:1rem; background:#1a1a1a; border-radius:1px;
            display:flex; flex-direction:column; gap:.5rem;
        }
        .sum-row { display:flex; justify-content:space-between; font-size:.8rem; }
        .sum-row span:first-child { color:var(--mid); }
        .sum-row span:last-child { color:var(--white); }
        .sum-divider { height:1px; background:var(--border); }
        .sum-total { display:flex; justify-content:space-between; align-items:baseline; }
        .sum-total span:first-child { font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:var(--mid); }
        .sum-total-val { font-family:var(--font-display); font-size:1.6rem; color:var(--acid); }

        /* CTA button */
        .btn-book {
            display:block; width:100%; padding:.95rem;
            background:var(--acid); color:var(--black); border:none;
            border-radius:2px; font-family:var(--font-body);
            font-size:.88rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
            text-align:center; text-decoration:none; cursor:pointer;
            transition:transform .15s, box-shadow .15s, background .2s;
        }
        .btn-book:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(200,245,66,.3); }
        .btn-book:disabled, .btn-book.disabled {
            background:#2a2a2a; color:var(--mid); cursor:not-allowed;
            transform:none; box-shadow:none; border:1px solid var(--border);
        }
        .btn-login-hint {
            display:block; text-align:center; font-size:.75rem; color:var(--mid);
            margin-top:.5rem;
        }
        .btn-login-hint a { color:var(--acid); text-decoration:none; }

        /* Trust badges */
        .trust-row {
            padding:1rem 1.5rem; border-top:1px solid var(--border);
            display:flex; flex-direction:column; gap:.5rem;
        }
        .trust-item { display:flex; align-items:center; gap:.6rem; font-size:.75rem; color:var(--mid); }
        .trust-item span:first-child { font-size:.95rem; }

        /* ── ANIMATIONS ── */
        .fade-up { opacity:0; transform:translateY(24px); transition:opacity .65s ease,transform .65s ease; }
        .fade-up.in { opacity:1; transform:translateY(0); }
        .d1{transition-delay:.1s} .d2{transition-delay:.2s} .d3{transition-delay:.3s} .d4{transition-delay:.4s}

        /* ── RESPONSIVE ── */
        @media(max-width:1100px) {
            .main-wrap { grid-template-columns:1fr; gap:2rem; padding:0 2rem 5rem; }
            .booking-card { position:static; }
        }
        @media(max-width:768px) {
            nav { padding:1rem 1.5rem; }
            .eq-hero-content { padding:0 1.5rem 2.5rem; }
            .main-wrap { padding:0 1.5rem 4rem; }
            .related-grid { grid-template-columns:1fr; }
            .eq-title { font-size:clamp(2.5rem,12vw,4rem); }
            .specs-grid { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a href="{{ route('home') }}" class="nav-logo">Sport<span>Rent</span></a>
    <a href="{{ route('catalog') }}" class="nav-back">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M12 7H2M7 12L2 7l5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Kembali ke Katalog
    </a>
    <div class="nav-auth">
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-ghost">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-solid">Daftar</a>
                @endif
            @endauth
        @endif
    </div>
</nav>

<!-- HERO BANNER -->
@php
    $fallbackImgs = [
        'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=1800&q=80',
        'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1800&q=80',
        'https://images.unsplash.com/photo-1452573992436-6d508f200b30?w=1800&q=80',
        'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=1800&q=80',
        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1800&q=80',
        'https://images.unsplash.com/photo-1519046904884-53103b34b206?w=1800&q=80',
    ];
    $heroBg = $equipment->image
        ? Storage::url($equipment->image)
        : $fallbackImgs[$equipment->id % count($fallbackImgs)];
    $avgRating = round($equipment->reviews->avg('rating') ?? 0, 1);
    $reviewCount = $equipment->reviews->count();
@endphp

<div class="eq-hero" style="padding-top:0;">
    <div class="eq-hero-img" style="background-image:url('{{ $heroBg }}');"></div>
    <div class="eq-hero-grad"></div>
    <div class="eq-hero-grain"></div>
    <div class="eq-hero-content">
        <div class="eq-breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('catalog') }}">Katalog</a>
            <span>/</span>
            <a href="{{ route('catalog', ['category' => $equipment->category->slug]) }}">{{ $equipment->category->name }}</a>
            <span>/</span>
            <span style="color:rgba(244,242,237,.55);">{{ Str::limit($equipment->name, 30) }}</span>
        </div>
        <div class="eq-cat-badge">{{ $equipment->category->icon ?? '🏅' }} {{ $equipment->category->name }}</div>
        <h1 class="eq-title">{{ $equipment->name }}</h1>
        <div class="eq-brand-row">
            @if($equipment->brand)
                <div class="eq-brand">Brand: <strong>{{ $equipment->brand }}</strong></div>
            @endif
            @if($avgRating > 0)
                <div class="eq-rating-inline">
                    @for($s=1;$s<=5;$s++)<span style="color:{{ $s<=$avgRating?'#fbbf24':'rgba(251,191,36,.25)' }}">★</span>@endfor
                    &nbsp;{{ $avgRating }} <span>({{ $reviewCount }} ulasan)</span>
                </div>
            @endif
            @if($equipment->rentals_count > 0)
                <div class="eq-brand" style="color:var(--mid);">{{ $equipment->rentals_count }}× disewa</div>
            @endif
        </div>
    </div>
</div>

<!-- MAIN -->
<div class="main-wrap">

    <!-- ── LEFT COLUMN ── -->
    <div>

        {{-- Gallery --}}
        <div class="eq-gallery fade-up">
            <div class="gallery-main" id="gallery-main" onclick="openLightbox()">
                @if($equipment->image)
                    <img src="{{ Storage::url($equipment->image) }}" alt="{{ $equipment->name }}" id="gallery-main-img">
                @else
                    <div class="gallery-main-ph">{{ $equipment->category->icon ?? '🏅' }}</div>
                @endif
                @if($equipment->image)
                    <div style="position:absolute;bottom:1rem;right:1rem;background:rgba(8,8,8,.65);backdrop-filter:blur(6px);padding:.35rem .7rem;border-radius:1px;font-size:.65rem;letter-spacing:.1em;text-transform:uppercase;color:var(--white);z-index:5;">
                        🔍 Perbesar
                    </div>
                @endif
            </div>
            {{-- Thumbnails (show extra angles using Unsplash variations) --}}
            @if($equipment->image)
                @php
                    $thumbImgs = [
                        Storage::url($equipment->image),
                        $fallbackImgs[($equipment->id + 1) % count($fallbackImgs)],
                        $fallbackImgs[($equipment->id + 2) % count($fallbackImgs)],
                        $fallbackImgs[($equipment->id + 3) % count($fallbackImgs)],
                    ];
                @endphp
                <!-- <div class="gallery-thumbs">
                    @foreach($thumbImgs as $ti => $thumb)
                        <div class="g-thumb {{ $ti === 0 ? 'active' : '' }}" onclick="setThumb('{{ $thumb }}', this)">
                            <img src="{{ $thumb }}" alt="Foto {{ $ti+1 }}" loading="lazy">
                        </div>
                    @endforeach
                </div> -->
            @else
                @php
                    $thumbVariants = array_slice($fallbackImgs, $equipment->id % count($fallbackImgs));
                    $thumbVariants = array_merge($thumbVariants, $fallbackImgs);
                @endphp
                <div class="gallery-thumbs">
                    @foreach(array_slice($thumbVariants, 0, 4) as $ti => $thumb)
                        <div class="g-thumb {{ $ti === 0 ? 'active' : '' }}" onclick="setThumbUrl('{{ $thumb }}', this)">
                            <img src="{{ $thumb }}" alt="Foto {{ $ti+1 }}" loading="lazy">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Deskripsi --}}
        <div class="eq-section fade-up d1">
            <div class="eq-section-title">Deskripsi</div>
            @if($equipment->description)
                <p class="eq-desc">{{ $equipment->description }}</p>
            @else
                <p class="eq-desc" style="color:var(--mid);font-style:italic;">Deskripsi belum tersedia untuk produk ini.</p>
            @endif
        </div>

        {{-- Spesifikasi --}}
        <div class="eq-section fade-up d2">
            <div class="eq-section-title">Spesifikasi & Detail</div>
            <div class="specs-grid">
                <div class="spec-cell">
                    <div class="spec-label">Kategori</div>
                    <div class="spec-val">{{ $equipment->category->icon ?? '' }} {{ $equipment->category->name }}</div>
                </div>
                @if($equipment->brand)
                    <div class="spec-cell">
                        <div class="spec-label">Brand / Merek</div>
                        <div class="spec-val">{{ $equipment->brand }}</div>
                    </div>
                @endif
                <div class="spec-cell">
                    <div class="spec-label">Kondisi Alat</div>
                    <div class="spec-val {{ $equipment->condition === 'good' ? 'good' : ($equipment->condition === 'fair' ? 'fair' : 'poor') }}">
                        @if($equipment->condition === 'good') ✓ Baik / Prima
                        @elseif($equipment->condition === 'fair') ⚡ Cukup Baik
                        @else ⚠ Perlu Perhatian
                        @endif
                    </div>
                </div>
                <div class="spec-cell">
                    <div class="spec-label">Stok Tersedia</div>
                    <div class="spec-val {{ $equipment->stock > 3 ? 'good' : ($equipment->stock > 0 ? 'fair' : 'poor') }}">
                        {{ $equipment->stock }} unit
                    </div>
                </div>
                <div class="spec-cell">
                    <div class="spec-label">Harga per Hari</div>
                    <div class="spec-val good" style="font-family:var(--font-display);font-size:1.1rem;">
                        Rp {{ number_format($equipment->price_per_day, 0, ',', '.') }}
                    </div>
                </div>
                <div class="spec-cell">
                    <div class="spec-label">Total Disewa</div>
                    <div class="spec-val">{{ $equipment->rentals_count }} kali</div>
                </div>
                @if($avgRating > 0)
                    <div class="spec-cell">
                        <div class="spec-label">Rating Rata-rata</div>
                        <div class="spec-val" style="color:#fbbf24;">{{ $avgRating }} / 5 ★</div>
                    </div>
                @endif
                <div class="spec-cell">
                    <div class="spec-label">Status</div>
                    <div class="spec-val {{ $equipment->is_available && $equipment->stock > 0 ? 'good' : 'poor' }}">
                        {{ $equipment->is_available && $equipment->stock > 0 ? '● Tersedia' : '● Tidak Tersedia' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Ulasan --}}
        <div class="eq-section fade-up d3">
            <div class="eq-section-title" style="display:flex;align-items:center;justify-content:space-between;">
                <span>Ulasan Penyewa</span>
                @if($reviewCount > 0)
                    <span style="font-family:var(--font-display);font-size:1.4rem;color:#fbbf24;letter-spacing:.02em;">
                        {{ $avgRating }} <span style="font-size:.9rem;color:var(--mid);font-family:var(--font-body);">/ 5</span>
                    </span>
                @endif
            </div>

            @if($reviewCount > 0)
                {{-- Rating bar summary --}}
                <div style="display:flex;gap:2rem;padding:1.25rem;background:var(--card);border:1px solid var(--border);border-radius:2px;margin-bottom:1.5rem;">
                    <div style="text-align:center;flex-shrink:0;">
                        <div style="font-family:var(--font-display);font-size:3.5rem;color:#fbbf24;line-height:1;">{{ $avgRating }}</div>
                        <div style="font-size:1rem;color:#fbbf24;letter-spacing:.06em;margin:.25rem 0;">
                            @for($s=1;$s<=5;$s++)<span style="color:{{ $s<=$avgRating?'#fbbf24':'rgba(251,191,36,.2)' }}">★</span>@endfor
                        </div>
                        <div style="font-size:.68rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;">{{ $reviewCount }} ulasan</div>
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;justify-content:center;gap:.4rem;">
                        @php
                            $ratingDist = $equipment->reviews->groupBy('rating')->map->count();
                            $maxR = max($ratingDist->values()->toArray() ?: [1]);
                        @endphp
                        @for($r=5;$r>=1;$r--)
                            @php $cnt = $ratingDist[$r] ?? 0; @endphp
                            <div style="display:flex;align-items:center;gap:.6rem;">
                                <span style="font-size:.65rem;color:#fbbf24;width:.6rem;">{{ $r }}</span>
                                <span style="font-size:.55rem;color:#fbbf24;">★</span>
                                <div style="flex:1;height:5px;background:rgba(255,255,255,.06);border-radius:3px;overflow:hidden;">
                                    <div style="height:100%;border-radius:3px;background:{{ $r>=4?'#fbbf24':($r===3?'rgba(251,191,36,.5)':'rgba(248,113,113,.5)') }};width:{{ $cnt?round($cnt/$maxR*100):0 }}%;transition:width .6s;"></div>
                                </div>
                                <span style="font-size:.65rem;color:var(--mid);width:1.2rem;text-align:right;">{{ $cnt }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="reviews-list">
                    @foreach($equipment->reviews->take(5) as $review)
                        <div class="review-item">
                            <div class="review-header">
                                <div class="review-user">
                                    <div class="review-av">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>
                                    <div>
                                        <div class="review-name">{{ $review->user->name }}</div>
                                        <div class="review-date">{{ $review->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="review-stars">
                                    @for($s=1;$s<=5;$s++)<span style="color:{{ $s<=$review->rating?'#fbbf24':'rgba(251,191,36,.2)' }}">★</span>@endfor
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="review-text">"{{ $review->comment }}"</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($reviewCount > 5)
                    <div style="margin-top:1rem;text-align:center;">
                        <button onclick="toggleAllReviews()" id="review-toggle"
                                style="background:transparent;border:1px solid var(--border);border-radius:1px;padding:.6rem 1.4rem;color:var(--mid);font-family:var(--font-body);font-size:.75rem;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;transition:border-color .2s,color .2s;">
                            Lihat Semua {{ $reviewCount }} Ulasan ↓
                        </button>
                    </div>
                    <div id="extra-reviews" style="display:none;margin-top:1.25rem;">
                        <div class="reviews-list">
                            @foreach($equipment->reviews->slice(5) as $review)
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="review-user">
                                            <div class="review-av">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>
                                            <div>
                                                <div class="review-name">{{ $review->user->name }}</div>
                                                <div class="review-date">{{ $review->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                        <div class="review-stars">
                                            @for($s=1;$s<=5;$s++)<span style="color:{{ $s<=$review->rating?'#fbbf24':'rgba(251,191,36,.2)' }}">★</span>@endfor
                                        </div>
                                    </div>
                                    @if($review->comment)
                                        <p class="review-text">"{{ $review->comment }}"</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="reviews-empty">
                    <div class="reviews-empty-ico">💬</div>
                    <div class="reviews-empty-txt">Belum ada ulasan untuk alat ini.<br>Jadilah yang pertama menyewa dan memberikan ulasan!</div>
                </div>
            @endif
        </div>

        {{-- Alat Terkait --}}
        @if($related->isNotEmpty())
            <div class="eq-section fade-up d4">
                <div class="eq-section-title">Alat Terkait</div>
                <div class="related-grid">
                    @foreach($related as $ri => $rel)
                        @php
                            $relImg = $rel->image
                                ? Storage::url($rel->image)
                                : $fallbackImgs[($rel->id + $ri) % count($fallbackImgs)];
                        @endphp
                        <a href="{{ route('equipment.show', $rel) }}" class="related-card">
                            <div class="related-img">
                                <img src="{{ $relImg }}" alt="{{ $rel->name }}" loading="lazy">
                            </div>
                            <div class="related-info">
                                <div class="related-cat">{{ $rel->category->name }}</div>
                                <div class="related-name">{{ $rel->name }}</div>
                                <div class="related-price">Rp {{ number_format($rel->price_per_day, 0, ',', '.') }}/hari</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- ── RIGHT: BOOKING CARD ── -->
    <div>
        <div class="booking-card fade-up">
            <div class="booking-header">
                <div class="booking-price-lbl">Harga Sewa</div>
                <div class="booking-price">
                    Rp {{ number_format($equipment->price_per_day, 0, ',', '.') }}<sub>/hari</sub>
                </div>
                @if($avgRating > 0)
                    <div class="booking-rating">
                        <div class="booking-stars">
                            @for($s=1;$s<=5;$s++)<span style="color:{{ $s<=$avgRating?'#fbbf24':'rgba(251,191,36,.2)' }}">★</span>@endfor
                        </div>
                        <span class="booking-rating-val">{{ $avgRating }}</span>
                        <span class="booking-rating-cnt">({{ $reviewCount }} ulasan)</span>
                    </div>
                @endif

                @if(!$equipment->is_available || $equipment->stock <= 0)
                    <div class="stock-badge out">● Stok Habis</div>
                @elseif($equipment->stock <= 3)
                    <div class="stock-badge low">⚡ Stok Terbatas — {{ $equipment->stock }} unit</div>
                @else
                    <div class="stock-badge in">✓ Tersedia — {{ $equipment->stock }} unit</div>
                @endif
            </div>

            @if($equipment->is_available && $equipment->stock > 0)
                <div class="booking-body">

                    {{-- Date picker --}}
                    <div class="date-row">
                        <div class="date-field">
                            <label>Tanggal Mulai</label>
                            <input type="date" id="start-date" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   onchange="recalc()">
                        </div>
                        <div class="date-field">
                            <label>Tanggal Selesai</label>
                            <input type="date" id="end-date" min="{{ date('Y-m-d', strtotime('+2 days')) }}"
                                   value="{{ date('Y-m-d', strtotime('+3 days')) }}"
                                   onchange="recalc()">
                        </div>
                    </div>

                    {{-- Quantity --}}
                    <div class="qty-row">
                        <span class="qty-label">Jumlah Unit</span>
                        <div class="qty-ctrl">
                            <div class="qty-btn" onclick="changeQty(-1)">−</div>
                            <div class="qty-num" id="qty-display">1</div>
                            <div class="qty-btn" onclick="changeQty(1)">+</div>
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="booking-summary" id="booking-summary">
                        <div class="sum-row">
                            <span>Harga per hari</span>
                            <span>Rp {{ number_format($equipment->price_per_day, 0, ',', '.') }}</span>
                        </div>
                        <div class="sum-row">
                            <span>Durasi</span>
                            <span id="sum-duration">2 hari</span>
                        </div>
                        <div class="sum-row">
                            <span>Jumlah unit</span>
                            <span id="sum-qty">1 unit</span>
                        </div>
                        <div class="sum-divider"></div>
                        <div class="sum-total">
                            <span>Total</span>
                            <span class="sum-total-val" id="sum-total">Rp {{ number_format($equipment->price_per_day * 2, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- CTA --}}
                    @auth
                        <a href="{{ route('rentals.create', ['equipment_id' => $equipment->id]) }}"
                           id="book-btn" class="btn-book">
                            Sewa Sekarang
                        </a>
                    @else
                        <a href="{{ route('login') }}?redirect={{ route('equipment.show', $equipment) }}"
                           class="btn-book">
                            Masuk untuk Menyewa
                        </a>
                        <span class="btn-login-hint">
                            Belum punya akun? <a href="{{ route('register') }}">Daftar gratis</a>
                        </span>
                    @endauth

                </div>
            @else
                <div class="booking-body">
                    <div class="btn-book disabled">Stok Tidak Tersedia</div>
                    <div style="text-align:center;font-size:.75rem;color:var(--mid);margin-top:.5rem;">
                        Alat ini sedang tidak tersedia. Coba lagi nanti atau lihat alat serupa di bawah.
                    </div>
                </div>
            @endif

            {{-- Trust badges --}}
            <div class="trust-row">
                <div class="trust-item"><span>✓</span> Gratis batalkan 24 jam sebelum sewa</div>
                <div class="trust-item"><span>📦</span> Pengiriman ke lokasi Anda</div>
                <div class="trust-item"><span>🛡</span> Alat bersih & terawat</div>
                <div class="trust-item"><span>💬</span> Bantuan 24/7</div>
            </div>
        </div>

        {{-- Share / info mini card --}}
        <div style="margin-top:1rem;padding:1rem 1.25rem;background:var(--card);border:1px solid var(--border);border-radius:2px;">
            <div style="font-size:.6rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.65rem;">Bagikan</div>
            <div style="display:flex;gap:.5rem;">
                @php $shareUrl = urlencode(route('equipment.show', $equipment)); $shareText = urlencode('Cek alat olahraga ini di SportRent: '.$equipment->name); @endphp
                <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank"
                   style="display:flex;align-items:center;gap:.4rem;padding:.45rem .85rem;background:#1a1a1a;border:1px solid var(--border);border-radius:1px;font-size:.7rem;color:var(--muted);text-decoration:none;transition:border-color .2s,color .2s;"
                   onmouseover="this.style.borderColor='rgba(200,245,66,.3)';this.style.color='var(--white)'"
                   onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
                    📱 WhatsApp
                </a>
                <button onclick="copyLink()" id="copy-btn"
                        style="display:flex;align-items:center;gap:.4rem;padding:.45rem .85rem;background:#1a1a1a;border:1px solid var(--border);border-radius:1px;font-size:.7rem;color:var(--muted);cursor:pointer;font-family:var(--font-body);transition:border-color .2s,color .2s;"
                        onmouseover="this.style.borderColor='rgba(200,245,66,.3)';this.style.color='var(--white)'"
                        onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
                    🔗 Salin Link
                </button>
            </div>
        </div>
    </div>

</div>

{{-- Lightbox --}}
<div id="lightbox" onclick="closeLightbox()"
     style="display:none;position:fixed;inset:0;z-index:999;background:rgba(8,8,8,.96);backdrop-filter:blur(10px);align-items:center;justify-content:center;cursor:zoom-out;">
    <img id="lightbox-img" src="" alt=""
         style="max-width:90vw;max-height:88vh;object-fit:contain;border-radius:2px;box-shadow:0 32px 80px rgba(0,0,0,.8);">
    <div onclick="closeLightbox()"
         style="position:absolute;top:1.5rem;right:2rem;font-size:1.5rem;color:rgba(244,242,237,.5);cursor:pointer;transition:color .2s;"
         onmouseover="this.style.color='var(--white)'"
         onmouseout="this.style.color='rgba(244,242,237,.5)'">✕</div>
</div>

<script>
    // ── Gallery ──────────────────────────────────────────────────────────────
    const mainImg    = document.getElementById('gallery-main-img');
    const lightboxEl = document.getElementById('lightbox');
    const lightboxImg= document.getElementById('lightbox-img');

    function setThumb(src, el) {
        if (mainImg) mainImg.src = src;
        document.querySelectorAll('.g-thumb').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }
    function setThumbUrl(src, el) {
        // For placeholder mode, inject an img element
        const wrap = document.getElementById('gallery-main');
        let img = wrap.querySelector('img');
        if (!img) {
            wrap.innerHTML = `<img id="gallery-main-img" src="${src}" alt="" style="width:100%;height:100%;object-fit:cover;display:block;transition:transform .6s ease;">`;
        } else {
            img.src = src;
        }
        document.querySelectorAll('.g-thumb').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    function openLightbox() {
        const src = mainImg ? mainImg.src : null;
        if (!src) return;
        lightboxImg.src = src;
        lightboxEl.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
        lightboxEl.style.display = 'none';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if(e.key==='Escape') closeLightbox(); });

    // ── Booking calculator ───────────────────────────────────────────────────
    const pricePerDay = {{ (float)$equipment->price_per_day }};
    const maxStock    = {{ $equipment->stock }};
    let qty = 1;

    function changeQty(delta) {
        qty = Math.max(1, Math.min(maxStock, qty + delta));
        document.getElementById('qty-display').textContent = qty;
        recalc();
    }

    function recalc() {
        const s = document.getElementById('start-date');
        const e = document.getElementById('end-date');
        if (!s || !e) return;

        const startVal = s.value, endVal = e.value;

        // Enforce end > start
        if (endVal && startVal && endVal <= startVal) {
            const next = new Date(startVal);
            next.setDate(next.getDate() + 1);
            e.value = next.toISOString().split('T')[0];
        }

        let days = 0;
        if (startVal && endVal) {
            const diff = (new Date(endVal) - new Date(startVal)) / 86400000;
            days = diff > 0 ? Math.round(diff) : 0;
        }

        const total = pricePerDay * days * qty;

        const durEl   = document.getElementById('sum-duration');
        const qtyEl   = document.getElementById('sum-qty');
        const totalEl = document.getElementById('sum-total');

        if (durEl)   durEl.textContent   = days ? `${days} hari` : '—';
        if (qtyEl)   qtyEl.textContent   = `${qty} unit`;
        if (totalEl) totalEl.textContent = days ? 'Rp ' + total.toLocaleString('id-ID') : '—';

        // Update book button href
        const btn = document.getElementById('book-btn');
        if (btn && days > 0) {
            const base = btn.href.split('?')[0];
            btn.href = `${base}?equipment_id={{ $equipment->id }}&start_date=${startVal}&end_date=${endVal}&quantity=${qty}`;
        }
    }

    // ── Reviews toggle ───────────────────────────────────────────────────────
    function toggleAllReviews() {
        const el  = document.getElementById('extra-reviews');
        const btn = document.getElementById('review-toggle');
        const open = el.style.display === 'none';
        el.style.display = open ? 'block' : 'none';
        btn.textContent  = open ? 'Sembunyikan ↑' : 'Lihat Semua {{ $reviewCount }} Ulasan ↓';
    }

    // ── Copy link ────────────────────────────────────────────────────────────
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            const btn = document.getElementById('copy-btn');
            btn.textContent = '✓ Tersalin!';
            btn.style.color = 'var(--acid)';
            setTimeout(() => { btn.textContent = '🔗 Salin Link'; btn.style.color = 'var(--muted)'; }, 2200);
        });
    }

    // ── Scroll fade-up ───────────────────────────────────────────────────────
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('in'); });
    }, { threshold:.1 });
    document.querySelectorAll('.fade-up').forEach(el => io.observe(el));

    // ── Init ─────────────────────────────────────────────────────────────────
    recalc();
</script>

</body>
</html>
@endsection
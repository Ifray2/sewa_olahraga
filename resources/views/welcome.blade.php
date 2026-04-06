@extends('layouts.home')

@section('title', 'SportRent — Sewa Alat Olahraga Premium')
@section('description', 'Platform sewa alat olahraga terpercaya. MTB, surfing, camping, panjat tebing — semua tersedia.')

{{-- Marquee shown on this page --}}
@section('show-marquee', true)

@push('styles')
<style>
    /* ── HERO ── */
    .hero { position:relative; height:100vh; min-height:640px; display:flex; align-items:flex-end; overflow:hidden; }
    .hero-bg { position:absolute; inset:0; background-size:cover; background-position:center; opacity:0; transition:opacity 1.5s ease; }
    .hero-bg.active { opacity:1; }
    .hero-bg:nth-child(1) { background-image:url('https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=1800&q=80'); }
    .hero-bg:nth-child(2) { background-image:url('https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1800&q=80'); }
    .hero-bg:nth-child(3) { background-image:url('https://images.unsplash.com/photo-1452573992436-6d508f200b30?w=1800&q=80'); }
    .hero-bg:nth-child(4) { background-image:url('https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=1800&q=80'); }
    .hero-overlay { position:absolute; inset:0; z-index:1; background:linear-gradient(to top, rgba(8,8,8,1) 0%, rgba(8,8,8,.7) 35%, rgba(8,8,8,.25) 70%, rgba(8,8,8,.1) 100%); }
    .hero-grain { position:absolute; inset:0; z-index:2; opacity:.2; background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); background-size:180px; pointer-events:none; }
    .hero-content { position:relative; z-index:10; width:100%; padding:0 3rem 5rem; display:grid; grid-template-columns:1fr 1fr; gap:4rem; align-items:flex-end; }
    .hero-eyebrow { display:inline-flex; align-items:center; gap:.6rem; font-size:.7rem; font-weight:500; letter-spacing:.22em; text-transform:uppercase; color:var(--acid); margin-bottom:1.25rem; }
    .hero-eyebrow::before { content:''; display:block; width:2.5rem; height:1px; background:var(--acid); }
    .hero-title { font-family:var(--font-display); font-size:clamp(4.5rem,9vw,9rem); line-height:.88; letter-spacing:.02em; text-transform:uppercase; }
    .hero-title .accent { color:var(--acid); display:block; }
    .hero-title .outline { display:block; -webkit-text-stroke:1.5px rgba(244,242,237,.35); color:transparent; }
    .hero-right { display:flex; flex-direction:column; justify-content:flex-end; gap:2rem; }
    .hero-desc { font-size:.98rem; line-height:1.8; color:rgba(244,242,237,.6); max-width:380px; }
    .hero-cta { display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
    .btn-hp { display:inline-flex; align-items:center; gap:.6rem; padding:.9rem 2rem; background:var(--acid); color:var(--black); border:none; border-radius:2px; font-family:var(--font-body); font-size:.88rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; text-decoration:none; cursor:pointer; transition:transform .15s,box-shadow .15s; }
    .btn-hp:hover { transform:translateY(-2px); box-shadow:0 12px 32px rgba(200,245,66,.3); }
    .btn-hg { display:inline-flex; align-items:center; gap:.6rem; padding:.9rem 1.8rem; background:transparent; color:var(--white); border:1px solid rgba(244,242,237,.25); border-radius:2px; font-family:var(--font-body); font-size:.88rem; letter-spacing:.08em; text-transform:uppercase; text-decoration:none; transition:border-color .2s; }
    .btn-hg:hover { border-color:var(--white); }
    .hero-stats { display:flex; gap:2.5rem; padding-top:1.75rem; border-top:1px solid rgba(244,242,237,.1); }
    .h-stat-num { font-family:var(--font-display); font-size:2.2rem; color:var(--acid); line-height:1; }
    .h-stat-lbl { font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:var(--mid); margin-top:.2rem; }
    .hero-dots { position:absolute; bottom:2.5rem; left:50%; transform:translateX(-50%); z-index:15; display:flex; gap:.5rem; }
    .hero-dot { width:5px; height:5px; border-radius:50%; background:rgba(244,242,237,.3); cursor:pointer; transition:background .3s,transform .3s; }
    .hero-dot.active { background:var(--acid); transform:scale(1.4); }
    .slide-labels { position:absolute; top:50%; right:3rem; transform:translateY(-50%); z-index:15; display:flex; flex-direction:column; gap:.85rem; }
    .slbl { font-size:.6rem; letter-spacing:.18em; text-transform:uppercase; color:rgba(244,242,237,.25); cursor:pointer; padding-left:1rem; border-left:1px solid transparent; transition:color .2s,border-color .2s; }
    .slbl.active { color:var(--acid); border-left-color:var(--acid); }
    .scroll-hint { position:absolute; bottom:2.5rem; right:3rem; z-index:15; display:flex; flex-direction:column; align-items:center; gap:.4rem; }
    .scroll-line { width:1px; height:3rem; background:rgba(244,242,237,.15); position:relative; overflow:hidden; }
    .scroll-line::after { content:''; position:absolute; top:-100%; width:100%; height:100%; background:var(--acid); animation:sd 1.8s ease infinite; }
    @keyframes sd { 0%{top:-100%} 100%{top:200%} }
    .scroll-txt { font-size:.55rem; letter-spacing:.18em; text-transform:uppercase; color:rgba(244,242,237,.3); writing-mode:vertical-rl; }

    /* ── MOSAIC ── */
    .mosaic-wrap { padding:0 3rem 6rem; }
    .mosaic-grid { display:grid; grid-template-columns:1.85fr 1fr 1fr; grid-template-rows:255px 255px; gap:.65rem; }
    .mosaic-cell { position:relative; overflow:hidden; border-radius:2px; cursor:pointer; }
    .mosaic-cell:first-child { grid-row:span 2; }
    .mosaic-cell img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .6s ease,filter .4s; filter:brightness(.72) saturate(.85); }
    .mosaic-cell:hover img { transform:scale(1.05); filter:brightness(.5) saturate(1.1); }
    .mosaic-ov { position:absolute; inset:0; background:linear-gradient(to top, rgba(8,8,8,.85) 0%, transparent 55%); display:flex; flex-direction:column; justify-content:flex-end; padding:1.5rem; }
    .mosaic-tag { font-size:.58rem; letter-spacing:.18em; text-transform:uppercase; color:var(--acid); margin-bottom:.25rem; }
    .mosaic-name { font-family:var(--font-display); font-size:1.5rem; text-transform:uppercase; color:var(--white); line-height:1; }

    /* ── CATEGORIES ── */
    .cat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1px; background:var(--border); border:1px solid var(--border); }
    .cat-card { background:var(--black); padding:2.25rem 1.75rem; text-decoration:none; display:block; position:relative; overflow:hidden; transition:background .2s; }
    .cat-card::before { content:''; position:absolute; bottom:0; left:0; right:0; height:2px; background:var(--acid); transform:scaleX(0); transform-origin:left; transition:transform .35s ease; }
    .cat-card:hover { background:rgba(200,245,66,.045); }
    .cat-card:hover::before { transform:scaleX(1); }
    .cat-icon { font-size:2.4rem; display:block; margin-bottom:.9rem; }
    .cat-name { font-family:var(--font-display); font-size:1.25rem; letter-spacing:.04em; text-transform:uppercase; color:var(--white); margin-bottom:.35rem; }
    .cat-cnt { font-size:.68rem; letter-spacing:.08em; color:var(--mid); }
    .cat-arrow { position:absolute; top:1.75rem; right:1.75rem; color:var(--acid); opacity:0; font-size:1rem; transition:opacity .2s,transform .2s; }
    .cat-card:hover .cat-arrow { opacity:1; transform:translate(3px,-3px); }

    /* ── PRODUCTS ── */
    .prod-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; }
    .prod-card { background:var(--gray); border-radius:2px; overflow:hidden; display:block; text-decoration:none; border:1px solid var(--border); transition:transform .25s; }
    .prod-card:hover { transform:translateY(-5px); }
    .prod-img { position:relative; aspect-ratio:4/3; overflow:hidden; }
    .prod-img img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .5s; filter:brightness(.82); }
    .prod-card:hover .prod-img img { transform:scale(1.06); }
    .prod-ph { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:3.5rem; background:linear-gradient(145deg,#1c1c1c,#111); }
    .prod-grad { position:absolute; inset:0; background:linear-gradient(180deg,transparent 45%,rgba(8,8,8,.65) 100%); }
    .prod-badge { position:absolute; top:.9rem; left:.9rem; z-index:2; background:var(--acid); color:var(--black); font-size:.6rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; padding:.22rem .55rem; border-radius:1px; }
    .prod-badge.new { background:#60a5fa; }
    .prod-info { padding:1.35rem 1.4rem; }
    .prod-cat { font-size:.6rem; letter-spacing:.14em; text-transform:uppercase; color:var(--mid); margin-bottom:.35rem; }
    .prod-name { font-family:var(--font-display); font-size:1.15rem; letter-spacing:.03em; text-transform:uppercase; color:var(--white); margin-bottom:.8rem; line-height:1.1; }
    .prod-meta { display:flex; align-items:flex-end; justify-content:space-between; }
    .prod-price-lbl { font-size:.6rem; color:var(--mid); }
    .prod-price { font-family:var(--font-display); font-size:1.25rem; color:var(--acid); line-height:1; }
    .prod-rating { font-size:.68rem; color:#fbbf24; display:flex; align-items:center; gap:.2rem; }
    .prod-rating span { color:var(--mid); font-size:.62rem; }
    .btn-rent { display:inline-block; margin-top:.8rem; padding:.42rem .85rem; background:transparent; border:1px solid rgba(200,245,66,.35); border-radius:1px; color:var(--acid); font-family:var(--font-body); font-size:.68rem; font-weight:500; letter-spacing:.08em; text-transform:uppercase; cursor:pointer; text-decoration:none; transition:background .2s,color .2s; }
    .btn-rent:hover { background:var(--acid); color:var(--black); }

    /* ── VIDEO STRIP ── */
    .vid-strip { margin:0 3rem; position:relative; border-radius:4px; overflow:hidden; height:420px; }
    .vid-strip video { width:100%; height:100%; object-fit:cover; display:block; }
    .vid-ov { position:absolute; inset:0; z-index:2; background:linear-gradient(90deg,rgba(8,8,8,.88) 0%,rgba(8,8,8,.35) 55%,transparent 100%); display:flex; align-items:center; padding:4rem; }
    .vid-text { max-width:500px; }
    .vid-label { font-size:.65rem; letter-spacing:.22em; text-transform:uppercase; color:var(--acid); margin-bottom:.85rem; }
    .vid-title { font-family:var(--font-display); font-size:clamp(2.5rem,4vw,3.8rem); text-transform:uppercase; line-height:.9; margin-bottom:1.2rem; }
    .vid-desc { font-size:.9rem; line-height:1.8; color:rgba(244,242,237,.6); margin-bottom:1.75rem; }

    /* ── HOW IT WORKS ── */
    .how-section { background:#111; padding:7rem 3rem; }
    .steps-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:2rem; margin-top:3.5rem; }
    .step { position:relative; padding:2rem; }
    .step::after { content:'→'; position:absolute; right:-.9rem; top:2rem; font-size:1rem; color:rgba(200,245,66,.2); }
    .step:last-child::after { display:none; }
    .step-num { font-family:var(--font-display); font-size:4.5rem; color:rgba(200,245,66,.09); line-height:1; margin-bottom:1rem; }
    .step-icon { font-size:1.75rem; margin-bottom:.8rem; }
    .step-title { font-family:var(--font-display); font-size:1.25rem; text-transform:uppercase; color:var(--white); margin-bottom:.65rem; }
    .step-desc { font-size:.83rem; line-height:1.75; color:var(--mid); }

    /* ── PROMO ── */
    .promo-wrap { padding:7rem 3rem; }
    .promo-inner { background:var(--acid); border-radius:4px; padding:4.5rem 4rem; display:grid; grid-template-columns:1fr auto; gap:3rem; align-items:center; position:relative; overflow:hidden; }
    .promo-inner::after { content:'GRATIS'; position:absolute; right:200px; top:-1.5rem; font-family:var(--font-display); font-size:9rem; color:rgba(0,0,0,.07); line-height:1; pointer-events:none; letter-spacing:-.02em; }
    .promo-tag { font-size:.68rem; font-weight:700; letter-spacing:.2em; text-transform:uppercase; color:rgba(0,0,0,.45); margin-bottom:.65rem; }
    .promo-title { font-family:var(--font-display); font-size:clamp(1.9rem,3vw,2.8rem); text-transform:uppercase; color:var(--black); line-height:1; margin-bottom:.8rem; }
    .promo-desc { font-size:.88rem; color:rgba(0,0,0,.55); line-height:1.7; }
    .btn-promo { padding:1rem 2.25rem; background:var(--black); color:var(--acid); border:none; border-radius:2px; font-family:var(--font-body); font-size:.85rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; text-decoration:none; white-space:nowrap; transition:background .2s; }
    .btn-promo:hover { background:#111; }

    /* ── TESTIMONIALS ── */
    .test-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; margin-top:3.5rem; }
    .test-card { background:#111; border-radius:2px; padding:2rem; border:1px solid var(--border); display:flex; flex-direction:column; gap:1.25rem; }
    .test-stars { color:#fbbf24; font-size:.88rem; letter-spacing:.06em; }
    .test-text { font-size:.87rem; line-height:1.8; color:rgba(244,242,237,.62); font-style:italic; flex:1; }
    .test-author { display:flex; align-items:center; gap:.8rem; }
    .test-av { width:2.4rem; height:2.4rem; border-radius:50%; background:var(--acid); color:var(--black); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.9rem; flex-shrink:0; }
    .test-name { font-size:.84rem; font-weight:500; color:var(--white); }
    .test-sport { font-size:.66rem; letter-spacing:.1em; text-transform:uppercase; color:var(--mid); margin-top:.1rem; }

    /* ── RESPONSIVE ── */
    @media(max-width:1100px) {
        .hero-content { grid-template-columns:1fr; }
        .hero-right { display:none; }
        .mosaic-grid { grid-template-columns:1fr 1fr; grid-template-rows:auto; }
        .mosaic-cell:first-child { grid-row:span 1; }
        .cat-grid { grid-template-columns:repeat(2,1fr); }
        .prod-grid { grid-template-columns:repeat(2,1fr); }
        .test-grid { grid-template-columns:repeat(2,1fr); }
    }
    @media(max-width:768px) {
        .hero-content { padding:0 1.5rem 4rem; }
        .slide-labels, .scroll-hint { display:none; }
        .mosaic-wrap { padding:0 1.5rem 4rem; }
        .mosaic-grid { grid-template-columns:1fr; }
        .vid-strip { margin:0 1.5rem; height:320px; }
        .vid-ov { padding:2rem; }
        .how-section { padding:4.5rem 1.5rem; }
        .steps-grid { grid-template-columns:1fr 1fr; }
        .promo-wrap { padding:4.5rem 1.5rem; }
        .promo-inner { grid-template-columns:1fr; padding:2.75rem 2rem; }
        .promo-inner::after { display:none; }
        .test-grid { grid-template-columns:1fr; }
    }
</style>
@endpush

@section('content')

{{-- HERO --}}
<section class="hero">
    <div class="hero-bg active"></div>
    <div class="hero-bg"></div>
    <div class="hero-bg"></div>
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-grain"></div>

    <div class="slide-labels">
        <div class="slbl active" onclick="goSlide(0)">MTB & Cycling</div>
        <div class="slbl" onclick="goSlide(1)">Water Sports</div>
        <div class="slbl" onclick="goSlide(2)">Surfing</div>
        <div class="slbl" onclick="goSlide(3)">Camping</div>
    </div>
    <div class="scroll-hint">
        <div class="scroll-line"></div>
        <div class="scroll-txt">Scroll</div>
    </div>

    <div class="hero-content">
        <div>
            <div class="hero-eyebrow">Platform Sewa Olahraga #1 Indonesia</div>
            <h1 class="hero-title">
                <span>Sewa</span>
                <span class="accent">Peralatan</span>
                <span class="outline">Olahraga</span>
            </h1>
        </div>
        <div class="hero-right">
            <p class="hero-desc">Akses ratusan perlengkapan olahraga premium — MTB, surfing, camping, panjat tebing — dengan harga terjangkau dan pengiriman cepat ke seluruh kota.</p>
            <div class="hero-cta">
                <a href="#produk" class="btn-hp">
                    Mulai Sewa
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M7 2l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <a href="#kategori" class="btn-hg">Lihat Katalog</a>
            </div>
            <div class="hero-stats">
                <div>
                    <div class="h-stat-num">{{ $stats['equipment'] > 0 ? $stats['equipment'] : '500' }}+</div>
                    <div class="h-stat-lbl">Alat Tersedia</div>
                </div>
                <div>
                    <div class="h-stat-num">{{ $stats['rentals'] > 100 ? number_format($stats['rentals'] / 1000, 1).'K' : ($stats['rentals'] > 0 ? $stats['rentals'] : '12K') }}</div>
                    <div class="h-stat-lbl">Transaksi Selesai</div>
                </div>
                <div>
                    <div class="h-stat-num">{{ $stats['avg_rating'] }}★</div>
                    <div class="h-stat-lbl">Rating Rata-rata</div>
                </div>
            </div>
        </div>
    </div>

    <div class="hero-dots">
        <div class="hero-dot active" onclick="goSlide(0)"></div>
        <div class="hero-dot" onclick="goSlide(1)"></div>
        <div class="hero-dot" onclick="goSlide(2)"></div>
        <div class="hero-dot" onclick="goSlide(3)"></div>
    </div>
</section>

<!-- {{-- PHOTO MOSAIC --}}
<div class="mosaic-wrap">
    <div class="mosaic-grid">
        <div class="mosaic-cell">
            <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=900&q=80" alt="MTB" loading="lazy">
            <div class="mosaic-ov"><div class="mosaic-tag">Populer</div><div class="mosaic-name">Mountain Biking</div></div>
        </div>
        <div class="mosaic-cell">
            <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80" alt="Surfing" loading="lazy">
            <div class="mosaic-ov"><div class="mosaic-tag">Trending</div><div class="mosaic-name">Surfing</div></div>
        </div>
        <div class="mosaic-cell">
            <img src="https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=600&q=80" alt="Camping" loading="lazy">
            <div class="mosaic-ov"><div class="mosaic-tag">Musim Ini</div><div class="mosaic-name">Camping</div></div>
        </div>
        <div class="mosaic-cell">
            <img src="https://images.unsplash.com/photo-1519046904884-53103b34b206?w=600&q=80" alt="Water Sports" loading="lazy">
            <div class="mosaic-ov"><div class="mosaic-tag">Seru!</div><div class="mosaic-name">Olahraga Air</div></div>
        </div>
        <div class="mosaic-cell">
            <img src="https://images.unsplash.com/photo-1522163182402-834f871fd851?w=600&q=80" alt="Climbing" loading="lazy">
            <div class="mosaic-ov"><div class="mosaic-tag">Ekstrem</div><div class="mosaic-name">Panjat Tebing</div></div>
        </div>
    </div>
</div> -->

{{-- KATEGORI --}}
<section class="section" id="kategori" style="padding-top:1rem;">
    <div class="section-hd fade-up">
        <div>
            <div class="section-label">Jelajahi</div>
            <h2 class="section-title">Kategori<br>Peralatan</h2>
        </div>
        <a href="{{ route('catalog') }}" class="section-lnk">Lihat Semua</a>
    </div>
    <div class="cat-grid fade-up d1">
        @forelse($categories as $cat)
            <a href="{{ route('catalog', ['category' => $cat->slug]) }}" class="cat-card">
                <span class="cat-icon">{{ $cat->icon ?: '🏅' }}</span>
                <div class="cat-name">{{ $cat->name }}</div>
                <div class="cat-cnt">{{ $cat->equipment_count }} alat tersedia</div>
                <span class="cat-arrow">↗</span>
            </a>
        @empty
            @foreach([['🚵','Sepeda & MTB'],['🤿','Olahraga Air'],['🏕','Camping & Hiking'],['🧗','Panjat Tebing'],['🏸','Olahraga Raket'],['⛷','Ski & Snowboard'],['🏄','Surfing & SUP'],['🎯','Lainnya']] as $c)
                <a href="{{ route('catalog') }}" class="cat-card">
                    <span class="cat-icon">{{ $c[0] }}</span>
                    <div class="cat-name">{{ $c[1] }}</div>
                    <div class="cat-cnt">Segera tersedia</div>
                    <span class="cat-arrow">↗</span>
                </a>
            @endforeach
        @endforelse
    </div>
</section>

{{-- PRODUK UNGGULAN --}}
@php
    $sportImgs = [
        'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=600&q=75',
        'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=600&q=75',
        'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=600&q=75',
        'https://images.unsplash.com/photo-1519046904884-53103b34b206?w=600&q=75',
        'https://images.unsplash.com/photo-1452573992436-6d508f200b30?w=600&q=75',
        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=75',
    ];
@endphp
<section class="section" id="produk" style="padding-top:0;">
    <div class="section-hd fade-up">
        <div>
            <div class="section-label">Terpopuler</div>
            <h2 class="section-title">Produk<br>Unggulan</h2>
        </div>
        <a href="{{ route('catalog') }}" class="section-lnk">Lihat Semua</a>
    </div>
    <div class="prod-grid">
        @forelse($featured as $i => $eq)
            <a href="{{ route('equipment.show', $eq) }}" class="prod-card fade-up d{{ min($i+1,5) }}">
                <div class="prod-img">
                    <img src="{{ $eq->image ? Storage::url($eq->image) : $sportImgs[$i % count($sportImgs)] }}" alt="{{ $eq->name }}" loading="lazy">
                    <div class="prod-grad"></div>
                    @if($i === 0)<div class="prod-badge">Populer</div>
                    @elseif($eq->created_at->gt(now()->subDays(14)))<div class="prod-badge new">Baru</div>
                    @endif
                </div>
                <div class="prod-info">
                    <div class="prod-cat">{{ $eq->category->name }}</div>
                    <div class="prod-name">{{ $eq->name }}</div>
                    <div class="prod-meta">
                        <div>
                            <div class="prod-price-lbl">Mulai dari</div>
                            <div class="prod-price">Rp {{ number_format($eq->price_per_day, 0, ',', '.') }}/hari</div>
                        </div>
                        @if($eq->reviews_avg_rating)
                            <div class="prod-rating">★ {{ number_format($eq->reviews_avg_rating, 1) }} <span>({{ $eq->rentals_count }}x)</span></div>
                        @endif
                    </div>
                    <div class="btn-rent">Sewa Sekarang</div> 
                </div>
            </a>
        @empty
            @foreach([['🚵','MTB Full Suspension Trek','Sepeda & MTB',150000,0],['🤿','Set Selam Lengkap Pro','Olahraga Air',250000,1],['🏕','Tenda Dome 4 Orang','Camping',80000,2],['🏄','Papan SUP Inflatable','Surfing & SUP',120000,3],['🧗','Set Climbing Lengkap','Panjat Tebing',200000,4],['🏸','Raket Badminton Yonex','Olahraga Raket',40000,5]] as $j => $p)
                <div class="prod-card fade-up d{{ min($j+1,5) }}">
                    <div class="prod-img">
                        <img src="{{ $sportImgs[$p[4]] }}" alt="{{ $p[1] }}" loading="lazy">
                        <div class="prod-grad"></div>
                        @if($j===0)<div class="prod-badge">Populer</div>@endif
                    </div>
                    <div class="prod-info">
                        <div class="prod-cat">{{ $p[2] }}</div>
                        <div class="prod-name">{{ $p[1] }}</div>
                        <div class="prod-meta">
                            <div>
                                <div class="prod-price-lbl">Mulai dari</div>
                                <div class="prod-price">Rp {{ number_format($p[3],0,',','.') }}/hari</div>
                            </div>
                        </div>
                        <div class="btn-rent">Sewa Sekarang</div>
                    </div>
                </div>
            @endforeach
        @endforelse
    </div>
</section>

{{-- VIDEO STRIP --}}
<div class="vid-strip">
    <video autoplay muted loop playsinline>
        <source src="https://player.vimeo.com/external/371433846.sd.mp4?s=236da2f3c0fd273d2c6d9a064f3ae35579b2bbba&profile_id=164&oauth2_token_id=57447761" type="video/mp4">
    </video>
    <div class="vid-ov">
        <div class="vid-text">
            <div class="vid-label">Kenapa SportRent?</div>
            <h2 class="vid-title">Gear Premium,<br>Harga Terjangkau</h2>
            <p class="vid-desc">Kami merawat setiap alat dengan standar tertinggi. Sewa hari ini, nikmati petualanganmu besok. Tanpa komitmen jangka panjang.</p>
            <a href="{{ route('catalog') }}" class="btn-hp">
                Jelajahi Katalog
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M7 2l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    </div>
</div>

{{-- HOW IT WORKS --}}
<section class="how-section" id="cara-sewa">
    <div class="section-label fade-up">Mudah & Cepat</div>
    <h2 class="section-title fade-up d1">Cara Sewa</h2>
    <div class="steps-grid">
        @foreach([
            ['01','🔍','Pilih Alat','Telusuri ratusan peralatan olahraga premium. Filter berdasarkan kategori, harga, dan ketersediaan.'],
            ['02','📅','Tentukan Durasi','Pilih tanggal mulai dan selesai. Tersedia opsi harian, mingguan, hingga bulanan.'],
            ['03','💳','Bayar & Konfirmasi','Bayar dengan transfer bank, e-wallet, atau tunai. Konfirmasi instan dari tim kami.'],
            ['04','📦','Terima & Nikmati','Alat dikirim ke lokasi Anda atau ambil di store. Selesai? Kami yang jemput.'],
        ] as $i => $s)
            <div class="step fade-up d{{ $i+1 }}">
                <div class="step-num">{{ $s[0] }}</div>
                <div class="step-icon">{{ $s[1] }}</div>
                <div class="step-title">{{ $s[2] }}</div>
                <p class="step-desc">{{ $s[3] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- PROMO --}}
<div class="promo-wrap" id="promo">
    <div class="promo-inner fade-up">
        <div>
            <div class="promo-tag">Penawaran Terbatas · Daftar Sekarang</div>
            <h2 class="promo-title">Daftar Sekarang,<br>Gratis Sewa 1 Hari</h2>
            <p class="promo-desc">Buat akun baru dan dapatkan voucher sewa gratis untuk alat apapun. Tidak perlu kode promo. Berlaku untuk semua kategori.</p>
        </div>
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="btn-promo">Klaim Sekarang</a>
        @else
            <a href="#" class="btn-promo">Klaim Sekarang</a>
        @endif
    </div>
</div>

{{-- TESTIMONIALS --}}
<section class="section" style="padding-top:0;">
    <div class="section-hd fade-up">
        <div>
            <div class="section-label">Cerita Pelanggan</div>
            <h2 class="section-title">Yang<br>Mereka Bilang</h2>
        </div>
    </div>
    <div class="test-grid">
        @forelse($reviews->take(3) as $i => $rev)
            <div class="test-card fade-up d{{ $i+1 }}">
                <div class="test-stars">{{ str_repeat('★',$rev->rating) }}{{ str_repeat('☆',5-$rev->rating) }}</div>
                <p class="test-text">"{{ Str::limit($rev->comment, 175) }}"</p>
                <div class="test-author">
                    <div class="test-av">{{ strtoupper(substr($rev->user->name,0,1)) }}</div>
                    <div>
                        <div class="test-name">{{ $rev->user->name }}</div>
                        <div class="test-sport">{{ $rev->equipment->name }}</div>
                    </div>
                </div>
            </div>
        @empty
            @foreach([
                ['R','Rizal F.','Mountain Biking','Pengalaman sewa yang luar biasa! Sepeda MTB kondisinya prima dan pengiriman tepat waktu.',5],
                ['S','Sari D.','Surfing','Akhirnya bisa coba surfing tanpa harus beli papan mahal. Set lengkap dan bersih. Pasti balik lagi!',5],
                ['A','Andi P.','Camping','Camping gear lengkap dan berkualitas. Proses sewa mudah banget lewat website.',4],
            ] as $i => $t)
                <div class="test-card fade-up d{{ $i+1 }}">
                    <div class="test-stars">{{ str_repeat('★',$t[4]) }}{{ str_repeat('☆',5-$t[4]) }}</div>
                    <p class="test-text">"{{ $t[3] }}"</p>
                    <div class="test-author">
                        <div class="test-av">{{ $t[0] }}</div>
                        <div>
                            <div class="test-name">{{ $t[1] }}</div>
                            <div class="test-sport">{{ $t[2] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforelse
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Hero slideshow
    const bgs  = document.querySelectorAll('.hero-bg');
    const dots = document.querySelectorAll('.hero-dot');
    const lbls = document.querySelectorAll('.slbl');
    let cur = 0, t;

    function goSlide(i) {
        bgs[cur].classList.remove('active');
        dots[cur].classList.remove('active');
        if (lbls[cur]) lbls[cur].classList.remove('active');
        cur = i;
        bgs[cur].classList.add('active');
        dots[cur].classList.add('active');
        if (lbls[cur]) lbls[cur].classList.add('active');
        clearInterval(t);
        t = setInterval(() => goSlide((cur + 1) % bgs.length), 5500);
    }
    t = setInterval(() => goSlide((cur + 1) % bgs.length), 5500);
</script>
@endpush
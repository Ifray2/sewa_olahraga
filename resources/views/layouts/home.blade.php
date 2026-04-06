<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SportRent — Sewa Alat Olahraga Premium')</title>
    <meta name="description" content="@yield('description', 'Platform sewa alat olahraga terpercaya. MTB, surfing, camping, panjat tebing — semua tersedia.')">

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
        nav#main-nav {
            position:fixed; top:0; left:0; right:0; z-index:200;
            display:flex; align-items:center; justify-content:space-between;
            padding:1.25rem 3rem;
            transition:background .35s, border-color .35s;
        }
        nav#main-nav.scrolled {
            background:rgba(8,8,8,.93);
            backdrop-filter:blur(18px);
            border-bottom:1px solid var(--border);
        }
        nav#main-nav.always-solid {
            background:rgba(8,8,8,.93);
            backdrop-filter:blur(18px);
            border-bottom:1px solid var(--border);
        }
        .nav-logo { font-family:var(--font-display); font-size:2rem; letter-spacing:.06em; color:var(--white); text-decoration:none; }
        .nav-logo span { color:var(--acid); }
        .nav-links { display:flex; align-items:center; gap:2.5rem; list-style:none; }
        .nav-links a { color:rgba(244,242,237,.5); font-size:.78rem; font-weight:400; letter-spacing:.12em; text-transform:uppercase; text-decoration:none; transition:color .2s; }
        .nav-links a:hover, .nav-links a.active { color:var(--acid); }
        .nav-center { display:flex; align-items:center; }
        .nav-back { display:flex; align-items:center; gap:.5rem; font-size:.75rem; letter-spacing:.1em; text-transform:uppercase; color:var(--mid); text-decoration:none; transition:color .2s; }
        .nav-back:hover { color:var(--acid); }
        .nav-back svg { transition:transform .2s; }
        .nav-back:hover svg { transform:translateX(-3px); }
        .nav-auth { display:flex; align-items:center; gap:.75rem; }
        .btn-ghost { padding:.45rem 1.1rem; border:1px solid rgba(244,242,237,.2); border-radius:2px; background:transparent; color:var(--white); font-family:var(--font-body); font-size:.78rem; letter-spacing:.06em; text-decoration:none; transition:border-color .2s,color .2s; }
        .btn-ghost:hover { border-color:var(--acid); color:var(--acid); }
        .btn-solid { padding:.45rem 1.3rem; border:1px solid var(--acid); border-radius:2px; background:var(--acid); color:var(--black); font-family:var(--font-body); font-size:.78rem; font-weight:700; letter-spacing:.06em; text-decoration:none; transition:background .2s,color .2s; }
        .btn-solid:hover { background:transparent; color:var(--acid); }

        /* ── MARQUEE ── */
        .mq-wrap { border-top:1px solid var(--border); border-bottom:1px solid var(--border); padding:1.1rem 0; overflow:hidden; background:rgba(200,245,66,.02); }
        .mq-track { display:flex; gap:2.5rem; width:max-content; animation:mq 30s linear infinite; }
        .mq-track:hover { animation-play-state:paused; }
        .mq-item { display:flex; align-items:center; gap:1rem; font-family:var(--font-display); font-size:.82rem; letter-spacing:.22em; text-transform:uppercase; color:rgba(244,242,237,.25); white-space:nowrap; }
        .mq-item b { color:var(--acid); font-weight:400; font-size:.45rem; }
        @keyframes mq { from{transform:translateX(0)} to{transform:translateX(-50%)} }

        /* ── SECTION BASE ── */
        .section { padding:7rem 3rem; }
        .section-label { font-size:.65rem; font-weight:500; letter-spacing:.22em; text-transform:uppercase; color:var(--acid); margin-bottom:.7rem; }
        .section-title { font-family:var(--font-display); font-size:clamp(2.8rem,5vw,4.5rem); line-height:.9; letter-spacing:.02em; text-transform:uppercase; }
        .section-hd { display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:3.5rem; }
        .section-lnk { font-size:.75rem; letter-spacing:.12em; text-transform:uppercase; color:var(--mid); text-decoration:none; border-bottom:1px solid var(--mid); padding-bottom:1px; transition:color .2s,border-color .2s; }
        .section-lnk:hover { color:var(--acid); border-color:var(--acid); }

        /* ── FOOTER ── */
        footer.public-footer { background:var(--card); padding:5rem 3rem 2.5rem; border-top:1px solid var(--border); }
        .ft-grid { display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:3rem; margin-bottom:4rem; }
        .ft-logo { font-family:var(--font-display); font-size:1.8rem; color:var(--white); text-decoration:none; letter-spacing:.06em; display:block; margin-bottom:1rem; }
        .ft-logo span { color:var(--acid); }
        .ft-desc { font-size:.83rem; line-height:1.75; color:var(--mid); max-width:260px; }
        .ft-col h4 { font-size:.64rem; font-weight:500; letter-spacing:.2em; text-transform:uppercase; color:var(--acid); margin-bottom:1.2rem; }
        .ft-col ul { list-style:none; display:flex; flex-direction:column; gap:.5rem; }
        .ft-col a { font-size:.83rem; color:var(--mid); text-decoration:none; transition:color .2s; }
        .ft-col a:hover { color:var(--white); }
        .ft-bot { display:flex; justify-content:space-between; align-items:center; padding-top:2rem; border-top:1px solid var(--border); }
        .ft-copy { font-size:.74rem; color:rgba(90,90,90,.65); }
        .ft-copy strong { color:var(--acid); }

        /* ── ANIMATIONS ── */
        .fade-up { opacity:0; transform:translateY(30px); transition:opacity .7s ease,transform .7s ease; }
        .fade-up.in { opacity:1; transform:translateY(0); }
        .d1{transition-delay:.1s} .d2{transition-delay:.2s} .d3{transition-delay:.3s}
        .d4{transition-delay:.4s} .d5{transition-delay:.5s}

        /* ── RESPONSIVE BASE ── */
        @media(max-width:768px) {
            nav#main-nav { padding:1rem 1.5rem; }
            .nav-links { display:none; }
            .section { padding:4.5rem 1.5rem; }
            .ft-grid { grid-template-columns:1fr; gap:2rem; }
            .ft-bot { flex-direction:column; gap:1rem; text-align:center; }
        }
        @media(max-width:1100px) {
            .ft-grid { grid-template-columns:1fr 1fr; }
        }
    </style>

    {{-- Page-specific styles --}}
    @stack('styles')
</head>
<body>

    {{-- NAV --}}
    <nav id="main-nav" class="@yield('nav-class')">
        <a href="{{ route('home') }}" class="nav-logo">Sport<span>Rent</span></a>

        {{-- Center slot: nav-links OR back button --}}
        @hasSection('nav-center')
            <div class="nav-center">@yield('nav-center')</div>
        @else
            <ul class="nav-links">
                <li><a href="{{ route('home') }}#kategori">Kategori</a></li>
                <li><a href="{{ route('catalog') }}">Katalog</a></li>
                <li><a href="{{ route('home') }}#cara-sewa">Cara Sewa</a></li>
                <li><a href="{{ route('home') }}#promo">Promo</a></li>
            </ul>
        @endif

        <div class="nav-auth">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-ghost">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-ghost" style="cursor:pointer;font-family:var(--font-body);">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-solid">Daftar</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    {{-- PAGE CONTENT --}}
    @yield('content')

    {{-- MARQUEE (optional, shown on pages that want it) --}}
    @hasSection('show-marquee')
        <div class="mq-wrap">
            <div class="mq-track">
                @php $mqItems = ['Sepeda Gunung','Perlengkapan Selam','Tenda Camping','Papan Selancar','Kayak & Kano','Panjat Tebing','Ski & Snowboard','Badminton','Surfboard','Paragliding']; @endphp
                @foreach(array_merge($mqItems, $mqItems) as $mqItem)
                    <div class="mq-item">{{ $mqItem }} <b>◆</b></div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- FOOTER --}}
    @hasSection('hide-footer')
    @else
        <footer class="public-footer">
            <div class="ft-grid">
                <div>
                    <a href="{{ route('home') }}" class="ft-logo">Sport<span>Rent</span></a>
                    <p class="ft-desc">Platform sewa alat olahraga terpercaya dengan ratusan pilihan peralatan premium untuk semua aktivitas outdoor dan indoor.</p>
                </div>
                <div class="ft-col">
                    <h4>Kategori</h4>
                    <ul>
                        @php $footerCats = \App\Models\Category::active()->limit(5)->get(); @endphp
                        @forelse($footerCats as $fc)
                            <li><a href="{{ route('catalog', ['category' => $fc->slug]) }}">{{ $fc->name }}</a></li>
                        @empty
                            <li><a href="{{ route('catalog') }}">Sepeda & MTB</a></li>
                            <li><a href="{{ route('catalog') }}">Olahraga Air</a></li>
                            <li><a href="{{ route('catalog') }}">Camping & Hiking</a></li>
                            <li><a href="{{ route('catalog') }}">Panjat Tebing</a></li>
                        @endforelse
                    </ul>
                </div>
                <div class="ft-col">
                    <h4>Perusahaan</h4>
                    <ul>
                        <li><a href="#">Tentang Kami</a></li>
                        <li><a href="{{ route('home') }}#cara-sewa">Cara Kerja</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Kontak</a></li>
                    </ul>
                </div>
                <div class="ft-col">
                    <h4>Bantuan</h4>
                    <ul>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Syarat & Ketentuan</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="{{ route('home') }}#cara-sewa">Cara Sewa</a></li>
                    </ul>
                </div>
            </div>
            <div class="ft-bot">
                <p class="ft-copy">© {{ date('Y') }} <strong>SportRent</strong>. Hak Cipta Dilindungi.</p>
                <p class="ft-copy">Made with ♥ in Indonesia</p>
            </div>
        </footer>
    @endif

    {{-- BASE JS: nav scroll + fade-up --}}
    <script>
        // Navbar scroll transparency
        const _nav = document.getElementById('main-nav');
        if (_nav && !_nav.classList.contains('always-solid')) {
            window.addEventListener('scroll', () => {
                _nav.classList.toggle('scrolled', scrollY > 60);
            }, { passive: true });
        }

        // Scroll fade-up
        const _io = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('in'); });
        }, { threshold: .1 });
        document.querySelectorAll('.fade-up').forEach(el => _io.observe(el));
    </script>

    {{-- Page-specific scripts --}}
    @stack('scripts')

</body>
</html>
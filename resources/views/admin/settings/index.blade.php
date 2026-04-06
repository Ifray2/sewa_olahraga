<x-app-layout>
    <x-slot name="heading">Pengaturan</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <span>Pengaturan</span>
    </x-slot>

    @php $activeTab = session('tab', old('_tab', 'profile')); @endphp

    <div style="display:grid;grid-template-columns:240px 1fr;gap:1.5rem;align-items:start;">

        {{-- ── SIDEBAR TABS ── --}}
        <div style="display:flex;flex-direction:column;gap:.25rem;position:sticky;top:70px;">

            {{-- Profile card --}}
            <div style="padding:1.25rem;background:var(--card);border:1px solid var(--border);border-radius:4px;text-align:center;margin-bottom:.75rem;">
                <div style="width:3.5rem;height:3.5rem;border-radius:50%;margin:0 auto .75rem;
                    background:{{ $admin->isAdmin() ? 'var(--acid)' : ($admin->isPetugas() ? 'rgba(96,165,250,.15)' : 'var(--surface)') }};
                    color:{{ $admin->isAdmin() ? 'var(--black)' : ($admin->isPetugas() ? '#60a5fa' : 'var(--mid)') }};
                    border:2px solid {{ $admin->isAdmin() ? 'var(--acid)' : ($admin->isPetugas() ? 'rgba(96,165,250,.4)' : 'var(--border)') }};
                    display:flex;align-items:center;justify-content:center;
                    font-family:var(--font-display);font-size:1.8rem;">
                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                </div>
                <div style="font-family:var(--font-display);font-size:.95rem;text-transform:uppercase;color:var(--white);letter-spacing:.04em;line-height:1.2;">
                    {{ $admin->name }}
                </div>
                <div style="font-size:.72rem;color:var(--mid);margin:.3rem 0 .5rem;">{{ $admin->email }}</div>
                @if($admin->isAdmin())
                    <span class="badge badge-active">Admin</span>
                @elseif($admin->isPetugas())
                    <span class="badge badge-confirmed">Petugas</span>
                @endif
                <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid var(--border);display:grid;grid-template-columns:1fr 1fr;gap:.5rem;">
                    <div style="text-align:center;">
                        <div style="font-family:var(--font-display);font-size:1.1rem;color:var(--acid);">{{ $stats['handled_rentals'] }}</div>
                        <div style="font-size:.6rem;color:var(--mid);text-transform:uppercase;letter-spacing:.08em;">Ditangani</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:.68rem;color:var(--muted);margin-top:.25rem;">Sejak</div>
                        <div style="font-size:.65rem;color:var(--mid);">{{ $admin->created_at->format('Y') }}</div>
                    </div>
                </div>
            </div>

            {{-- Tab links --}}
            @php
                $tabs = [
                    'profile'  => ['icon' => '👤', 'label' => 'Profil Saya'],
                    'password' => ['icon' => '🔒', 'label' => 'Ubah Password'],
                    'system'   => ['icon' => '⚙',  'label' => 'Info Sistem'],
                ];
                if ($admin->isAdmin()) {
                    $tabs['danger'] = ['icon' => '⚠', 'label' => 'Zona Berbahaya'];
                }
            @endphp

            @foreach($tabs as $key => $tab)
                <button onclick="switchTab('{{ $key }}')" id="tab-btn-{{ $key }}"
                        style="display:flex;align-items:center;gap:.65rem;width:100%;padding:.75rem 1rem;
                               background:{{ $activeTab === $key ? 'rgba(200,245,66,.08)' : 'transparent' }};
                               border:1px solid {{ $activeTab === $key ? 'rgba(200,245,66,.2)' : 'transparent' }};
                               border-radius:3px;color:{{ $key === 'danger' ? '#f87171' : ($activeTab === $key ? 'var(--acid)' : 'var(--muted)') }};
                               cursor:pointer;text-align:left;font-family:var(--font-body);font-size:.82rem;
                               letter-spacing:.03em;transition:all .15s;">
                    <span style="font-size:1rem;opacity:.8;">{{ $tab['icon'] }}</span>
                    {{ $tab['label'] }}
                </button>
            @endforeach

        </div>

        {{-- ── CONTENT PANELS ── --}}
        <div>

            {{-- ─────────────── PROFIL ─────────────── --}}
            <div id="panel-profile" style="display:{{ $activeTab === 'profile' ? 'flex' : 'none' }};flex-direction:column;gap:1.5rem;">

                <div class="table-card">
                    <div class="table-card-header">
                        <div>
                            <div class="table-card-title">Informasi Profil</div>
                            <div style="font-size:.72rem;color:var(--mid);margin-top:.15rem;">Perbarui nama, email, dan kontak Anda</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.profile') }}" style="padding:1.75rem;">
                        @csrf @method('PUT')
                        <input type="hidden" name="_tab" value="profile">

                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group span-2">
                                <label class="form-label">Nama Lengkap <span style="color:#f87171;">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $admin->name) }}"
                                       class="form-control" oninput="document.getElementById('preview-name').textContent=this.value||'—'">
                                @error('name') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group span-2">
                                <label class="form-label">Alamat Email <span style="color:#f87171;">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                                       class="form-control">
                                @if($admin->email_verified_at)
                                    <div class="form-hint" style="color:var(--acid);">✓ Email sudah diverifikasi pada {{ $admin->email_verified_at->format('d M Y') }}</div>
                                @else
                                    <div class="form-hint" style="color:#fbbf24;">⚠ Email belum diverifikasi</div>
                                @endif
                                @error('email') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}"
                                       class="form-control" placeholder="08xx-xxxx-xxxx">
                                @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Role</label>
                                <div class="form-control" style="opacity:.55;cursor:not-allowed;display:flex;align-items:center;gap:.5rem;">
                                    @if($admin->isAdmin()) ⚡ Admin
                                    @elseif($admin->isPetugas()) 🛠 Petugas
                                    @else 👤 User @endif
                                </div>
                                <div class="form-hint">Role tidak dapat diubah dari sini.</div>
                            </div>

                            <div class="form-group span-2">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" class="form-control" rows="3"
                                          placeholder="Alamat lengkap Anda…">{{ old('address', $admin->address) }}</textarea>
                                @error('address') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>

                        <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
                            <div style="font-size:.75rem;color:var(--mid);">
                                Terakhir diperbarui: <span style="color:var(--muted);">{{ $admin->updated_at->diffForHumans() }}</span>
                            </div>
                            <button type="submit" class="btn-add" style="padding:.65rem 1.5rem;">
                                Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Ringkasan akun --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Ringkasan Akun</div>
                    </div>
                    <div style="padding:1.25rem;display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
                        @php
                            $accountInfo = [
                                ['label'=>'ID Akun',          'val'=>'#'.$admin->id,                          'color'=>'var(--white)'],
                                ['label'=>'Bergabung',         'val'=>$admin->created_at->format('d M Y'),     'color'=>'var(--white)'],
                                ['label'=>'Transaksi Ditangani','val'=>$stats['handled_rentals'].' sewa',       'color'=>'var(--acid)'],
                            ];
                        @endphp
                        @foreach($accountInfo as $info)
                            <div style="padding:.85rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                                <div style="font-size:.6rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.3rem;">{{ $info['label'] }}</div>
                                <div style="font-size:.9rem;color:{{ $info['color'] }};font-family:var(--font-display);">{{ $info['val'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- ─────────────── PASSWORD ─────────────── --}}
            <div id="panel-password" style="display:{{ $activeTab === 'password' ? 'block' : 'none' }};">
                <div class="table-card">
                    <div class="table-card-header">
                        <div>
                            <div class="table-card-title">Ubah Password</div>
                            <div style="font-size:.72rem;color:var(--mid);margin-top:.15rem;">Gunakan password yang kuat dan unik</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.password') }}" style="padding:1.75rem;">
                        @csrf @method('PUT')
                        <input type="hidden" name="_tab" value="password">

                        <div style="display:flex;flex-direction:column;gap:1.25rem;max-width:480px;">

                            <div class="form-group">
                                <label class="form-label">Password Saat Ini <span style="color:#f87171;">*</span></label>
                                <div style="position:relative;">
                                    <input type="password" name="current_password" id="cpw"
                                           class="form-control" placeholder="Masukkan password saat ini" autocomplete="current-password">
                                    <button type="button" onclick="togglePw('cpw',this)"
                                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--mid);cursor:pointer;">👁</button>
                                </div>
                                @error('current_password') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div style="height:1px;background:var(--border);"></div>

                            <div class="form-group">
                                <label class="form-label">Password Baru <span style="color:#f87171;">*</span></label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="npw"
                                           class="form-control" placeholder="Min. 8 karakter"
                                           autocomplete="new-password"
                                           oninput="checkStrength(this.value);checkMatch()">
                                    <button type="button" onclick="togglePw('npw',this)"
                                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--mid);cursor:pointer;">👁</button>
                                </div>
                                <div style="display:flex;gap:.25rem;margin-top:.4rem;">
                                    @for($b=0;$b<4;$b++)
                                        <div class="sbar" data-i="{{ $b }}" style="flex:1;height:3px;border-radius:2px;background:var(--border);transition:background .2s;"></div>
                                    @endfor
                                </div>
                                <div id="slabel" style="font-size:.68rem;color:var(--mid);margin-top:.2rem;min-height:1em;"></div>
                                @error('password') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Konfirmasi Password Baru <span style="color:#f87171;">*</span></label>
                                <div style="position:relative;">
                                    <input type="password" name="password_confirmation" id="cpw2"
                                           class="form-control" placeholder="Ulangi password baru"
                                           autocomplete="new-password" oninput="checkMatch()">
                                    <button type="button" onclick="togglePw('cpw2',this)"
                                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--mid);cursor:pointer;">👁</button>
                                </div>
                                <div id="mlabel" style="font-size:.68rem;margin-top:.2rem;min-height:1em;"></div>
                            </div>

                            {{-- Tips --}}
                            <div style="padding:1rem;background:var(--surface);border-radius:4px;border:1px solid var(--border);">
                                <div style="font-size:.65rem;letter-spacing:.12em;text-transform:uppercase;color:var(--mid);margin-bottom:.6rem;">Tips Password Kuat</div>
                                <div style="display:flex;flex-direction:column;gap:.3rem;">
                                    @foreach(['Minimal 8 karakter','Kombinasi huruf besar dan kecil','Mengandung angka','Mengandung karakter spesial (!@#$%)'] as $tip)
                                        <div style="font-size:.75rem;color:var(--muted);display:flex;align-items:center;gap:.5rem;">
                                            <span style="color:var(--acid);flex-shrink:0;">›</span> {{ $tip }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div style="padding-top:.5rem;">
                                <button type="submit" class="btn-add" style="padding:.65rem 1.5rem;">
                                    🔒 Ubah Password
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            {{-- ─────────────── SISTEM ─────────────── --}}
            <div id="panel-system" style="display:{{ $activeTab === 'system' ? 'flex' : 'none' }};flex-direction:column;gap:1.5rem;">

                {{-- App info --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Informasi Aplikasi</div>
                    </div>
                    <div style="padding:1.25rem;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--border);border:1px solid var(--border);border-radius:4px;overflow:hidden;">
                            @php
                                $sysInfo = [
                                    ['Nama Aplikasi',      config('app.name', 'SportRent')],
                                    ['Versi Laravel',      app()->version()],
                                    ['Versi PHP',          phpversion()],
                                    ['Lingkungan',         strtoupper(app()->environment())],
                                    ['Mode Debug',         config('app.debug') ? 'AKTIF' : 'Nonaktif'],
                                    ['Timezone',           config('app.timezone')],
                                    ['Locale',             config('app.locale')],
                                    ['Driver Cache',       strtoupper(config('cache.default'))],
                                    ['Driver Queue',       strtoupper(config('queue.default'))],
                                    ['Driver Database',    strtoupper(config('database.default'))],
                                ];
                            @endphp
                            @foreach($sysInfo as $row)
                                <div style="background:var(--card);padding:.85rem 1.1rem;">
                                    <div style="font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:var(--mid);margin-bottom:.25rem;">{{ $row[0] }}</div>
                                    <div style="font-size:.85rem;color:{{ $row[0]==='Mode Debug'&&config('app.debug') ? '#fbbf24' : ($row[0]==='Lingkungan'&&app()->isProduction() ? 'var(--acid)' : 'var(--white)') }};font-family:{{ in_array($row[0],['Versi Laravel','Versi PHP']) ? 'monospace' : 'inherit' }};">
                                        {{ $row[1] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Storage info --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Penyimpanan</div>
                    </div>
                    <div style="padding:1.25rem;">
                        @php
                            $storagePath = storage_path('app/public');
                            $storageSize = 0;
                            if (is_dir($storagePath)) {
                                $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($storagePath, FilesystemIterator::SKIP_DOTS));
                                foreach ($iter as $file) $storageSize += $file->getSize();
                            }
                            $storageMB   = round($storageSize / 1048576, 2);
                            $diskFree    = round(disk_free_space('/') / 1073741824, 1);
                            $diskTotal   = round(disk_total_space('/') / 1073741824, 1);
                            $diskUsedPct = $diskTotal > 0 ? round((($diskTotal - $diskFree) / $diskTotal) * 100) : 0;
                        @endphp
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
                            <div style="padding:.85rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);text-align:center;">
                                <div style="font-family:var(--font-display);font-size:1.3rem;color:var(--acid);">{{ $storageMB }} MB</div>
                                <div style="font-size:.6rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;margin-top:.2rem;">Digunakan App</div>
                            </div>
                            <div style="padding:.85rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);text-align:center;">
                                <div style="font-family:var(--font-display);font-size:1.3rem;color:var(--white);">{{ $diskFree }} GB</div>
                                <div style="font-size:.6rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;margin-top:.2rem;">Disk Tersisa</div>
                            </div>
                            <div style="padding:.85rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);text-align:center;">
                                <div style="font-family:var(--font-display);font-size:1.3rem;color:{{ $diskUsedPct > 85 ? '#f87171' : ($diskUsedPct > 65 ? '#fbbf24' : 'var(--white)') }};">{{ $diskUsedPct }}%</div>
                                <div style="font-size:.6rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;margin-top:.2rem;">Disk Terpakai</div>
                            </div>
                        </div>
                        <div style="height:6px;background:var(--border);border-radius:3px;overflow:hidden;">
                            <div style="height:100%;border-radius:3px;background:{{ $diskUsedPct > 85 ? '#f87171' : ($diskUsedPct > 65 ? '#fbbf24' : 'var(--acid)') }};width:{{ $diskUsedPct }}%;transition:width .5s;"></div>
                        </div>
                        <div style="font-size:.7rem;color:var(--mid);margin-top:.4rem;text-align:right;">{{ $diskTotal - $diskFree }} GB / {{ $diskTotal }} GB digunakan</div>
                    </div>
                </div>

                {{-- Database stats --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Statistik Database</div>
                    </div>
                    <div style="padding:1.25rem;display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
                        @php
                        $dbStats = [
                            ['Pengguna',   \App\Models\User::count(),      '👤'],
                            ['Kategori',   \App\Models\Category::count(),  '📁'],
                            ['Alat',       \App\Models\Equipment::count(), '🎒'],
                            ['Transaksi',  \App\Models\Rental::count(),    '📋'],
                            ['Pembayaran', \App\Models\Payment::count(),   '💳'],
                            ['Ulasan',     \App\Models\Review::count(),    '⭐'],
                        ];
                        @endphp
                        @foreach($dbStats as $s)
                            <div style="padding:.85rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);display:flex;align-items:center;gap:.75rem;">
                                <span style="font-size:1.3rem;opacity:.7;">{{ $s[2] }}</span>
                                <div>
                                    <div style="font-family:var(--font-display);font-size:1.3rem;color:var(--acid);line-height:1;">{{ number_format($s[1]) }}</div>
                                    <div style="font-size:.62rem;color:var(--mid);text-transform:uppercase;letter-spacing:.1em;margin-top:.1rem;">{{ $s[0] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Quick links --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Tautan Cepat</div>
                    </div>
                    <div style="padding:1.25rem;display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                        @php
                            $quickLinks = [
                                ['label'=>'Buat Transaksi Baru', 'href'=>route('admin.rentals.create'),   'icon'=>'📋', 'color'=>'var(--acid)'],
                                ['label'=>'Tambah Alat Baru',    'href'=>route('admin.equipment.create'), 'icon'=>'🎒', 'color'=>'var(--acid)'],
                                ['label'=>'Tambah Kategori',     'href'=>route('admin.categories.create'),'icon'=>'📁', 'color'=>'#60a5fa'],
                                ['label'=>'Tambah Pengguna',     'href'=>route('admin.users.create'),     'icon'=>'👤', 'color'=>'#60a5fa'],
                                ['label'=>'Lihat Pending',       'href'=>route('admin.rentals.index',['status'=>'pending']), 'icon'=>'⏳', 'color'=>'#fbbf24'],
                                ['label'=>'Lihat Website',       'href'=>('/'),                   'icon'=>'🌐', 'color'=>'#9ca3af'],
                            ];
                        @endphp
                        @foreach($quickLinks as $link)
                            <a href="{{ $link['href'] }}"
                               style="display:flex;align-items:center;gap:.65rem;padding:.8rem 1rem;background:var(--surface);border:1px solid var(--border);border-radius:2px;text-decoration:none;transition:border-color .15s,background .15s;"
                               onmouseover="this.style.borderColor='{{ $link['color'] }}';this.style.background='var(--card)'"
                               onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface)'">
                                <span style="font-size:1rem;">{{ $link['icon'] }}</span>
                                <span style="font-size:.78rem;color:var(--muted);">{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- ─────────────── DANGER (admin only) ─────────────── --}}
            @if($admin->isAdmin())
            <div id="panel-danger" style="display:{{ $activeTab === 'danger' ? 'flex' : 'none' }};flex-direction:column;gap:1.5rem;">

                <div style="padding:1rem 1.25rem;background:rgba(248,113,113,.06);border:1px solid rgba(248,113,113,.2);border-radius:4px;display:flex;align-items:flex-start;gap:.75rem;">
                    <span style="font-size:1.3rem;flex-shrink:0;">⚠</span>
                    <div style="font-size:.82rem;color:#fca5a5;line-height:1.7;">
                        Tindakan di halaman ini bersifat <strong>permanen dan tidak dapat dibatalkan</strong>. Lakukan hanya jika Anda tahu apa yang sedang dilakukan.
                    </div>
                </div>

                {{-- Clear cache --}}
                <div class="table-card" style="border-color:rgba(248,113,113,.15);">
                    <div class="table-card-header" style="border-color:rgba(248,113,113,.15);">
                        <div>
                            <div class="table-card-title">Bersihkan Cache</div>
                            <div style="font-size:.72rem;color:var(--mid);margin-top:.15rem;">Hapus cache aplikasi, config, route, dan view</div>
                        </div>
                    </div>
                    <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.75rem;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                            @php
                                $cacheActions = [
                                    ['label'=>'Cache Aplikasi',   'route'=>'admin.settings.clear-cache',       'desc'=>'php artisan cache:clear'],
                                    ['label'=>'Cache Config',     'route'=>'admin.settings.clear-config',      'desc'=>'php artisan config:clear'],
                                    ['label'=>'Cache Route',      'route'=>'admin.settings.clear-routes',      'desc'=>'php artisan route:clear'],
                                    ['label'=>'Cache View',       'route'=>'admin.settings.clear-views',       'desc'=>'php artisan view:clear'],
                                ];
                            @endphp
                            @foreach($cacheActions as $action)
                                <div style="padding:.85rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                                    <div style="font-size:.78rem;color:var(--white);margin-bottom:.2rem;">{{ $action['label'] }}</div>
                                    <div style="font-family:monospace;font-size:.65rem;color:var(--mid);margin-bottom:.65rem;">{{ $action['desc'] }}</div>
                                    <form method="POST" action="{{ route($action['route']) }}">
                                        @csrf
                                        <button type="submit" class="btn-action"
                                                style="font-size:.7rem;border-color:rgba(248,113,113,.3);color:#f87171;background:transparent;cursor:pointer;font-family:var(--font-body);letter-spacing:.04em;padding:.3rem .7rem;"
                                                onclick="return confirm('Bersihkan {{ $action['label'] }}?')">
                                            Bersihkan
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                        <form method="POST" action="{{ route('admin.settings.clear-all') }}">
                            @csrf
                            <button type="submit" class="btn-action btn-delete"
                                    style="width:100%;justify-content:center;padding:.65rem;font-size:.8rem;border-width:1px;"
                                    onclick="return confirm('Bersihkan SEMUA cache? Pastikan backup sebelum melanjutkan.')">
                                ⚡ Bersihkan Semua Cache Sekarang
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Maintenance mode --}}
                <div class="table-card" style="border-color:rgba(248,113,113,.15);">
                    <div class="table-card-header" style="border-color:rgba(248,113,113,.15);">
                        <div>
                            <div class="table-card-title">Mode Maintenance</div>
                            <div style="font-size:.72rem;color:var(--mid);margin-top:.15rem;">
                                Status:
                                @if(app()->isDownForMaintenance())
                                    <span style="color:#f87171;">🔴 Aplikasi sedang dalam maintenance</span>
                                @else
                                    <span style="color:var(--acid);">🟢 Aplikasi berjalan normal</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div style="padding:1.25rem;">
                        <div style="font-size:.8rem;color:var(--muted);line-height:1.7;margin-bottom:1rem;">
                            Saat mode maintenance aktif, semua pengunjung akan melihat halaman maintenance. Admin masih dapat mengakses panel ini.
                        </div>
                        @if(app()->isDownForMaintenance())
                            <form method="POST" action="{{ route('admin.settings.maintenance-up') }}">
                                @csrf
                                <button type="submit" class="btn-add" style="width:100%;justify-content:center;padding:.7rem;">
                                    ✅ Matikan Maintenance Mode
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.settings.maintenance-down') }}"
                                  onsubmit="return confirm('Aktifkan mode maintenance? Website akan tidak dapat diakses pengunjung.')">
                                @csrf
                                <button type="submit" class="btn-action btn-delete"
                                        style="width:100%;justify-content:center;padding:.7rem;font-size:.82rem;border-width:1px;">
                                    🔴 Aktifkan Maintenance Mode
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Logout all sessions --}}
                <div class="table-card" style="border-color:rgba(248,113,113,.15);">
                    <div class="table-card-header" style="border-color:rgba(248,113,113,.15);">
                        <div>
                            <div class="table-card-title">Logout Semua Sesi</div>
                            <div style="font-size:.72rem;color:var(--mid);margin-top:.15rem;">Paksa semua pengguna logout dari seluruh perangkat</div>
                        </div>
                    </div>
                    <div style="padding:1.25rem;">
                        <div style="font-size:.8rem;color:var(--muted);line-height:1.7;margin-bottom:1rem;">
                            Tindakan ini akan menghapus semua sesi aktif kecuali sesi Anda sendiri. Pengguna yang sedang login harus masuk kembali.
                        </div>
                        <form method="POST" action="{{ route('admin.settings.logout-all') }}"
                              onsubmit="return confirm('Logout semua sesi pengguna?')">
                            @csrf
                            <button type="submit" class="btn-action btn-delete"
                                    style="width:100%;justify-content:center;padding:.7rem;font-size:.82rem;border-width:1px;">
                                🚪 Logout Semua Sesi
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            @endif

        </div>
    </div>

    <script>
        function switchTab(key) {
            ['profile','password','system','danger'].forEach(t => {
                const panel = document.getElementById('panel-' + t);
                const btn   = document.getElementById('tab-btn-' + t);
                if (!panel || !btn) return;

                const active = t === key;
                panel.style.display = active ? (t === 'password' ? 'block' : 'flex') : 'none';
                panel.style.flexDirection = 'column';

                btn.style.background   = active ? 'rgba(200,245,66,.08)' : 'transparent';
                btn.style.borderColor  = active ? 'rgba(200,245,66,.2)' : 'transparent';
                btn.style.color        = t === 'danger' ? '#f87171' : (active ? 'var(--acid)' : 'var(--muted)');
            });
        }

        // Password strength
        function checkStrength(pw) {
            let score = 0;
            if (pw.length >= 8) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[^A-Za-z0-9]/.test(pw)) score++;

            const bars   = document.querySelectorAll('.sbar');
            const colors = ['#f87171','#fbbf24','#60a5fa','#c8f542'];
            const labels = ['Lemah','Cukup','Kuat','Sangat Kuat'];

            bars.forEach((b, i) => {
                b.style.background = i < score ? colors[score-1] : 'var(--border)';
            });
            const lbl = document.getElementById('slabel');
            lbl.textContent = pw ? (labels[score-1] || '') : '';
            lbl.style.color = pw ? (colors[score-1] || 'var(--mid)') : 'var(--mid)';
        }

        function checkMatch() {
            const p1  = document.getElementById('npw').value;
            const p2  = document.getElementById('cpw2').value;
            const lbl = document.getElementById('mlabel');
            if (!p2) { lbl.textContent = ''; return; }
            lbl.textContent = p1 === p2 ? '✓ Cocok' : '✕ Tidak cocok';
            lbl.style.color = p1 === p2 ? 'var(--acid)' : '#f87171';
        }

        function togglePw(id, btn) {
            const inp = document.getElementById(id);
            inp.type = inp.type === 'password' ? 'text' : 'password';
            btn.style.opacity = inp.type === 'text' ? '1' : '.5';
        }
    </script>

    <style>
        @media (max-width: 900px) {
            div[style*="grid-template-columns:240px 1fr"] { grid-template-columns:1fr!important; }
            div[style*="grid-template-columns:repeat(3,1fr)"] { grid-template-columns:1fr 1fr!important; }
            div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns:1fr!important; }
        }
    </style>
</x-app-layout>
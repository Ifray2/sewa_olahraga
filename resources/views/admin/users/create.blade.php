<x-app-layout>
    <x-slot name="heading">Tambah Pengguna</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.users.index') }}">Pengguna</a>
        <span>/</span>
        <span>Tambah</span>
    </x-slot>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">

            {{-- LEFT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Data Diri --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Data Diri</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group span-2">
                                <label class="form-label">Nama Lengkap <span style="color:#f87171;">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                       class="form-control" placeholder="Nama lengkap pengguna">
                                @error('name') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group span-2">
                                <label class="form-label">Alamat Email <span style="color:#f87171;">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="form-control" placeholder="email@contoh.com">
                                @error('email') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       class="form-control" placeholder="08xx-xxxx-xxxx">
                                @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Role <span style="color:#f87171;">*</span></label>
                                <select name="role" id="role-select" class="form-control" onchange="updateRoleInfo(this)">
                                    <option value="user"    {{ old('role','user') === 'user'    ? 'selected':'' }}>👤 Pengguna (User)</option>
                                    <option value="petugas" {{ old('role') === 'petugas' ? 'selected':'' }}>🛠 Petugas</option>
                                    <option value="admin"   {{ old('role') === 'admin'   ? 'selected':'' }}>⚡ Admin</option>
                                </select>
                                @error('role') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group span-2">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" class="form-control" rows="3"
                                          placeholder="Alamat lengkap…">{{ old('address') }}</textarea>
                                @error('address') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Password --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Password</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group">
                                <label class="form-label">Password <span style="color:#f87171;">*</span></label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="pw1"
                                           class="form-control" placeholder="Min. 8 karakter"
                                           oninput="checkStrength(this.value);checkMatch()">
                                    <button type="button" onclick="togglePw('pw1',this)"
                                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--mid);cursor:pointer;font-size:.85rem;">
                                        👁
                                    </button>
                                </div>
                                {{-- Strength meter --}}
                                <div style="margin-top:.5rem;display:flex;gap:.2rem;">
                                    @for($b=0;$b<4;$b++)
                                        <div class="strength-bar" data-idx="{{ $b }}"
                                             style="flex:1;height:3px;border-radius:2px;background:var(--border);transition:background .2s;"></div>
                                    @endfor
                                </div>
                                <div id="strength-label" style="font-size:.68rem;color:var(--mid);margin-top:.25rem;"></div>
                                @error('password') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Konfirmasi Password <span style="color:#f87171;">*</span></label>
                                <div style="position:relative;">
                                    <input type="password" name="password_confirmation" id="pw2"
                                           class="form-control" placeholder="Ulangi password"
                                           oninput="checkMatch()">
                                    <button type="button" onclick="togglePw('pw2',this)"
                                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--mid);cursor:pointer;font-size:.85rem;">
                                        👁
                                    </button>
                                </div>
                                <div id="match-label" style="font-size:.68rem;margin-top:.25rem;"></div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;position:sticky;top:70px;">

                {{-- Preview & Actions --}}
                <div class="table-card">
                    <div style="padding:1.5rem;text-align:center;border-bottom:1px solid var(--border);">
                        <div id="avatar-preview" style="width:4rem;height:4rem;border-radius:50%;margin:0 auto .75rem;
                            background:var(--surface);border:2px solid var(--border);
                            display:flex;align-items:center;justify-content:center;
                            font-family:var(--font-display);font-size:2rem;color:var(--mid);
                            transition:all .2s;">
                            ?
                        </div>
                        <div id="name-preview" style="font-family:var(--font-display);font-size:1.1rem;text-transform:uppercase;color:var(--mid);letter-spacing:.05em;">
                            Nama Pengguna
                        </div>
                        <div id="role-badge-preview" style="margin-top:.5rem;display:inline-block;font-size:.7rem;padding:.2rem .65rem;border-radius:2px;background:var(--surface);color:var(--mid);border:1px solid var(--border);">
                            User
                        </div>
                    </div>
                    <div style="padding:1rem 1.25rem;display:flex;flex-direction:column;gap:.5rem;">
                        <button type="submit" class="btn-add" style="justify-content:center;padding:.75rem;">
                            Tambah Pengguna
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                            Batal
                        </a>
                    </div>
                </div>

                {{-- Role info --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Info Role</div>
                    </div>
                    <div id="role-info" style="padding:1.25rem;">
                        <div style="font-size:.8rem;color:var(--muted);line-height:1.7;" id="role-desc">
                            Pengguna biasa yang dapat menyewa alat, melakukan pembayaran, dan memberikan ulasan.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <script>
        const roleConfig = {
            user:    { label:'User',    color:'var(--surface)', textColor:'#9ca3af', borderColor:'var(--border)', desc:'Pengguna biasa yang dapat menyewa alat, melakukan pembayaran, dan memberikan ulasan.', avatarBg:'var(--surface)', avatarColor:'var(--mid)' },
            petugas: { label:'Petugas', color:'rgba(96,165,250,.12)', textColor:'#60a5fa', borderColor:'rgba(96,165,250,.35)', desc:'Staf yang dapat memproses transaksi, mengkonfirmasi penyewaan, dan mengelola pengembalian alat.', avatarBg:'rgba(96,165,250,.15)', avatarColor:'#60a5fa' },
            admin:   { label:'Admin',   color:'rgba(200,245,66,.12)', textColor:'var(--acid)', borderColor:'rgba(200,245,66,.35)', desc:'Akses penuh ke seluruh sistem. Dapat mengelola pengguna, alat, kategori, dan semua transaksi.', avatarBg:'var(--acid)', avatarColor:'var(--black)' },
        };

        function updateRoleInfo(sel) {
            const cfg = roleConfig[sel.value] || roleConfig.user;
            const badge = document.getElementById('role-badge-preview');
            badge.textContent = cfg.label;
            badge.style.color = cfg.textColor;
            badge.style.borderColor = cfg.borderColor;
            badge.style.background = cfg.color;
            document.getElementById('role-desc').textContent = cfg.desc;

            const av = document.getElementById('avatar-preview');
            av.style.background = cfg.avatarBg;
            av.style.color = cfg.avatarColor;
            av.style.borderColor = cfg.borderColor;
        }

        // Live name preview
        document.querySelector('input[name=name]').addEventListener('input', function() {
            const val = this.value.trim();
            document.getElementById('name-preview').textContent = val || 'Nama Pengguna';
            document.getElementById('avatar-preview').textContent = val ? val[0].toUpperCase() : '?';
        });

        // Password strength
        function checkStrength(pw) {
            let score = 0;
            if (pw.length >= 8) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[^A-Za-z0-9]/.test(pw)) score++;

            const bars   = document.querySelectorAll('.strength-bar');
            const colors = ['#f87171','#fbbf24','#60a5fa','var(--acid)'];
            const labels = ['Lemah','Cukup','Kuat','Sangat Kuat'];

            bars.forEach((b, i) => {
                b.style.background = i < score ? colors[score - 1] : 'var(--border)';
            });
            document.getElementById('strength-label').textContent = pw.length ? labels[score-1] ?? '' : '';
            document.getElementById('strength-label').style.color = pw.length ? colors[score-1] ?? 'var(--mid)' : 'var(--mid)';
        }

        function checkMatch() {
            const p1  = document.getElementById('pw1').value;
            const p2  = document.getElementById('pw2').value;
            const lbl = document.getElementById('match-label');
            if (!p2) { lbl.textContent = ''; return; }
            lbl.textContent = p1 === p2 ? '✓ Password cocok' : '✕ Password tidak cocok';
            lbl.style.color = p1 === p2 ? 'var(--acid)' : '#f87171';
        }

        function togglePw(id, btn) {
            const inp = document.getElementById(id);
            inp.type = inp.type === 'password' ? 'text' : 'password';
            btn.style.opacity = inp.type === 'text' ? '1' : '.5';
        }

        // Init
        updateRoleInfo(document.getElementById('role-select'));
        const nameVal = '{{ old("name") }}';
        if (nameVal) {
            document.getElementById('name-preview').textContent = nameVal;
            document.getElementById('avatar-preview').textContent = nameVal[0].toUpperCase();
        }
    </script>

    <style>
        @media (max-width: 900px) {
            form > div[style*="grid-template-columns:1fr 300px"] { grid-template-columns:1fr!important; }
        }
    </style>
</x-app-layout>
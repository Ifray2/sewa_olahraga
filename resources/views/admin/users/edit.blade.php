<x-app-layout>
    <x-slot name="heading">Edit Pengguna</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.users.index') }}">Pengguna</a>
        <span>/</span>
        <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
        <span>/</span>
        <span>Edit</span>
    </x-slot>

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">

            {{-- LEFT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Data Diri --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Data Diri</div>
                        @if($user->id === auth()->id())
                            <span style="font-size:.72rem;color:var(--acid);border:1px solid rgba(200,245,66,.3);padding:.2rem .6rem;border-radius:2px;">Akun Anda</span>
                        @endif
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group span-2">
                                <label class="form-label">Nama Lengkap <span style="color:#f87171;">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                       class="form-control" oninput="updatePreview(this.value)">
                                @error('name') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group span-2">
                                <label class="form-label">Alamat Email <span style="color:#f87171;">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="form-control">
                                @error('email') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                       class="form-control" placeholder="08xx-xxxx-xxxx">
                                @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Role <span style="color:#f87171;">*</span></label>
                                @if($user->id === auth()->id())
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                    <div class="form-control" style="opacity:.6;cursor:not-allowed;display:flex;align-items:center;gap:.5rem;">
                                        @if($user->isAdmin()) ⚡ Admin
                                        @elseif($user->isPetugas()) 🛠 Petugas
                                        @else 👤 User @endif
                                    </div>
                                    <div class="form-hint">Anda tidak dapat mengubah role akun sendiri.</div>
                                @else
                                    <select name="role" id="role-select" class="form-control" onchange="updateRolePreview(this)">
                                        <option value="user"    {{ old('role', $user->role) === 'user'    ? 'selected':'' }}>👤 Pengguna (User)</option>
                                        <option value="petugas" {{ old('role', $user->role) === 'petugas' ? 'selected':'' }}>🛠 Petugas</option>
                                        <option value="admin"   {{ old('role', $user->role) === 'admin'   ? 'selected':'' }}>⚡ Admin</option>
                                    </select>
                                @endif
                                @error('role') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group span-2">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" class="form-control" rows="3">{{ old('address', $user->address) }}</textarea>
                                @error('address') <div class="form-error">{{ $message }}</div> @enderror
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
                        @php
                            $avatarBg    = $user->isAdmin() ? 'var(--acid)' : ($user->isPetugas() ? 'rgba(96,165,250,.15)' : 'var(--surface)');
                            $avatarColor = $user->isAdmin() ? 'var(--black)' : ($user->isPetugas() ? '#60a5fa' : 'var(--mid)');
                            $avatarBdr   = $user->isAdmin() ? 'var(--acid)' : ($user->isPetugas() ? 'rgba(96,165,250,.4)' : 'var(--border)');
                        @endphp
                        <div id="avatar-preview" style="width:4rem;height:4rem;border-radius:50%;margin:0 auto .75rem;
                            background:{{ $avatarBg }};color:{{ $avatarColor }};border:2px solid {{ $avatarBdr }};
                            display:flex;align-items:center;justify-content:center;
                            font-family:var(--font-display);font-size:2rem;transition:all .2s;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div id="name-preview" style="font-family:var(--font-display);font-size:1.1rem;text-transform:uppercase;color:var(--white);letter-spacing:.05em;">
                            {{ $user->name }}
                        </div>
                        @php
                            $roleLabel = $user->isAdmin() ? 'Admin' : ($user->isPetugas() ? 'Petugas' : 'User');
                            $roleTextC = $user->isAdmin() ? 'var(--acid)' : ($user->isPetugas() ? '#60a5fa' : '#9ca3af');
                            $roleBgC   = $user->isAdmin() ? 'rgba(200,245,66,.1)' : ($user->isPetugas() ? 'rgba(96,165,250,.1)' : 'var(--surface)');
                            $roleBdrC  = $user->isAdmin() ? 'rgba(200,245,66,.3)' : ($user->isPetugas() ? 'rgba(96,165,250,.3)' : 'var(--border)');
                        @endphp
                        <div id="role-badge" style="margin-top:.5rem;display:inline-block;font-size:.7rem;padding:.2rem .65rem;border-radius:2px;
                            background:{{ $roleBgC }};color:{{ $roleTextC }};border:1px solid {{ $roleBdrC }};">
                            {{ $roleLabel }}
                        </div>
                    </div>
                    <div style="padding:1rem 1.25rem;display:flex;flex-direction:column;gap:.5rem;">
                        <button type="submit" class="btn-add" style="justify-content:center;padding:.75rem;">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                            Batal
                        </a>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="table-card">
                    <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.6rem;">
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">ID</span>
                            <span style="color:var(--muted);">#{{ $user->id }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">Bergabung</span>
                            <span style="color:var(--muted);">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">Email Verified</span>
                            @if($user->email_verified_at)
                                <span style="color:var(--acid);font-size:.75rem;">✓ {{ $user->email_verified_at->format('d M Y') }}</span>
                            @else
                                <span style="color:#fbbf24;font-size:.75rem;">Belum diverifikasi</span>
                            @endif
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">Total Transaksi</span>
                            <span style="color:var(--white);">{{ $user->rentals()->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Danger zone --}}
                @if($user->id !== auth()->id())
                    <div class="table-card" style="border-color:rgba(248,113,113,.2);">
                        <div class="table-card-header" style="border-color:rgba(248,113,113,.2);">
                            <div class="table-card-title" style="color:#f87171;">Zona Berbahaya</div>
                        </div>
                        <div style="padding:1.25rem;">
                            <div style="font-size:.78rem;color:var(--mid);margin-bottom:.75rem;">
                                Hapus akun pengguna ini secara permanen.
                            </div>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Hapus pengguna ini secara permanen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-delete"
                                        style="width:100%;justify-content:center;padding:.6rem;font-size:.78rem;border-width:1px;">
                                    Hapus Pengguna
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </form>

    <script>
        const roleConfig = {
            user:    { label:'User',    textColor:'#9ca3af', borderColor:'var(--border)',          bgColor:'var(--surface)',             avatarBg:'var(--surface)',          avatarColor:'var(--mid)',   avatarBdr:'var(--border)' },
            petugas: { label:'Petugas', textColor:'#60a5fa', borderColor:'rgba(96,165,250,.3)',     bgColor:'rgba(96,165,250,.1)',         avatarBg:'rgba(96,165,250,.15)',     avatarColor:'#60a5fa',     avatarBdr:'rgba(96,165,250,.4)' },
            admin:   { label:'Admin',   textColor:'var(--acid)', borderColor:'rgba(200,245,66,.3)', bgColor:'rgba(200,245,66,.1)',         avatarBg:'var(--acid)',              avatarColor:'var(--black)', avatarBdr:'var(--acid)' },
        };

        function updateRolePreview(sel) {
            if (!sel) return;
            const cfg   = roleConfig[sel.value] || roleConfig.user;
            const badge = document.getElementById('role-badge');
            if (badge) {
                badge.textContent    = cfg.label;
                badge.style.color    = cfg.textColor;
                badge.style.borderColor = cfg.borderColor;
                badge.style.background  = cfg.bgColor;
            }
            const av = document.getElementById('avatar-preview');
            if (av) {
                av.style.background  = cfg.avatarBg;
                av.style.color       = cfg.avatarColor;
                av.style.borderColor = cfg.avatarBdr;
            }
        }

        function updatePreview(val) {
            const av = document.getElementById('avatar-preview');
            const nv = document.getElementById('name-preview');
            nv.textContent = val || '{{ $user->name }}';
            av.textContent = (val || '{{ $user->name }}')[0].toUpperCase();
        }

        // Init
        const roleSelect = document.getElementById('role-select');
        if (roleSelect) updateRolePreview(roleSelect);
    </script>

    <style>
        @media (max-width: 900px) {
            form > div[style*="grid-template-columns:1fr 300px"] { grid-template-columns:1fr!important; }
        }
    </style>
</x-app-layout>
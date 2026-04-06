<x-app-layout>
    <x-slot name="heading">Pengguna</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <span>Pengguna</span>
    </x-slot>

    {{-- Role tabs --}}
    <div style="display:flex;gap:1px;background:var(--border);border:1px solid var(--border);border-radius:4px;overflow:hidden;margin-bottom:1.5rem;">
        @php
            $tabs = [
                ''        => ['label' => 'Semua',    'count' => $summary['all'],     'color' => 'var(--white)'],
                'admin'   => ['label' => 'Admin',    'count' => $summary['admin'],   'color' => 'var(--acid)'],
                'petugas' => ['label' => 'Petugas',  'count' => $summary['petugas'], 'color' => '#60a5fa'],
                'user'    => ['label' => 'Pengguna', 'count' => $summary['user'],    'color' => '#9ca3af'],
            ];
            $cur = request('role', '');
        @endphp
        @foreach($tabs as $val => $tab)
            <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => $val ?: null])) }}"
               style="flex:1;padding:.85rem .5rem;text-align:center;text-decoration:none;
                      background:{{ $cur === $val ? 'rgba(200,245,66,.07)' : 'var(--card)' }};
                      border-bottom:2px solid {{ $cur === $val ? 'var(--acid)' : 'transparent' }};
                      transition:background .15s;">
                <div style="font-family:var(--font-display);font-size:1.4rem;color:{{ $tab['color'] }};line-height:1;">{{ $tab['count'] }}</div>
                <div style="font-size:.65rem;letter-spacing:.1em;text-transform:uppercase;color:{{ $cur === $val ? 'var(--acid)' : 'var(--mid)' }};margin-top:.2rem;">
                    {{ $tab['label'] }}
                </div>
            </a>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Daftar Pengguna</div>
            <div class="table-card-actions">
                <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
                    @if(request('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="search-input" placeholder="Nama / email / telepon…">
                    <button type="submit" class="btn-outline">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('admin.users.index', request('role') ? ['role'=>request('role')] : []) }}" class="btn-outline">Reset</a>
                    @endif
                </form>
                <a href="{{ route('admin.users.create') }}" class="btn-add">+ Tambah Pengguna</a>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Telepon</th>
                    <th>Transaksi</th>
                    <th>Ulasan</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <div style="width:2.2rem;height:2.2rem;border-radius:50%;flex-shrink:0;
                                    background:{{ $user->isAdmin() ? 'var(--acid)' : ($user->isPetugas() ? 'rgba(96,165,250,.15)' : 'var(--surface)') }};
                                    color:{{ $user->isAdmin() ? 'var(--black)' : ($user->isPetugas() ? '#60a5fa' : 'var(--mid)') }};
                                    border:1px solid {{ $user->isAdmin() ? 'var(--acid)' : ($user->isPetugas() ? 'rgba(96,165,250,.3)' : 'var(--border)') }};
                                    display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.82rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="td-name" style="{{ $user->id === auth()->id() ? 'color:var(--acid);' : '' }}">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span style="font-size:.65rem;color:var(--mid);font-weight:400;">(Anda)</span>
                                        @endif
                                    </div>
                                    <div style="font-size:.72rem;color:var(--mid);">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge badge-active">Admin</span>
                            @elseif($user->isPetugas())
                                <span class="badge badge-confirmed">Petugas</span>
                            @else
                                <span class="badge badge-returned">User</span>
                            @endif
                        </td>
                        <td style="font-size:.8rem;color:var(--muted);">
                            {{ $user->phone ?? '—' }}
                        </td>
                        <td>
                            <span style="font-family:var(--font-display);font-size:1.1rem;color:var(--white);">
                                {{ $user->rentals_count }}
                            </span>
                        </td>
                        <td>
                            <span style="font-family:var(--font-display);font-size:1.1rem;color:var(--white);">
                                {{ $user->reviews_count }}
                            </span>
                        </td>
                        <td style="font-size:.75rem;color:var(--mid);white-space:nowrap;">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn-action btn-view">Lihat</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn-action btn-edit">Edit</a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline;"
                                          onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:3rem;color:var(--mid);">
                            <div style="font-size:2rem;margin-bottom:.75rem;">👤</div>
                            <div>Tidak ada pengguna ditemukan.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-footer">
            <div class="table-info">
                Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna
            </div>
            <div class="pagination">
                @if($users->onFirstPage())
                    <span class="page-btn" style="opacity:.3;">‹</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="page-btn">‹</a>
                @endif
                @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="page-btn {{ $page == $users->currentPage() ? 'active':'' }}">{{ $page }}</a>
                @endforeach
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="page-btn">›</a>
                @else
                    <span class="page-btn" style="opacity:.3;">›</span>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
@extends('layouts.petugas')

@section('content')

    <div style="max-width:640px;">
        <div class="table-card">
            <div class="table-card-header">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    @if($category->icon)
                        <span style="font-size:2rem;line-height:1;">{{ $category->icon }}</span>
                    @endif
                    <div class="table-card-title">Edit: {{ $category->name }}</div>
                </div>
                <div style="display:flex;gap:.5rem;">
                    <a href="{{ route('petugas.categories.show', $category) }}" class="btn-outline">Lihat Detail</a>
                </div>
            </div>

            <form method="POST" action="{{ route('petugas.categories.update', $category) }}" style="padding:1.75rem;">
                @csrf
                @method('PUT')

                <div class="form-grid cols-1" style="gap:1.25rem;">

                    {{-- Nama --}}
                    <div class="form-group">
                        <label class="form-label">Nama Kategori <span style="color:#f87171;">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}"
                               class="form-control" placeholder="cth. Sepeda & MTB" autofocus>
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Icon & Status --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                        <div class="form-group">
                            <label class="form-label">Icon (Emoji)</label>
                            <input type="text" name="icon" value="{{ old('icon', $category->icon) }}"
                                   class="form-control" placeholder="cth. 🚵" maxlength="10"
                                   style="font-size:1.4rem;">
                            <div class="form-hint">Satu karakter emoji untuk ikon kategori.</div>
                            @error('icon')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <div style="display:flex;align-items:center;gap:.75rem;height:2.35rem;margin-top:.1rem;">
                                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.85rem;color:var(--muted);">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1"
                                           id="is_active"
                                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                           style="accent-color:var(--acid);width:1rem;height:1rem;">
                                    Aktifkan kategori
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="4"
                                  placeholder="Deskripsi singkat mengenai kategori ini…">{{ old('description', $category->description) }}</textarea>
                        <div class="form-hint">Opsional. Maksimal 500 karakter.</div>
                        @error('description')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- Slug preview --}}
                <div style="margin-top:1.25rem;padding:.85rem 1rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                    <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.3rem;">Slug</div>
                    <div id="slug-preview" style="font-size:.85rem;color:var(--acid);font-family:monospace;">{{ $category->slug }}</div>
                </div>

                {{-- Meta info --}}
                <div style="margin-top:1.25rem;display:flex;gap:2rem;padding:.85rem 1rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                    <div>
                        <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Dibuat</div>
                        <div style="font-size:.82rem;color:var(--muted);">{{ $category->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div>
                        <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Diperbarui</div>
                        <div style="font-size:.82rem;color:var(--muted);">{{ $category->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div>
                        <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Jumlah Alat</div>
                        <div style="font-size:.82rem;color:var(--acid);">{{ $category->equipment()->count() }} alat</div>
                    </div>
                </div>

                <div style="display:flex;gap:.75rem;margin-top:1.75rem;padding-top:1.5rem;border-top:1px solid var(--border);">
                    <button type="submit" class="btn-add" style="padding:.7rem 1.75rem;font-size:.85rem;">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('petugas.categories.index') }}" class="btn-outline" style="padding:.7rem 1.25rem;">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Danger zone --}}
        <div class="table-card" style="margin-top:1.5rem;border-color:rgba(248,113,113,.2);">
            <div class="table-card-header" style="border-color:rgba(248,113,113,.2);">
                <div class="table-card-title" style="color:#f87171;">Zona Berbahaya</div>
            </div>
            <div style="padding:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                <div>
                    <div style="font-size:.85rem;color:var(--muted);margin-bottom:.25rem;">Hapus Kategori</div>
                    <div style="font-size:.75rem;color:var(--mid);">
                        Kategori hanya bisa dihapus jika tidak memiliki alat terdaftar.
                    </div>
                </div>
                <form method="POST" action="{{ route('petugas.categories.destroy', $category) }}"
                      onsubmit="return confirm('Yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-action btn-delete"
                            style="padding:.55rem 1.1rem;font-size:.78rem;">
                        Hapus Kategori
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const nameInput = document.querySelector('input[name="name"]');
        const slugPreview = document.getElementById('slug-preview');

        function toSlug(str) {
            return str.toLowerCase().trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }

        nameInput.addEventListener('input', () => {
            slugPreview.textContent = toSlug(nameInput.value) || '—';
        });
    </script>
@endsection
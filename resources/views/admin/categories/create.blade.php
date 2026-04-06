<x-app-layout>
    <x-slot name="heading">Tambah Kategori</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.categories.index') }}">Kategori</a>
        <span>/</span>
        <span>Tambah</span>
    </x-slot>

    <div style="max-width:640px;">
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">Form Tambah Kategori</div>
            </div>

            <form method="POST" action="{{ route('admin.categories.store') }}" style="padding:1.75rem;">
                @csrf

                <div class="form-grid cols-1" style="gap:1.25rem;">

                    {{-- Nama --}}
                    <div class="form-group">
                        <label class="form-label">Nama Kategori <span style="color:#f87171;">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control" placeholder="cth. Sepeda & MTB" autofocus>
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Icon & Status --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                        <div class="form-group">
                            <label class="form-label">Icon (Emoji)</label>
                            <input type="text" name="icon" value="{{ old('icon') }}"
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
                                           id="is_active" {{ old('is_active', '1') ? 'checked' : '' }}
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
                                  placeholder="Deskripsi singkat mengenai kategori ini…">{{ old('description') }}</textarea>
                        <div class="form-hint">Opsional. Maksimal 500 karakter.</div>
                        @error('description')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- Preview slug --}}
                <div style="margin-top:1.25rem;padding:.85rem 1rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                    <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.3rem;">Slug yang akan dibuat</div>
                    <div id="slug-preview" style="font-size:.85rem;color:var(--acid);font-family:monospace;">—</div>
                </div>

                <div style="display:flex;gap:.75rem;margin-top:1.75rem;padding-top:1.5rem;border-top:1px solid var(--border);">
                    <button type="submit" class="btn-add" style="padding:.7rem 1.75rem;font-size:.85rem;">
                        Simpan Kategori
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn-outline" style="padding:.7rem 1.25rem;">
                        Batal
                    </a>
                </div>
            </form>
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
            const slug = toSlug(nameInput.value);
            slugPreview.textContent = slug || '—';
        });
    </script>
</x-app-layout>
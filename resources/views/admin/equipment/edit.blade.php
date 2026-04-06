<x-app-layout>
    <x-slot name="heading">Edit Alat</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.equipment.index') }}">Alat Olahraga</a>
        <span>/</span>
        <span>Edit: {{ $equipment->name }}</span>
    </x-slot>

    <form method="POST" action="{{ route('admin.equipment.update', $equipment) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start;">

            {{-- ── LEFT ── --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Info Dasar --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Informasi Dasar</div>
                        <a href="{{ route('admin.equipment.show', $equipment) }}" class="btn-outline">Lihat Detail</a>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group span-2">
                                <label class="form-label">Nama Alat <span style="color:#f87171;">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $equipment->name) }}"
                                       class="form-control" autofocus>
                                @error('name') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kategori <span style="color:#f87171;">*</span></label>
                                <select name="category_id" class="form-control">
                                    <option value="">— Pilih Kategori —</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('category_id', $equipment->category_id) == $cat->id ? 'selected':'' }}>
                                            {{ $cat->icon }} {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Merek / Brand</label>
                                <input type="text" name="brand" value="{{ old('brand', $equipment->brand) }}"
                                       class="form-control" placeholder="cth. Trek, Shimano…">
                                @error('brand') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group span-2">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description', $equipment->description) }}</textarea>
                                @error('description') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>

                        <div style="margin-top:1rem;padding:.75rem 1rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                            <div style="font-size:.62rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Slug</div>
                            <div id="slug-preview" style="font-size:.82rem;color:var(--acid);font-family:monospace;">{{ $equipment->slug }}</div>
                        </div>
                    </div>
                </div>

                {{-- Harga & Stok --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Harga & Stok</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group">
                                <label class="form-label">Harga Sewa per Hari (Rp) <span style="color:#f87171;">*</span></label>
                                <div style="position:relative;">
                                    <span style="position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:var(--mid);font-size:.82rem;pointer-events:none;">Rp</span>
                                    <input type="number" name="price_per_day"
                                           value="{{ old('price_per_day', $equipment->price_per_day) }}"
                                           class="form-control" min="1000" step="1000" style="padding-left:2.5rem;">
                                </div>
                                <div class="form-hint" id="price-hint">
                                    Rp {{ number_format($equipment->price_per_day, 0, ',', '.') }} / hari
                                </div>
                                @error('price_per_day') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Stok Unit <span style="color:#f87171;">*</span></label>
                                <input type="number" name="stock" value="{{ old('stock', $equipment->stock) }}"
                                       class="form-control" min="0">
                                @error('stock') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>

                        {{-- Meta --}}
                        <div style="margin-top:1.25rem;display:flex;gap:2rem;padding:.85rem 1rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                            <div>
                                <div style="font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Dibuat</div>
                                <div style="font-size:.8rem;color:var(--muted);">{{ $equipment->created_at->format('d M Y') }}</div>
                            </div>
                            <div>
                                <div style="font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Diperbarui</div>
                                <div style="font-size:.8rem;color:var(--muted);">{{ $equipment->updated_at->format('d M Y') }}</div>
                            </div>
                            <div>
                                <div style="font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Total Sewa</div>
                                <div style="font-size:.8rem;color:var(--acid);">{{ $equipment->rentals()->count() }}×</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT ── --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Publish --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Publikasi</div>
                    </div>
                    <div style="padding:1.25rem;">
                        <div style="margin-bottom:1rem;">
                            <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;padding:.6rem .75rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                                <span style="font-size:.82rem;color:var(--muted);">Tersedia untuk disewa</span>
                                <input type="hidden" name="is_available" value="0">
                                <input type="checkbox" name="is_available" value="1" id="is_available"
                                       {{ old('is_available', $equipment->is_available) ? 'checked':'' }}
                                       style="accent-color:var(--acid);width:1.1rem;height:1.1rem;">
                            </label>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Kondisi Alat <span style="color:#f87171;">*</span></label>
                            <select name="condition" class="form-control">
                                <option value="good" {{ old('condition',$equipment->condition)==='good' ? 'selected':'' }}>✓ Baik (Good)</option>
                                <option value="fair" {{ old('condition',$equipment->condition)==='fair' ? 'selected':'' }}>~ Cukup (Fair)</option>
                                <option value="poor" {{ old('condition',$equipment->condition)==='poor' ? 'selected':'' }}>! Perlu Perbaikan (Poor)</option>
                            </select>
                            @error('condition') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:.5rem;">
                        <button type="submit" class="btn-add" style="justify-content:center;padding:.7rem;">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.equipment.index') }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                            Batal
                        </a>
                    </div>
                </div>

                {{-- Foto --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Foto Alat</div>
                    </div>
                    <div style="padding:1.25rem;">

                        {{-- Current image --}}
                        @if($equipment->image)
                            <div style="margin-bottom:1rem;position:relative;">
                                <img src="{{ Storage::url($equipment->image) }}" alt="{{ $equipment->name }}"
                                     style="width:100%;border-radius:2px;max-height:180px;object-fit:cover;border:1px solid var(--border);"
                                     id="current-image">
                                <label style="display:flex;align-items:center;gap:.4rem;margin-top:.6rem;cursor:pointer;font-size:.78rem;color:#f87171;">
                                    <input type="checkbox" name="remove_image" value="1" id="remove-image"
                                           style="accent-color:#f87171;" onchange="toggleRemoveImage(this)">
                                    Hapus foto ini
                                </label>
                            </div>
                        @endif

                        {{-- Upload zone --}}
                        <div id="drop-zone"
                             style="border:2px dashed var(--border);border-radius:4px;padding:1.5rem 1rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
                             onclick="document.getElementById('image-input').click()"
                             ondragover="event.preventDefault();this.style.borderColor='var(--acid)'"
                             ondragleave="this.style.borderColor='var(--border)'"
                             ondrop="handleDrop(event)">
                            <div id="drop-content">
                                <div style="font-size:1.5rem;margin-bottom:.4rem;">📷</div>
                                <div style="font-size:.78rem;color:var(--mid);">{{ $equipment->image ? 'Ganti foto' : 'Upload foto' }}</div>
                                <div style="font-size:.68rem;color:var(--mid);margin-top:.2rem;">JPG, PNG, WebP · Maks 2MB</div>
                            </div>
                            <img id="image-preview" src="" alt=""
                                 style="display:none;width:100%;border-radius:2px;max-height:160px;object-fit:cover;">
                        </div>
                        <input type="file" name="image" id="image-input" accept="image/*"
                               style="display:none;" onchange="previewImage(this)">
                        <div id="file-name" style="font-size:.72rem;color:var(--mid);margin-top:.4rem;text-align:center;"></div>
                        @error('image') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- Danger zone --}}
        <div class="table-card" style="margin-top:1.5rem;border-color:rgba(248,113,113,.2);">
            <div class="table-card-header" style="border-color:rgba(248,113,113,.2);">
                <div class="table-card-title" style="color:#f87171;">Zona Berbahaya</div>
            </div>
            <div style="padding:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                <div>
                    <div style="font-size:.85rem;color:var(--muted);margin-bottom:.25rem;">Hapus Alat Ini</div>
                    <div style="font-size:.75rem;color:var(--mid);">
                        Tidak dapat dihapus jika masih ada transaksi pending, confirmed, atau active.
                    </div>
                </div>
                <button type="button" class="btn-action btn-delete"
                        style="padding:.55rem 1.1rem;font-size:.78rem;border-width:1px;"
                        onclick="document.getElementById('delete-form').submit()">
                    Hapus Alat
                </button>
            </div>
        </div>
    </form>

    <form id="delete-form" method="POST" action="{{ route('admin.equipment.destroy', $equipment) }}"
          onsubmit="return confirm('Yakin ingin menghapus alat ini? Tindakan ini tidak dapat dibatalkan.')">
        @csrf @method('DELETE')
    </form>

    <script>
        const nameInput  = document.querySelector('input[name="name"]');
        const slugPrev   = document.getElementById('slug-preview');
        const priceInput = document.querySelector('input[name="price_per_day"]');
        const priceHint  = document.getElementById('price-hint');

        function toSlug(s) {
            return s.toLowerCase().trim().replace(/[^a-z0-9\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-');
        }
        nameInput.addEventListener('input', () => { slugPrev.textContent = toSlug(nameInput.value) || '—'; });
        priceInput.addEventListener('input', () => {
            const v = parseInt(priceInput.value);
            priceHint.textContent = v ? 'Rp ' + v.toLocaleString('id-ID') + ' / hari' : '—';
        });

        function previewImage(input) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview').style.display = 'block';
                document.getElementById('drop-content').style.display  = 'none';
                document.getElementById('file-name').textContent = file.name + ' (' + (file.size/1024).toFixed(1) + ' KB)';
            };
            reader.readAsDataURL(file);
        }
        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('drop-zone').style.borderColor = 'var(--border)';
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const dt = new DataTransfer(); dt.items.add(file);
                document.getElementById('image-input').files = dt.files;
                previewImage(document.getElementById('image-input'));
            }
        }
        function toggleRemoveImage(cb) {
            const img = document.getElementById('current-image');
            if (img) img.style.opacity = cb.checked ? '.3' : '1';
        }
    </script>

    <style>
        @media (max-width: 900px) {
            form > div[style*="grid-template-columns:1fr 340px"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
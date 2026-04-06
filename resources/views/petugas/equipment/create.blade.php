@extends('layouts.petugas')

@section('content')

    <form method="POST" action="{{ route('petugas.equipment.store') }}" enctype="multipart/form-data">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start;">

            {{-- ── LEFT: Form utama ── --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Info Dasar --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Informasi Dasar</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group span-2">
                                <label class="form-label">Nama Alat <span style="color:#f87171;">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                       class="form-control" placeholder="cth. MTB Full Suspension Trek X-Caliber" autofocus>
                                @error('name') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kategori <span style="color:#f87171;">*</span></label>
                                <select name="category_id" class="form-control">
                                    <option value="">— Pilih Kategori —</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ (old('category_id', $selectedCat) == $cat->id) ? 'selected' : '' }}>
                                            {{ $cat->icon }} {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Merek / Brand</label>
                                <input type="text" name="brand" value="{{ old('brand') }}"
                                       class="form-control" placeholder="cth. Trek, Shimano, Osprey…">
                                @error('brand') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group span-2">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="4"
                                          placeholder="Jelaskan spesifikasi, kondisi, cara penggunaan, dll…">{{ old('description') }}</textarea>
                                <div class="form-hint">Opsional. Maksimal 1000 karakter.</div>
                                @error('description') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>

                        {{-- Slug preview --}}
                        <div style="margin-top:1rem;padding:.75rem 1rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                            <div style="font-size:.62rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Slug</div>
                            <div id="slug-preview" style="font-size:.82rem;color:var(--acid);font-family:monospace;">—</div>
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
                                    <input type="number" name="price_per_day" value="{{ old('price_per_day') }}"
                                           class="form-control" placeholder="50000" min="1000" step="1000"
                                           style="padding-left:2.5rem;">
                                </div>
                                <div class="form-hint" id="price-hint">—</div>
                                @error('price_per_day') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Stok Unit <span style="color:#f87171;">*</span></label>
                                <input type="number" name="stock" value="{{ old('stock', 1) }}"
                                       class="form-control" min="0" placeholder="1">
                                @error('stock') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT: Sidebar settings ── --}}
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
                                       {{ old('is_available', '1') ? 'checked' : '' }}
                                       style="accent-color:var(--acid);width:1.1rem;height:1.1rem;">
                            </label>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Kondisi Alat <span style="color:#f87171;">*</span></label>
                            <select name="condition" class="form-control">
                                <option value="good"  {{ old('condition','good') === 'good' ? 'selected':'' }}>✓ Baik (Good)</option>
                                <option value="fair"  {{ old('condition') === 'fair' ? 'selected':'' }}>~ Cukup (Fair)</option>
                                <option value="poor"  {{ old('condition') === 'poor' ? 'selected':'' }}>! Perlu Perbaikan (Poor)</option>
                            </select>
                            @error('condition') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:.5rem;">
                        <button type="submit" class="btn-add" style="justify-content:center;padding:.7rem;">
                            Simpan Alat
                        </button>
                        <a href="{{ route('petugas.equipment.index') }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                            Batal
                        </a>
                    </div>
                </div>

                {{-- Upload Gambar --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Foto Alat</div>
                    </div>
                    <div style="padding:1.25rem;">
                        {{-- Drop zone --}}
                        <div id="drop-zone"
                             style="border:2px dashed var(--border);border-radius:4px;padding:2rem 1rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
                             onclick="document.getElementById('image-input').click()"
                             ondragover="event.preventDefault();this.style.borderColor='var(--acid)';this.style.background='rgba(200,245,66,.04)'"
                             ondragleave="this.style.borderColor='var(--border)';this.style.background='transparent'"
                             ondrop="handleDrop(event)">
                            <div id="drop-content">
                                <div style="font-size:2rem;margin-bottom:.5rem;">📷</div>
                                <div style="font-size:.8rem;color:var(--mid);">Klik atau seret foto ke sini</div>
                                <div style="font-size:.7rem;color:var(--mid);margin-top:.25rem;">JPG, PNG, WebP · Maks 2MB</div>
                            </div>
                            <img id="image-preview" src="" alt=""
                                 style="display:none;width:100%;border-radius:2px;max-height:180px;object-fit:cover;">
                        </div>
                        <input type="file" name="image" id="image-input" accept="image/*"
                               style="display:none;" onchange="previewImage(this)">
                        <div id="file-name" style="font-size:.72rem;color:var(--mid);margin-top:.5rem;text-align:center;"></div>
                        @error('image') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

            </div>
        </div><!-- /grid -->
    </form>

    <script>
        // Slug preview
        const nameInput = document.querySelector('input[name="name"]');
        const slugPreview = document.getElementById('slug-preview');
        function toSlug(s) {
            return s.toLowerCase().trim().replace(/[^a-z0-9\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-');
        }
        nameInput.addEventListener('input', () => {
            slugPreview.textContent = toSlug(nameInput.value) || '—';
        });

        // Price hint
        const priceInput = document.querySelector('input[name="price_per_day"]');
        const priceHint  = document.getElementById('price-hint');
        priceInput.addEventListener('input', () => {
            const v = parseInt(priceInput.value);
            priceHint.textContent = v ? 'Rp ' + v.toLocaleString('id-ID') + ' / hari' : '—';
        });

        // Image preview
        function previewImage(input) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview').style.display = 'block';
                document.getElementById('drop-content').style.display = 'none';
                document.getElementById('file-name').textContent = file.name + ' (' + (file.size/1024).toFixed(1) + ' KB)';
            };
            reader.readAsDataURL(file);
        }
        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('drop-zone').style.borderColor = 'var(--border)';
            document.getElementById('drop-zone').style.background = 'transparent';
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const dt = new DataTransfer();
                dt.items.add(file);
                document.getElementById('image-input').files = dt.files;
                previewImage(document.getElementById('image-input'));
            }
        }
    </script>

    <style>
        @media (max-width: 900px) {
            form > div[style*="grid-template-columns:1fr 340px"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endsection
<x-app-layout>
    <x-slot name="heading">Tambah Ulasan</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.reviews.index') }}">Ulasan</a>
        <span>/</span>
        <span>Tambah</span>
    </x-slot>

    <form method="POST" action="{{ route('admin.reviews.store') }}">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">

            {{-- LEFT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Pilih Transaksi --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Pilih Transaksi</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-group">
                            <label class="form-label">Transaksi Selesai <span style="color:#f87171;">*</span></label>
                            <select name="rental_id" id="rental-select" class="form-control" onchange="fillRental(this)">
                                <option value="">— Pilih Transaksi —</option>
                                @foreach($rentals as $rental)
                                    <option value="{{ $rental->id }}"
                                        data-code="{{ $rental->rental_code }}"
                                        data-user="{{ $rental->user->name }}"
                                        data-equipment="{{ $rental->equipment->name }}"
                                        data-category="{{ $rental->equipment->category->name }}"
                                        data-icon="{{ $rental->equipment->category->icon ?? '🏄' }}"
                                        data-price="{{ $rental->equipment->price_per_day }}"
                                        data-duration="{{ $rental->duration_days }}"
                                        data-total="{{ $rental->total_price }}"
                                        data-returned="{{ $rental->returned_at?->format('d M Y') ?? '—' }}"
                                        {{ old('rental_id') == $rental->id ? 'selected':'' }}>
                                        {{ $rental->rental_code }} — {{ $rental->user->name }} · {{ $rental->equipment->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint">Hanya menampilkan transaksi dengan status Dikembalikan yang belum diulas.</div>
                            @error('rental_id') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        {{-- Preview --}}
                        <div id="rental-preview" style="display:none;margin-top:1.25rem;">
                            <div style="display:flex;gap:1rem;padding:1.25rem;background:var(--surface);border-radius:4px;border:1px solid var(--border);">
                                <div id="rp-icon" style="width:3.5rem;height:3.5rem;border-radius:2px;background:var(--card);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:1.8rem;flex-shrink:0;">
                                    🏄
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div id="rp-equipment" style="font-family:var(--font-display);font-size:1.05rem;text-transform:uppercase;color:var(--white);">—</div>
                                    <div id="rp-category" style="font-size:.72rem;color:var(--mid);margin:.15rem 0;">—</div>
                                    <div id="rp-user" style="font-size:.78rem;color:var(--muted);">—</div>
                                </div>
                                <div style="text-align:right;flex-shrink:0;">
                                    <div id="rp-total" style="font-family:var(--font-display);font-size:1.1rem;color:var(--acid);">—</div>
                                    <div id="rp-duration" style="font-size:.68rem;color:var(--mid);margin-top:.2rem;">—</div>
                                    <div id="rp-returned" style="font-size:.68rem;color:var(--mid);margin-top:.1rem;">—</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rating & Komentar --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Rating &amp; Komentar</div>
                    </div>
                    <div style="padding:1.75rem;">

                        <div class="form-group" style="margin-bottom:1.5rem;">
                            <label class="form-label">Rating <span style="color:#f87171;">*</span></label>

                            {{-- Interactive star picker --}}
                            <div id="star-picker" style="display:flex;gap:.35rem;margin:.75rem 0;cursor:pointer;">
                                @for($s = 1; $s <= 5; $s++)
                                    <span class="star-btn" data-val="{{ $s }}"
                                          style="font-size:2.5rem;color:{{ old('rating', 0) >= $s ? '#fbbf24' : 'var(--border)' }};transition:color .1s;user-select:none;line-height:1;">★</span>
                                @endfor
                            </div>

                            <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}">
                            <div id="rating-label" style="font-size:.78rem;color:var(--mid);min-height:1.2em;">
                                {{ old('rating') ? ['','Tidak Puas','Kurang Puas','Cukup','Puas','Sangat Puas'][old('rating')] : 'Pilih rating di atas' }}
                            </div>
                            @error('rating') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Komentar (Opsional)</label>
                            <textarea name="comment" class="form-control" rows="5"
                                      placeholder="Ceritakan pengalaman menyewa alat ini…"
                                      maxlength="1000"
                                      oninput="document.getElementById('char-count').textContent = this.value.length">{{ old('comment') }}</textarea>
                            <div style="display:flex;justify-content:flex-end;margin-top:.3rem;">
                                <span id="char-count" style="font-size:.7rem;color:var(--mid);">{{ strlen(old('comment','')) }}</span>
                                <span style="font-size:.7rem;color:var(--mid);">/1000</span>
                            </div>
                            @error('comment') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;position:sticky;top:70px;">

                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Pratinjau</div>
                    </div>
                    <div style="padding:1.25rem;text-align:center;">
                        {{-- Star preview --}}
                        <div id="preview-stars" style="font-size:2rem;letter-spacing:.1em;color:var(--border);margin-bottom:.5rem;">
                            ★★★★★
                        </div>
                        <div id="preview-label" style="font-family:var(--font-display);font-size:1.5rem;color:var(--mid);margin-bottom:1rem;">
                            —
                        </div>
                        <div id="preview-comment" style="font-size:.8rem;color:var(--mid);font-style:italic;display:none;padding:.75rem;background:var(--surface);border-radius:2px;border-left:2px solid var(--acid);text-align:left;line-height:1.6;">
                        </div>
                    </div>
                    <div style="padding:.75rem 1.25rem;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:.5rem;">
                        <button type="submit" class="btn-add" style="justify-content:center;padding:.75rem;">
                            Simpan Ulasan
                        </button>
                        <a href="{{ route('admin.reviews.index') }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                            Batal
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <script>
        const ratingLabels = ['','Tidak Puas','Kurang Puas','Cukup','Puas','Sangat Puas'];
        const ratingColors = ['','#f87171','#f87171','#fbbf24','var(--acid)','var(--acid)'];
        let currentRating  = parseInt('{{ old("rating", 0) }}') || 0;

        // ── Star picker ──────────────────────────────────────────────────────
        const stars = document.querySelectorAll('.star-btn');

        function paintStars(val) {
            stars.forEach(s => {
                s.style.color = parseInt(s.dataset.val) <= val ? '#fbbf24' : 'var(--border)';
            });
        }

        stars.forEach(star => {
            star.addEventListener('mouseover', () => paintStars(parseInt(star.dataset.val)));
            star.addEventListener('mouseleave', () => paintStars(currentRating));
            star.addEventListener('click', () => {
                currentRating = parseInt(star.dataset.val);
                document.getElementById('rating-input').value = currentRating;
                document.getElementById('rating-label').textContent = ratingLabels[currentRating];
                updatePreview();
            });
        });

        paintStars(currentRating);

        // ── Preview update ───────────────────────────────────────────────────
        function updatePreview() {
            const starsEl   = document.getElementById('preview-stars');
            const labelEl   = document.getElementById('preview-label');
            const commentEl = document.getElementById('preview-comment');
            const commentTx = document.querySelector('textarea[name=comment]').value.trim();

            if (currentRating) {
                let html = '';
                for (let i=1;i<=5;i++) html += `<span style="color:${i<=currentRating?'#fbbf24':'var(--border)'}">★</span>`;
                starsEl.innerHTML = html;
                labelEl.textContent = ratingLabels[currentRating];
                labelEl.style.color = ratingColors[currentRating];
            } else {
                starsEl.innerHTML = '★★★★★';
                starsEl.style.color = 'var(--border)';
                labelEl.textContent = '—';
                labelEl.style.color = 'var(--mid)';
            }

            if (commentTx) {
                commentEl.textContent  = '"' + commentTx + '"';
                commentEl.style.display = 'block';
            } else {
                commentEl.style.display = 'none';
            }
        }

        document.querySelector('textarea[name=comment]').addEventListener('input', updatePreview);
        updatePreview();

        // ── Rental info fill ─────────────────────────────────────────────────
        function fillRental(sel) {
            const opt     = sel.options[sel.selectedIndex];
            const preview = document.getElementById('rental-preview');
            if (!sel.value) { preview.style.display = 'none'; return; }

            document.getElementById('rp-icon').textContent      = opt.dataset.icon;
            document.getElementById('rp-equipment').textContent = opt.dataset.equipment;
            document.getElementById('rp-category').textContent  = opt.dataset.category;
            document.getElementById('rp-user').textContent      = '👤 ' + opt.dataset.user;
            document.getElementById('rp-total').textContent     = 'Rp ' + parseFloat(opt.dataset.total).toLocaleString('id-ID');
            document.getElementById('rp-duration').textContent  = opt.dataset.duration + ' hari';
            document.getElementById('rp-returned').textContent  = 'Kembali: ' + opt.dataset.returned;
            preview.style.display = 'block';
        }
    </script>

    <style>
        .star-btn:hover { transform: scale(1.15); }
        @media (max-width: 900px) {
            form > div[style*="grid-template-columns:1fr 300px"] { grid-template-columns:1fr!important; }
        }
    </style>
</x-app-layout>
<x-app-layout>
    <x-slot name="heading">Edit Ulasan</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.reviews.index') }}">Ulasan</a>
        <span>/</span>
        <a href="{{ route('admin.reviews.show', $review) }}">#{{ $review->id }}</a>
        <span>/</span>
        <span>Edit</span>
    </x-slot>

    <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
        @csrf @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">

            {{-- LEFT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Konteks (read-only) --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Konteks Ulasan</div>
                        <a href="{{ route('admin.rentals.show', $review->rental) }}" class="btn-outline">Lihat Transaksi</a>
                    </div>
                    <div style="padding:1.5rem;">
                        <div style="display:flex;gap:1rem;padding:1.25rem;background:var(--surface);border-radius:4px;border:1px solid var(--border);margin-bottom:1rem;">
                            <div style="width:3.5rem;height:3.5rem;border-radius:2px;background:var(--card);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:1.8rem;flex-shrink:0;overflow:hidden;">
                                @if($review->equipment->image)
                                    <img src="{{ Storage::url($review->equipment->image) }}" alt=""
                                         style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ $review->equipment->category->icon ?? '🏄' }}
                                @endif
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-family:var(--font-display);font-size:1.05rem;text-transform:uppercase;color:var(--white);">
                                    {{ $review->equipment->name }}
                                </div>
                                <div style="font-size:.75rem;color:var(--mid);margin:.15rem 0;">
                                    {{ $review->equipment->category->name }}
                                </div>
                                <div style="font-size:.78rem;color:var(--muted);">
                                    👤 {{ $review->user->name }}
                                    · <span style="font-family:monospace;font-size:.75rem;color:var(--acid);">{{ $review->rental->rental_code }}</span>
                                </div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <div style="font-size:.65rem;letter-spacing:.1em;text-transform:uppercase;color:var(--mid);margin-bottom:.2rem;">Tanggal Ulasan</div>
                                <div style="font-size:.82rem;color:var(--white);">{{ $review->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="form-hint">Transaksi, penyewa, dan alat tidak dapat diubah.</div>
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

                            <div id="star-picker" style="display:flex;gap:.35rem;margin:.75rem 0;cursor:pointer;">
                                @for($s = 1; $s <= 5; $s++)
                                    <span class="star-btn" data-val="{{ $s }}"
                                          style="font-size:2.5rem;color:{{ old('rating', $review->rating) >= $s ? '#fbbf24' : 'var(--border)' }};transition:color .1s;user-select:none;line-height:1;">★</span>
                                @endfor
                            </div>

                            <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', $review->rating) }}">
                            <div id="rating-label" style="font-size:.78rem;color:var(--mid);">
                                {{ ['','Tidak Puas','Kurang Puas','Cukup','Puas','Sangat Puas'][old('rating', $review->rating)] ?? '' }}
                            </div>
                            @error('rating') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Komentar (Opsional)</label>
                            <textarea name="comment" id="comment-input" class="form-control" rows="5"
                                      placeholder="Ceritakan pengalaman menyewa alat ini…"
                                      maxlength="1000"
                                      oninput="updatePreview();document.getElementById('char-count').textContent=this.value.length">{{ old('comment', $review->comment) }}</textarea>
                            <div style="display:flex;justify-content:flex-end;margin-top:.3rem;">
                                <span id="char-count" style="font-size:.7rem;color:var(--mid);">{{ strlen(old('comment', $review->comment ?? '')) }}</span>
                                <span style="font-size:.7rem;color:var(--mid);">/1000</span>
                            </div>
                            @error('comment') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;position:sticky;top:70px;">

                {{-- Preview --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Pratinjau</div>
                    </div>
                    <div style="padding:1.25rem;text-align:center;">
                        <div id="preview-stars" style="font-size:2rem;letter-spacing:.1em;margin-bottom:.5rem;">
                            @for($s=1;$s<=5;$s++)<span style="color:{{ $s<=old('rating',$review->rating) ? '#fbbf24':'var(--border)' }}">★</span>@endfor
                        </div>
                        <div id="preview-label" style="font-family:var(--font-display);font-size:1.5rem;color:{{ ['','#f87171','#f87171','#fbbf24','var(--acid)','var(--acid)'][old('rating',$review->rating)] ?? 'var(--mid)' }};margin-bottom:1rem;">
                            {{ ['','Tidak Puas','Kurang Puas','Cukup','Puas','Sangat Puas'][old('rating', $review->rating)] ?? '—' }}
                        </div>
                        @if(old('comment', $review->comment))
                            <div id="preview-comment" style="font-size:.8rem;color:var(--mid);font-style:italic;padding:.75rem;background:var(--surface);border-radius:2px;border-left:2px solid var(--acid);text-align:left;line-height:1.6;">
                                "{{ old('comment', $review->comment) }}"
                            </div>
                        @else
                            <div id="preview-comment" style="display:none;font-size:.8rem;color:var(--mid);font-style:italic;padding:.75rem;background:var(--surface);border-radius:2px;border-left:2px solid var(--acid);text-align:left;line-height:1.6;"></div>
                        @endif
                    </div>
                    <div style="padding:.75rem 1.25rem;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:.5rem;">
                        <button type="submit" class="btn-add" style="justify-content:center;padding:.75rem;">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.reviews.show', $review) }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                            Batal
                        </a>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="table-card">
                    <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.6rem;">
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">ID Ulasan</span>
                            <span style="color:var(--muted);">#{{ $review->id }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">Rating Awal</span>
                            <span style="color:#fbbf24;">{{ str_repeat('★',$review->rating) }}{{ str_repeat('☆',5-$review->rating) }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">Dibuat</span>
                            <span style="color:var(--muted);">{{ $review->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">Diperbarui</span>
                            <span style="color:var(--muted);">{{ $review->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Danger zone --}}
                <div class="table-card" style="border-color:rgba(248,113,113,.2);">
                    <div class="table-card-header" style="border-color:rgba(248,113,113,.2);">
                        <div class="table-card-title" style="color:#f87171;">Zona Berbahaya</div>
                    </div>
                    <div style="padding:1.25rem;">
                        <div style="font-size:.78rem;color:var(--mid);margin-bottom:.75rem;">
                            Hapus ulasan ini secara permanen.
                        </div>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                              onsubmit="return confirm('Hapus ulasan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-action btn-delete"
                                    style="width:100%;justify-content:center;padding:.6rem;font-size:.78rem;border-width:1px;">
                                Hapus Ulasan
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <script>
        const ratingLabels = ['','Tidak Puas','Kurang Puas','Cukup','Puas','Sangat Puas'];
        const ratingColors = ['','#f87171','#f87171','#fbbf24','var(--acid)','var(--acid)'];
        let currentRating  = {{ old('rating', $review->rating) }};

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

        function updatePreview() {
            const starsEl   = document.getElementById('preview-stars');
            const labelEl   = document.getElementById('preview-label');
            const commentEl = document.getElementById('preview-comment');
            const commentTx = document.getElementById('comment-input').value.trim();

            if (currentRating) {
                let html = '';
                for (let i=1;i<=5;i++) html += `<span style="color:${i<=currentRating?'#fbbf24':'var(--border)'}">★</span>`;
                starsEl.innerHTML = html;
                labelEl.textContent  = ratingLabels[currentRating];
                labelEl.style.color  = ratingColors[currentRating];
            }

            if (commentTx) {
                commentEl.textContent   = '"' + commentTx + '"';
                commentEl.style.display = 'block';
            } else {
                commentEl.style.display = 'none';
            }
        }
    </script>

    <style>
        .star-btn:hover { transform: scale(1.15); }
        @media (max-width: 900px) {
            form > div[style*="grid-template-columns:1fr 300px"] { grid-template-columns:1fr!important; }
        }
    </style>
</x-app-layout>
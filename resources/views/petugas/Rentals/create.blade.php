@extends('layouts.petugas')

@section('content')

    <form method="POST" action="{{ route('petugas.rentals.store') }}" id="rental-form">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start;">

            {{-- LEFT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Pilih Penyewa --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Data Penyewa</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-group">
                            <label class="form-label">Pilih User <span style="color:#f87171;">*</span></label>
                            <select name="user_id" id="user-select" class="form-control" onchange="updateUserInfo(this)">
                                <option value="">— Pilih Penyewa —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        data-phone="{{ $user->phone }}"
                                        data-address="{{ $user->address }}"
                                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} — {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div id="user-info" style="display:none;margin-top:1rem;padding:1rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                            <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.5rem;">Info Penyewa</div>
                            <div id="user-phone" style="font-size:.82rem;color:var(--muted);"></div>
                            <div id="user-address" style="font-size:.82rem;color:var(--muted);margin-top:.2rem;"></div>
                        </div>
                    </div>
                </div>

                {{-- Pilih Alat --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Alat yang Disewa</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group span-2">
                                <label class="form-label">Pilih Alat <span style="color:#f87171;">*</span></label>
                                <select name="equipment_id" id="equipment-select" class="form-control" onchange="updateEquipmentInfo(this)">
                                    <option value="">— Pilih Alat —</option>
                                    @foreach($equipment as $item)
                                        <option value="{{ $item->id }}"
                                            data-price="{{ $item->price_per_day }}"
                                            data-stock="{{ $item->stock }}"
                                            data-name="{{ $item->name }}"
                                            data-category="{{ $item->category->name }}"
                                            {{ old('equipment_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->category->icon }} {{ $item->name }}
                                            @if($item->brand) ({{ $item->brand }}) @endif
                                            — Rp {{ number_format($item->price_per_day, 0, ',', '.') }}/hari
                                        </option>
                                    @endforeach
                                </select>
                                @error('equipment_id') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jumlah Unit <span style="color:#f87171;">*</span></label>
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}"
                                       class="form-control" min="1" oninput="recalculate()">
                                <div id="stock-hint" class="form-hint">—</div>
                                @error('quantity') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>

                        <div id="equipment-detail" style="display:none;margin-top:1rem;padding:1rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);">
                            <div style="font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--mid);margin-bottom:.5rem;">Info Alat</div>
                            <div id="eq-name" style="font-size:.9rem;font-weight:500;color:var(--white);"></div>
                            <div id="eq-price" style="font-size:.8rem;color:var(--acid);margin-top:.2rem;"></div>
                        </div>
                    </div>
                </div>

                {{-- Periode & Catatan --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Periode Sewa</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group">
                                <label class="form-label">Tanggal Mulai <span style="color:#f87171;">*</span></label>
                                <input type="date" name="start_date" id="start-date"
                                       value="{{ old('start_date', date('Y-m-d')) }}"
                                       class="form-control" min="{{ date('Y-m-d') }}" onchange="recalculate()">
                                @error('start_date') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tanggal Selesai <span style="color:#f87171;">*</span></label>
                                <input type="date" name="end_date" id="end-date"
                                       value="{{ old('end_date') }}"
                                       class="form-control" onchange="recalculate()">
                                @error('end_date') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group span-2">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="notes" class="form-control" rows="3"
                                          placeholder="Catatan khusus atau permintaan tambahan…">{{ old('notes') }}</textarea>
                                @error('notes') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;position:sticky;top:70px;">

                {{-- Kalkulasi --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Ringkasan</div>
                    </div>
                    <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.75rem;">
                        <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:var(--mid);">Harga/hari</span>
                            <span id="sum-price" style="color:var(--white);">—</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:var(--mid);">Jumlah unit</span>
                            <span id="sum-qty" style="color:var(--white);">—</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:var(--mid);">Durasi</span>
                            <span id="sum-duration" style="color:var(--white);">—</span>
                        </div>
                        <div style="height:1px;background:var(--border);"></div>
                        <div style="display:flex;justify-content:space-between;align-items:flex-end;">
                            <span style="font-size:.7rem;letter-spacing:.12em;text-transform:uppercase;color:var(--mid);">Total</span>
                            <span id="sum-total" style="font-family:var(--font-display);font-size:1.8rem;color:var(--acid);">Rp —</span>
                        </div>
                    </div>
                    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:.5rem;">
                        <button type="submit" class="btn-add" style="justify-content:center;padding:.75rem;">
                            Buat Transaksi
                        </button>
                        <a href="{{ route('petugas.rentals.index') }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                            Batal
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <script>
        let pricePerDay = 0, stock = 0;

        function updateUserInfo(sel) {
            const opt = sel.options[sel.selectedIndex];
            const info = document.getElementById('user-info');
            const phone = opt.dataset.phone, address = opt.dataset.address;
            if (sel.value) {
                document.getElementById('user-phone').textContent    = phone ? '📞 ' + phone : '';
                document.getElementById('user-address').textContent  = address ? '📍 ' + address : '';
                info.style.display = (phone || address) ? 'block' : 'none';
            } else {
                info.style.display = 'none';
            }
        }

        function updateEquipmentInfo(sel) {
            const opt = sel.options[sel.selectedIndex];
            pricePerDay = parseFloat(opt.dataset.price) || 0;
            stock = parseInt(opt.dataset.stock) || 0;
            const detail = document.getElementById('equipment-detail');
            if (sel.value) {
                document.getElementById('eq-name').textContent  = opt.dataset.name + ' · ' + opt.dataset.category;
                document.getElementById('eq-price').textContent = 'Rp ' + pricePerDay.toLocaleString('id-ID') + ' / hari  |  Stok: ' + stock + ' unit';
                document.getElementById('stock-hint').textContent = 'Stok tersedia: ' + stock + ' unit';
                detail.style.display = 'block';
            } else {
                detail.style.display = 'none';
                document.getElementById('stock-hint').textContent = '—';
            }
            recalculate();
        }

        function recalculate() {
            const startVal = document.getElementById('start-date').value;
            const endVal   = document.getElementById('end-date').value;
            const qty      = parseInt(document.getElementById('quantity').value) || 1;

            let duration = 0;
            if (startVal && endVal) {
                const diff = (new Date(endVal) - new Date(startVal)) / (1000 * 60 * 60 * 24);
                duration = diff > 0 ? diff : 0;
            }

            const total = pricePerDay * duration * qty;

            document.getElementById('sum-price').textContent    = pricePerDay ? 'Rp ' + pricePerDay.toLocaleString('id-ID') : '—';
            document.getElementById('sum-qty').textContent      = qty + ' unit';
            document.getElementById('sum-duration').textContent = duration ? duration + ' hari' : '—';
            document.getElementById('sum-total').textContent    = total ? 'Rp ' + total.toLocaleString('id-ID') : 'Rp —';
        }

        // Init
        recalculate();
    </script>

    <style>
        @media (max-width: 900px) {
            form > div[style*="grid-template-columns:1fr 320px"] { grid-template-columns:1fr!important; }
        }
    </style>
@endsection
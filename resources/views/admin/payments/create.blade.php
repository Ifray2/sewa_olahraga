<x-app-layout>
    <x-slot name="heading">Tambah Pembayaran</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.payments.index') }}">Pembayaran</a>
        <span>/</span>
        <span>Tambah</span>
    </x-slot>

    <form method="POST" action="{{ route('admin.payments.store') }}" enctype="multipart/form-data">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start;">

            {{-- LEFT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Pilih Transaksi --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Pilih Transaksi</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-group">
                            <label class="form-label">Transaksi Sewa <span style="color:#f87171;">*</span></label>
                            <select name="rental_id" id="rental-select" class="form-control" onchange="fillRentalInfo(this)">
                                <option value="">— Pilih Transaksi —</option>
                                @foreach($rentals as $rental)
                                    <option value="{{ $rental->id }}"
                                        data-code="{{ $rental->rental_code }}"
                                        data-user="{{ $rental->user->name }}"
                                        data-equipment="{{ $rental->equipment->name }}"
                                        data-total="{{ $rental->total_price }}"
                                        data-duration="{{ $rental->duration_days }}"
                                        data-status="{{ $rental->status_label }}"
                                        {{ (old('rental_id') == $rental->id || ($selectedRental && $selectedRental->id == $rental->id)) ? 'selected' : '' }}>
                                        {{ $rental->rental_code }} — {{ $rental->user->name }} · {{ $rental->equipment->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rental_id') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        {{-- Rental preview --}}
                        <div id="rental-info" style="{{ $selectedRental ? '' : 'display:none;' }}margin-top:1rem;">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--border);border:1px solid var(--border);border-radius:4px;overflow:hidden;">
                                <div style="background:var(--surface);padding:.9rem 1.1rem;">
                                    <div style="font-size:.6rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.25rem;">Kode Transaksi</div>
                                    <div id="ri-code" style="font-family:monospace;font-size:.85rem;color:var(--acid);">
                                        {{ $selectedRental?->rental_code }}
                                    </div>
                                </div>
                                <div style="background:var(--surface);padding:.9rem 1.1rem;">
                                    <div style="font-size:.6rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.25rem;">Penyewa</div>
                                    <div id="ri-user" style="font-size:.85rem;color:var(--white);">
                                        {{ $selectedRental?->user->name }}
                                    </div>
                                </div>
                                <div style="background:var(--surface);padding:.9rem 1.1rem;">
                                    <div style="font-size:.6rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.25rem;">Alat</div>
                                    <div id="ri-equipment" style="font-size:.85rem;color:var(--white);">
                                        {{ $selectedRental?->equipment->name }}
                                    </div>
                                </div>
                                <div style="background:var(--surface);padding:.9rem 1.1rem;">
                                    <div style="font-size:.6rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.25rem;">Total Sewa</div>
                                    <div id="ri-total" style="font-family:var(--font-display);font-size:1.1rem;color:var(--acid);">
                                        @if($selectedRental)
                                            Rp {{ number_format($selectedRental->total_price, 0, ',', '.') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detail Pembayaran --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Detail Pembayaran</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group">
                                <label class="form-label">Jumlah (Rp) <span style="color:#f87171;">*</span></label>
                                <div style="position:relative;">
                                    <span style="position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:var(--mid);font-size:.82rem;pointer-events:none;">Rp</span>
                                    <input type="number" name="amount" id="amount-input"
                                           value="{{ old('amount', $selectedRental?->total_price) }}"
                                           class="form-control" min="0" step="1000" style="padding-left:2.5rem;">
                                </div>
                                <div id="amount-hint" class="form-hint">
                                    @if($selectedRental)
                                        Total sewa: Rp {{ number_format($selectedRental->total_price, 0, ',', '.') }}
                                    @else
                                        —
                                    @endif
                                </div>
                                @error('amount') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Metode Pembayaran <span style="color:#f87171;">*</span></label>
                                <select name="method" class="form-control">
                                    <option value="transfer" {{ old('method','transfer')==='transfer' ? 'selected':'' }}>🏦 Transfer Bank</option>
                                    <option value="cash"     {{ old('method')==='cash'     ? 'selected':'' }}>💵 Tunai</option>
                                    <option value="ewallet"  {{ old('method')==='ewallet'  ? 'selected':'' }}>📱 E-Wallet</option>
                                </select>
                                @error('method') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Status Pembayaran <span style="color:#f87171;">*</span></label>
                                <select name="status" id="status-select" class="form-control" onchange="togglePaidAt(this)">
                                    <option value="unpaid"   {{ old('status','unpaid')==='unpaid'   ? 'selected':'' }}>⏳ Belum Bayar</option>
                                    <option value="paid"     {{ old('status')==='paid'               ? 'selected':'' }}>✅ Lunas</option>
                                    <option value="refunded" {{ old('status')==='refunded'           ? 'selected':'' }}>↩ Refunded</option>
                                </select>
                                @error('status') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group" id="paid-at-group" style="{{ old('status','unpaid')==='paid' ? '' : 'display:none;' }}">
                                <label class="form-label">Tanggal Bayar</label>
                                <input type="datetime-local" name="paid_at"
                                       value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}"
                                       class="form-control">
                                <div class="form-hint">Kosongkan untuk menggunakan waktu sekarang.</div>
                                @error('paid_at') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;position:sticky;top:70px;">

                {{-- Aksi --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Simpan</div>
                    </div>
                    <div style="padding:1rem 1.25rem;display:flex;flex-direction:column;gap:.5rem;">
                        <button type="submit" class="btn-add" style="justify-content:center;padding:.75rem;">
                            Tambah Pembayaran
                        </button>
                        <a href="{{ route('admin.payments.index') }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                            Batal
                        </a>
                    </div>
                </div>

                {{-- Upload Bukti --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Bukti Pembayaran</div>
                    </div>
                    <div style="padding:1.25rem;">
                        <div id="drop-zone"
                             style="border:2px dashed var(--border);border-radius:4px;padding:2rem 1rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
                             onclick="document.getElementById('proof-input').click()"
                             ondragover="event.preventDefault();this.style.borderColor='var(--acid)';this.style.background='rgba(200,245,66,.04)'"
                             ondragleave="this.style.borderColor='var(--border)';this.style.background='transparent'"
                             ondrop="handleDrop(event)">
                            <div id="drop-content">
                                <div style="font-size:2rem;margin-bottom:.5rem;">🖼</div>
                                <div style="font-size:.78rem;color:var(--mid);">Klik atau seret foto bukti</div>
                                <div style="font-size:.68rem;color:var(--mid);margin-top:.2rem;">JPG, PNG, WebP · Maks 3MB</div>
                            </div>
                            <img id="proof-preview" src="" alt=""
                                 style="display:none;width:100%;border-radius:2px;max-height:200px;object-fit:cover;">
                        </div>
                        <input type="file" name="proof_image" id="proof-input"
                               accept="image/*" style="display:none;" onchange="previewProof(this)">
                        <div id="proof-name" style="font-size:.72rem;color:var(--mid);margin-top:.4rem;text-align:center;"></div>
                        @error('proof_image') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

            </div>
        </div>
    </form>

    <script>
        const rentalsData = @json($rentals->keyBy('id'));

        function fillRentalInfo(sel) {
            const opt   = sel.options[sel.selectedIndex];
            const block = document.getElementById('rental-info');
            if (!sel.value) { block.style.display = 'none'; return; }

            const rental = rentalsData[sel.value];
            document.getElementById('ri-code').textContent      = opt.dataset.code;
            document.getElementById('ri-user').textContent      = opt.dataset.user;
            document.getElementById('ri-equipment').textContent = opt.dataset.equipment;
            document.getElementById('ri-total').textContent     = 'Rp ' + parseFloat(opt.dataset.total).toLocaleString('id-ID');

            // Auto-fill amount
            document.getElementById('amount-input').value = opt.dataset.total;
            document.getElementById('amount-hint').textContent  = 'Total sewa: Rp ' + parseFloat(opt.dataset.total).toLocaleString('id-ID');

            block.style.display = 'block';
        }

        function togglePaidAt(sel) {
            const group = document.getElementById('paid-at-group');
            group.style.display = sel.value === 'paid' ? 'flex' : 'none';
            group.style.flexDirection = 'column';
        }

        function previewProof(input) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('proof-preview').src = e.target.result;
                document.getElementById('proof-preview').style.display = 'block';
                document.getElementById('drop-content').style.display  = 'none';
                document.getElementById('proof-name').textContent = file.name + ' (' + (file.size/1024).toFixed(1) + ' KB)';
            };
            reader.readAsDataURL(file);
        }

        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('drop-zone').style.borderColor = 'var(--border)';
            document.getElementById('drop-zone').style.background  = 'transparent';
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const dt = new DataTransfer(); dt.items.add(file);
                document.getElementById('proof-input').files = dt.files;
                previewProof(document.getElementById('proof-input'));
            }
        }

        // Init if selectedRental
        @if($selectedRental)
            document.getElementById('rental-info').style.display = 'block';
        @endif
    </script>

    <style>
        @media (max-width: 900px) {
            form > div[style*="grid-template-columns:1fr 320px"] { grid-template-columns:1fr!important; }
            div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns:1fr!important; }
        }
    </style>
</x-app-layout>
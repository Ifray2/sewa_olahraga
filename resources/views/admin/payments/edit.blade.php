<x-app-layout>
    <x-slot name="heading">Edit Pembayaran</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.payments.index') }}">Pembayaran</a>
        <span>/</span>
        <a href="{{ route('admin.payments.show', $payment) }}">#{{ $payment->id }}</a>
        <span>/</span>
        <span>Edit</span>
    </x-slot>

    <form method="POST" action="{{ route('admin.payments.update', $payment) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start;">

            {{-- LEFT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Transaksi terkait (read-only) --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Transaksi Terkait</div>
                        <a href="{{ route('admin.rentals.show', $payment->rental) }}" class="btn-outline">Lihat Transaksi</a>
                    </div>
                    <div style="padding:1.5rem;">
                        <div style="display:flex;gap:1rem;padding:1.25rem;background:var(--surface);border-radius:4px;border:1px solid var(--border);">
                            <div style="width:3rem;height:3rem;border-radius:2px;background:var(--card);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">
                                {{ $payment->rental->equipment->category->icon ?? '🏄' }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-family:monospace;font-size:.82rem;color:var(--acid);">{{ $payment->rental->rental_code }}</div>
                                <div style="font-size:.88rem;font-weight:500;color:var(--white);margin:.15rem 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $payment->rental->equipment->name }}
                                </div>
                                <div style="font-size:.72rem;color:var(--mid);">
                                    {{ $payment->rental->user->name }} · {{ $payment->rental->duration_days }} hari
                                </div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <div style="font-family:var(--font-display);font-size:1.1rem;color:var(--acid);">
                                    Rp {{ number_format($payment->rental->total_price, 0, ',', '.') }}
                                </div>
                                <div style="font-size:.65rem;color:var(--mid);text-transform:uppercase;letter-spacing:.08em;">Total Sewa</div>
                            </div>
                        </div>
                        <div class="form-hint" style="margin-top:.5rem;">Transaksi tidak dapat diubah dari sini.</div>
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
                                    <input type="number" name="amount"
                                           value="{{ old('amount', $payment->amount) }}"
                                           class="form-control" min="0" step="1000" style="padding-left:2.5rem;">
                                </div>
                                <div class="form-hint">
                                    Total sewa: Rp {{ number_format($payment->rental->total_price, 0, ',', '.') }}
                                </div>
                                @error('amount') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Metode Pembayaran <span style="color:#f87171;">*</span></label>
                                <select name="method" class="form-control">
                                    <option value="transfer" {{ old('method', $payment->method) === 'transfer' ? 'selected':'' }}>🏦 Transfer Bank</option>
                                    <option value="cash"     {{ old('method', $payment->method) === 'cash'     ? 'selected':'' }}>💵 Tunai</option>
                                    <option value="ewallet"  {{ old('method', $payment->method) === 'ewallet'  ? 'selected':'' }}>📱 E-Wallet</option>
                                </select>
                                @error('method') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Status Pembayaran <span style="color:#f87171;">*</span></label>
                                <select name="status" id="status-select" class="form-control" onchange="togglePaidAt(this)">
                                    <option value="unpaid"   {{ old('status', $payment->status) === 'unpaid'   ? 'selected':'' }}>⏳ Belum Bayar</option>
                                    <option value="paid"     {{ old('status', $payment->status) === 'paid'     ? 'selected':'' }}>✅ Lunas</option>
                                    <option value="refunded" {{ old('status', $payment->status) === 'refunded' ? 'selected':'' }}>↩ Refunded</option>
                                </select>
                                @error('status') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group" id="paid-at-group"
                                 style="{{ old('status', $payment->status) === 'paid' ? '' : 'display:none;' }}">
                                <label class="form-label">Tanggal &amp; Waktu Bayar</label>
                                <input type="datetime-local" name="paid_at"
                                       value="{{ old('paid_at', $payment->paid_at?->format('Y-m-d\TH:i')) }}"
                                       class="form-control">
                                <div class="form-hint">Kosongkan untuk menggunakan waktu sekarang saat status diubah ke lunas.</div>
                                @error('paid_at') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;position:sticky;top:70px;">

                {{-- Simpan --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Simpan</div>
                    </div>
                    <div style="padding:1rem 1.25rem;display:flex;flex-direction:column;gap:.5rem;">
                        <button type="submit" class="btn-add" style="justify-content:center;padding:.75rem;">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.payments.show', $payment) }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                            Batal
                        </a>
                    </div>
                </div>

                {{-- Bukti Pembayaran --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Bukti Pembayaran</div>
                    </div>
                    <div style="padding:1.25rem;">

                        {{-- Existing proof --}}
                        @if($payment->proof_image)
                            <div id="existing-proof" style="margin-bottom:1rem;">
                                <div style="font-size:.65rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.5rem;">Foto Saat Ini</div>
                                <a href="{{ Storage::url($payment->proof_image) }}" target="_blank"
                                   style="display:block;border-radius:2px;overflow:hidden;border:1px solid var(--border);margin-bottom:.5rem;">
                                    <img src="{{ Storage::url($payment->proof_image) }}" alt="Bukti"
                                         style="width:100%;max-height:200px;object-fit:cover;display:block;">
                                </a>
                                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;padding:.5rem;background:rgba(248,113,113,.06);border:1px solid rgba(248,113,113,.2);border-radius:2px;">
                                    <input type="checkbox" name="remove_proof" value="1" id="remove-proof"
                                           onchange="toggleRemove(this)" style="accent-color:#f87171;">
                                    <span style="font-size:.78rem;color:#f87171;">Hapus foto ini</span>
                                </label>
                            </div>
                            <div style="height:1px;background:var(--border);margin-bottom:1rem;"></div>
                            <div style="font-size:.72rem;color:var(--mid);margin-bottom:.6rem;text-align:center;">Atau ganti dengan foto baru:</div>
                        @endif

                        {{-- Upload zone --}}
                        <div id="drop-zone"
                             style="border:2px dashed var(--border);border-radius:4px;padding:1.75rem 1rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
                             onclick="document.getElementById('proof-input').click()"
                             ondragover="event.preventDefault();this.style.borderColor='var(--acid)';this.style.background='rgba(200,245,66,.04)'"
                             ondragleave="this.style.borderColor='var(--border)';this.style.background='transparent'"
                             ondrop="handleDrop(event)">
                            <div id="drop-content">
                                <div style="font-size:1.75rem;margin-bottom:.4rem;">🖼</div>
                                <div style="font-size:.75rem;color:var(--mid);">Klik atau seret foto baru</div>
                                <div style="font-size:.65rem;color:var(--mid);margin-top:.2rem;">JPG, PNG, WebP · Maks 3MB</div>
                            </div>
                            <img id="proof-preview" src="" alt=""
                                 style="display:none;width:100%;border-radius:2px;max-height:180px;object-fit:cover;">
                        </div>
                        <input type="file" name="proof_image" id="proof-input"
                               accept="image/*" style="display:none;" onchange="previewProof(this)">
                        <div id="proof-name" style="font-size:.7rem;color:var(--mid);margin-top:.4rem;text-align:center;"></div>
                        @error('proof_image') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Meta --}}
                <div class="table-card">
                    <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.6rem;">
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">ID</span>
                            <span style="color:var(--muted);">#{{ $payment->id }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">Dibuat</span>
                            <span style="color:var(--muted);">{{ $payment->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">Diperbarui</span>
                            <span style="color:var(--muted);">{{ $payment->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if($payment->paid_at)
                            <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                                <span style="color:var(--mid);">Lunas</span>
                                <span style="color:var(--acid);">{{ $payment->paid_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </form>

    <script>
        function togglePaidAt(sel) {
            const group = document.getElementById('paid-at-group');
            group.style.display = sel.value === 'paid' ? '' : 'none';
        }

        function toggleRemove(cb) {
            const zone = document.getElementById('drop-zone');
            zone.style.opacity = cb.checked ? '.4' : '1';
            zone.style.pointerEvents = cb.checked ? 'none' : 'auto';
        }

        function previewProof(input) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('proof-preview').src    = e.target.result;
                document.getElementById('proof-preview').style.display = 'block';
                document.getElementById('drop-content').style.display  = 'none';
                document.getElementById('proof-name').textContent =
                    file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                // uncheck remove if new file selected
                const cb = document.getElementById('remove-proof');
                if (cb) { cb.checked = false; toggleRemove(cb); }
            };
            reader.readAsDataURL(file);
        }

        function handleDrop(e) {
            e.preventDefault();
            const dz = document.getElementById('drop-zone');
            dz.style.borderColor = 'var(--border)';
            dz.style.background  = 'transparent';
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const dt = new DataTransfer(); dt.items.add(file);
                document.getElementById('proof-input').files = dt.files;
                previewProof(document.getElementById('proof-input'));
            }
        }
    </script>

    <style>
        @media (max-width: 900px) {
            form > div[style*="grid-template-columns:1fr 320px"] { grid-template-columns:1fr!important; }
        }
    </style>
</x-app-layout>
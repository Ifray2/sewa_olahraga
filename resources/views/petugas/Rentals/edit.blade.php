@extends('layouts.petugas')

@section('content')

    @php
        $badgeMap = [
            'pending'   => 'badge-pending',
            'confirmed' => 'badge-confirmed',
            'active'    => 'badge-active',
            'returned'  => 'badge-returned',
            'cancelled' => 'badge-cancelled',
        ];
        $allowedTransitions = [
            'pending'   => ['pending','confirmed','cancelled'],
            'confirmed' => ['confirmed','active','cancelled'],
            'active'    => ['active','returned','cancelled'],
            'returned'  => ['returned'],
            'cancelled' => ['cancelled'],
        ];
        $statusLabels = [
            'pending'   => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'active'    => 'Sedang Disewa',
            'returned'  => 'Dikembalikan',
            'cancelled' => 'Dibatalkan',
        ];
    @endphp

    <form method="POST" action="{{ route('petugas.rentals.update', $rental) }}" id="edit-form">
        @csrf @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start;">

            {{-- LEFT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                {{-- Alat & Penyewa (read-only) --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div>
                            <div style="font-family:monospace;font-size:.82rem;color:var(--acid);margin-bottom:.3rem;">{{ $rental->rental_code }}</div>
                            <div class="table-card-title">Informasi Utama</div>
                        </div>
                        <span class="badge {{ $badgeMap[$rental->status] }}">{{ $rental->status_label }}</span>
                    </div>
                    <div style="padding:1.5rem;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;padding:1.25rem;background:var(--surface);border-radius:4px;border:1px solid var(--border);">
                            <div>
                                <div style="font-size:.62rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.4rem;">Penyewa</div>
                                <div style="font-size:.9rem;font-weight:500;color:var(--white);">{{ $rental->user->name }}</div>
                                <div style="font-size:.75rem;color:var(--mid);">{{ $rental->user->email }}</div>
                            </div>
                            <div>
                                <div style="font-size:.62rem;letter-spacing:.13em;text-transform:uppercase;color:var(--mid);margin-bottom:.4rem;">Alat</div>
                                <div style="font-size:.9rem;font-weight:500;color:var(--white);">{{ $rental->equipment->name }}</div>
                                <div style="font-size:.75rem;color:var(--mid);">
                                    {{ $rental->equipment->category->name }}
                                    · Rp {{ number_format($rental->equipment->price_per_day, 0, ',', '.') }}/hari
                                </div>
                            </div>
                        </div>
                        <div class="form-hint" style="margin-top:.5rem;">Alat dan penyewa tidak dapat diubah. Buat transaksi baru jika perlu.</div>
                    </div>
                </div>

                {{-- Periode & Kuantitas --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Periode & Kuantitas</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group">
                                <label class="form-label">Tanggal Mulai <span style="color:#f87171;">*</span></label>
                                <input type="date" name="start_date" id="start-date"
                                       value="{{ old('start_date', $rental->start_date->format('Y-m-d')) }}"
                                       class="form-control" onchange="recalculate()">
                                @error('start_date') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tanggal Selesai <span style="color:#f87171;">*</span></label>
                                <input type="date" name="end_date" id="end-date"
                                       value="{{ old('end_date', $rental->end_date->format('Y-m-d')) }}"
                                       class="form-control" onchange="recalculate()">
                                @error('end_date') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jumlah Unit <span style="color:#f87171;">*</span></label>
                                <input type="number" name="quantity" id="quantity"
                                       value="{{ old('quantity', $rental->quantity) }}"
                                       class="form-control" min="1" oninput="recalculate()">
                                @error('quantity') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Status & Penanganan --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Status & Penanganan</div>
                    </div>
                    <div style="padding:1.75rem;">
                        <div class="form-grid" style="gap:1.25rem;">

                            <div class="form-group">
                                <label class="form-label">Status Transaksi <span style="color:#f87171;">*</span></label>
                                <select name="status" class="form-control">
                                    @foreach($allowedTransitions[$rental->status] as $s)
                                        <option value="{{ $s }}" {{ old('status', $rental->status) === $s ? 'selected' : '' }}>
                                            {{ $statusLabels[$s] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Diproses Oleh</label>
                                <select name="handled_by" class="form-control">
                                    <option value="">— Tidak Ada —</option>
                                    @foreach($staff as $member)
                                        <option value="{{ $member->id }}"
                                            {{ old('handled_by', $rental->handled_by) == $member->id ? 'selected' : '' }}>
                                            {{ $member->name }} ({{ ucfirst($member->role) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('handled_by') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group span-2">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $rental->notes) }}</textarea>
                                @error('notes') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div style="display:flex;flex-direction:column;gap:1.5rem;position:sticky;top:70px;">

                {{-- Summary --}}
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">Ringkasan</div>
                    </div>
                    <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.75rem;">
                        <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:var(--mid);">Harga/hari</span>
                            <span style="color:var(--white);">
                                Rp {{ number_format($rental->equipment->price_per_day, 0, ',', '.') }}
                            </span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:var(--mid);">Jumlah unit</span>
                            <span id="sum-qty" style="color:var(--white);">{{ $rental->quantity }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:var(--mid);">Durasi</span>
                            <span id="sum-duration" style="color:var(--white);">{{ $rental->duration_days }} hari</span>
                        </div>
                        <div style="height:1px;background:var(--border);"></div>
                        <div style="display:flex;justify-content:space-between;align-items:flex-end;">
                            <span style="font-size:.7rem;letter-spacing:.12em;text-transform:uppercase;color:var(--mid);">Total Baru</span>
                            <span id="sum-total" style="font-family:var(--font-display);font-size:1.8rem;color:var(--acid);">
                                Rp {{ number_format($rental->total_price, 0, ',', '.') }}
                            </span>
                        </div>
                        <div style="padding:.6rem .75rem;background:rgba(251,191,36,.06);border-radius:2px;border:1px solid rgba(251,191,36,.2);">
                            <div style="font-size:.68rem;color:#fbbf24;text-align:center;">
                                Total sebelumnya: Rp {{ number_format($rental->total_price, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:.5rem;">
                        <button type="submit" class="btn-add" style="justify-content:center;padding:.75rem;">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('petugas.rentals.show', $rental) }}" class="btn-outline" style="justify-content:center;padding:.65rem;">
                            Batal
                        </a>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="table-card">
                    <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.6rem;">
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">Dibuat</span>
                            <span style="color:var(--muted);">{{ $rental->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                            <span style="color:var(--mid);">Diperbarui</span>
                            <span style="color:var(--muted);">{{ $rental->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if($rental->confirmed_at)
                            <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                                <span style="color:var(--mid);">Dikonfirmasi</span>
                                <span style="color:var(--muted);">{{ $rental->confirmed_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                        @if($rental->returned_at)
                            <div style="display:flex;justify-content:space-between;font-size:.78rem;">
                                <span style="color:var(--mid);">Dikembalikan</span>
                                <span style="color:var(--muted);">{{ $rental->returned_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </form>

    <script>
        const pricePerDay = {{ $rental->equipment->price_per_day }};

        function recalculate() {
            const startVal = document.getElementById('start-date').value;
            const endVal   = document.getElementById('end-date').value;
            const qty      = parseInt(document.getElementById('quantity').value) || 1;

            let duration = 0;
            if (startVal && endVal) {
                const diff = (new Date(endVal) - new Date(startVal)) / (1000 * 60 * 60 * 24);
                duration = diff > 0 ? Math.round(diff) : 0;
            }

            const total = pricePerDay * duration * qty;
            document.getElementById('sum-qty').textContent      = qty + ' unit';
            document.getElementById('sum-duration').textContent = duration ? duration + ' hari' : '—';
            document.getElementById('sum-total').textContent    = 'Rp ' + total.toLocaleString('id-ID');
        }

        recalculate();
    </script>

    <style>
        @media (max-width: 900px) {
            form > div[style*="grid-template-columns:1fr 320px"] { grid-template-columns:1fr!important; }
            div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns:1fr!important; }
        }
    </style>
@endsection
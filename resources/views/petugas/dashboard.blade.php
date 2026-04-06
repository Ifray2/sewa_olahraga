@extends('layouts.petugas')

@section('content')
    <x-slot name="heading">Dashboard</x-slot>
    <x-slot name="breadcrumb">
        <span>Petugas</span>
        <span>/</span>
        <span>Dashboard</span>
    </x-slot>

    {{-- ── Greeting ── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:.75rem;">
        <div>
            <div style="font-family:var(--font-display);font-size:1.8rem;text-transform:uppercase;color:var(--white);letter-spacing:.04em;line-height:1;">
                Selamat Datang, {{ auth()->user()->name }} 👋
            </div>
            <div style="font-size:.82rem;color:var(--mid);margin-top:.3rem;">
                {{ now()->isoFormat('dddd, D MMMM Y') }} · Sportrent Petugas
            </div>
        </div>
        <div style="display:flex;gap:.5rem;">
            <a href="{{ route('admin.rentals.index', ['status'=>'pending']) }}"
               style="position:relative;padding:.6rem 1rem;background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.3);border-radius:4px;color:#fbbf24;text-decoration:none;font-size:.78rem;letter-spacing:.06em;text-transform:uppercase;display:flex;align-items:center;gap:.5rem;">
                ⚡ Pending
                @if($kpi['rentals_pending'] > 0)
                    <span style="background:#fbbf24;color:var(--black);border-radius:10px;padding:0 .4rem;font-size:.65rem;font-weight:700;font-family:monospace;">
                        {{ $kpi['rentals_pending'] }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.rentals.create') }}"
               style="padding:.6rem 1rem;background:var(--acid);border:1px solid var(--acid);border-radius:4px;color:var(--black);text-decoration:none;font-size:.78rem;letter-spacing:.06em;text-transform:uppercase;font-weight:700;">
                + Buat Transaksi
            </a>
        </div>
    </div>

    {{-- ── KPI Cards ── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;">

        {{-- Revenue bulan ini --}}
        <div class="stat-card" style="grid-column:span 2;background:linear-gradient(135deg,rgba(200,245,66,.09) 0%,rgba(200,245,66,.02) 100%);border-color:rgba(200,245,66,.2);">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;">
                <div>
                    <div class="stat-label">Pendapatan Bulan Ini</div>
                    <div style="font-family:var(--font-display);font-size:2.4rem;color:var(--acid);line-height:1.1;margin:.35rem 0;">
                        Rp {{ number_format($kpi['revenue_month'], 0, ',', '.') }}
                    </div>
                    <div style="font-size:.72rem;color:var(--mid);">
                        Total semua waktu: <span style="color:var(--muted);">Rp {{ number_format($kpi['revenue_total'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <div style="font-size:2.5rem;opacity:.2;line-height:1;">💰</div>
            </div>
        </div>

        {{-- Transaksi aktif --}}
        <div class="stat-card">
            <div class="stat-label">Transaksi Aktif</div>
            <div class="stat-value acid">{{ $kpi['rentals_active'] }}</div>
            @if($kpi['rentals_pending'] > 0)
                <div style="font-size:.7rem;color:#fbbf24;margin-top:.3rem;">
                    +{{ $kpi['rentals_pending'] }} menunggu konfirmasi
                </div>
            @endif
            <div class="stat-bg-icon">🏄</div>
        </div>

        {{-- Users --}}
        <div class="stat-card">
            <div class="stat-label">Total User</div>
            <div class="stat-value">{{ $kpi['users_total'] }}</div>
            <div style="font-size:.7rem;color:var(--acid);margin-top:.3rem;">+{{ $kpi['users_new_month'] }} bulan ini</div>
            <div class="stat-bg-icon">👤</div>
        </div>

        {{-- Equipment --}}
        <div class="stat-card">
            <div class="stat-label">Total Alat</div>
            <div class="stat-value">{{ $kpi['equipment_total'] }}</div>
            @if($kpi['low_stock'] > 0)
                <div style="font-size:.7rem;color:#f87171;margin-top:.3rem;">⚠ {{ $kpi['low_stock'] }} stok menipis</div>
            @endif
            <div class="stat-bg-icon">🎒</div>
        </div>

        {{-- Rating --}}
        <div class="stat-card">
            <div class="stat-label">Avg Rating</div>
            <div style="display:flex;align-items:baseline;gap:.4rem;">
                <div class="stat-value" style="color:#fbbf24;">{{ $kpi['avg_rating'] }}</div>
                <div style="font-size:1.2rem;color:#fbbf24;">★</div>
            </div>
            <div style="font-size:.7rem;color:var(--mid);margin-top:.3rem;">dari {{ $kpi['reviews_total'] }} ulasan</div>
            <div class="stat-bg-icon">⭐</div>
        </div>

    </div>

    {{-- ── Row 1: Revenue + Rental Status ── --}}
    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.25rem;margin-bottom:1.25rem;align-items:start;">

        {{-- Revenue 12 bulan --}}
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Pendapatan 12 Bulan Terakhir</div>
                    <div style="font-size:.72rem;color:var(--mid);margin-top:.15rem;">Revenue bulanan dari pembayaran lunas</div>
                </div>
                <div style="font-family:var(--font-display);font-size:1.3rem;color:var(--acid);" id="rev-hover-val"></div>
            </div>
            <div style="padding:1.25rem 1.5rem 1.5rem;">
                <canvas id="revenueChart" height="200"></canvas>
            </div>
        </div>

        {{-- Rental status donut --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">Status Transaksi</div>
            </div>
            <div style="padding:1.25rem;display:flex;flex-direction:column;align-items:center;gap:1rem;">
                <div style="position:relative;width:160px;height:160px;">
                    <canvas id="statusDonut" width="160" height="160"></canvas>
                    <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none;">
                        <div style="font-family:var(--font-display);font-size:2rem;color:var(--white);line-height:1;" id="donut-center-val">
                            {{ array_sum($rentalStatus) }}
                        </div>
                        <div style="font-size:.6rem;color:var(--mid);text-transform:uppercase;letter-spacing:.12em;" id="donut-center-label">Total</div>
                    </div>
                </div>
                {{-- Legend --}}
                <div style="width:100%;display:flex;flex-direction:column;gap:.45rem;">
                    @php
                        $statusMeta = [
                            'pending'   => ['label'=>'Menunggu',     'color'=>'#fbbf24'],
                            'confirmed' => ['label'=>'Dikonfirmasi', 'color'=>'#60a5fa'],
                            'active'    => ['label'=>'Aktif',        'color'=>'#c8f542'],
                            'returned'  => ['label'=>'Dikembalikan', 'color'=>'#6b7280'],
                            'cancelled' => ['label'=>'Dibatalkan',   'color'=>'#f87171'],
                        ];
                    @endphp
                    @foreach($statusMeta as $key => $meta)
                        @php $cnt = $rentalStatus[$key] ?? 0; $total = array_sum($rentalStatus) ?: 1; @endphp
                        <div style="display:flex;align-items:center;gap:.6rem;">
                            <div style="width:.55rem;height:.55rem;border-radius:50%;background:{{ $meta['color'] }};flex-shrink:0;"></div>
                            <div style="flex:1;font-size:.75rem;color:var(--muted);">{{ $meta['label'] }}</div>
                            <div style="font-family:var(--font-display);font-size:.95rem;color:var(--white);">{{ $cnt }}</div>
                            <div style="font-size:.68rem;color:var(--mid);width:2.5rem;text-align:right;">{{ round($cnt/$total*100) }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 2: Daily rentals + Top Equipment ── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;align-items:start;">

        {{-- Daily activity sparkline --}}
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Aktivitas Harian (30 Hari)</div>
                    <div style="font-size:.72rem;color:var(--mid);margin-top:.15rem;">Jumlah transaksi baru per hari</div>
                </div>
            </div>
            <div style="padding:1rem 1.5rem 1.5rem;">
                <canvas id="dailyChart" height="120"></canvas>
            </div>
        </div>

        {{-- Payment methods --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">Metode Pembayaran</div>
            </div>
            <div style="padding:1.25rem 1.5rem 1.5rem;">
                <canvas id="paymentChart" height="120"></canvas>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-top:1rem;">
                    @php $methodMeta = ['transfer'=>['icon'=>'🏦','label'=>'Transfer','color'=>'#c8f542'],'cash'=>['icon'=>'💵','label'=>'Tunai','color'=>'#60a5fa'],'ewallet'=>['icon'=>'📱','label'=>'E-Wallet','color'=>'#fbbf24']]; @endphp
                    @foreach($paymentMethods as $pm)
                        @php $meta = $methodMeta[$pm->method] ?? ['icon'=>'💳','label'=>$pm->method,'color'=>'#9ca3af']; @endphp
                        <div style="padding:.75rem;background:var(--surface);border-radius:2px;border:1px solid var(--border);text-align:center;">
                            <div style="font-size:1.2rem;margin-bottom:.3rem;">{{ $meta['icon'] }}</div>
                            <div style="font-size:.62rem;color:var(--mid);text-transform:uppercase;letter-spacing:.08em;">{{ $meta['label'] }}</div>
                            <div style="font-family:var(--font-display);font-size:1.1rem;color:{{ $meta['color'] }};margin-top:.15rem;">{{ $pm->total }}</div>
                            <div style="font-size:.65rem;color:var(--mid);">transaksi</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 3: Top Equipment + Rating + Activity ── --}}
    <div style="display:grid;grid-template-columns:1fr 260px 280px;gap:1.25rem;margin-bottom:1.25rem;align-items:start;">

        {{-- Top equipment horizontal bars --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">Alat Paling Banyak Disewa</div>
            </div>
            <div style="padding:1.25rem 1.5rem;">
                @php $maxRent = $topEquipment->max('rentals_count') ?: 1; @endphp
                @foreach($topEquipment as $i => $eq)
                    <div style="margin-bottom:{{ $loop->last ? '0' : '.95rem' }};">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.3rem;">
                            <div style="display:flex;align-items:center;gap:.5rem;min-width:0;">
                                <span style="font-family:var(--font-display);font-size:.75rem;color:var(--mid);width:1rem;flex-shrink:0;">#{{ $i+1 }}</span>
                                <span style="font-size:.8rem;color:var(--white);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;">{{ $eq->name }}</span>
                                <span style="font-size:.65rem;color:var(--mid);flex-shrink:0;">{{ $eq->category->name }}</span>
                            </div>
                            <span style="font-family:var(--font-display);font-size:1rem;color:var(--acid);flex-shrink:0;">{{ $eq->rentals_count }}</span>
                        </div>
                        <div style="height:5px;background:var(--border);border-radius:3px;overflow:hidden;">
                            <div style="height:100%;border-radius:3px;width:{{ round($eq->rentals_count / $maxRent * 100) }}%;
                                background:{{ $i === 0 ? 'var(--acid)' : ($i === 1 ? 'rgba(200,245,66,.7)' : 'rgba(200,245,66,.4)') }};
                                transition:width .6s {{ $i * 0.08 }}s;">
                            </div>
                        </div>
                    </div>
                @endforeach
                @if($topEquipment->isEmpty())
                    <div style="text-align:center;padding:1.5rem 0;color:var(--mid);font-size:.82rem;">Belum ada data rental.</div>
                @endif
            </div>
        </div>

        {{-- Rating distribution --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">Distribusi Rating</div>
            </div>
            <div style="padding:1.25rem;">
                <div style="text-align:center;margin-bottom:1rem;">
                    <div style="font-family:var(--font-display);font-size:3.5rem;color:#fbbf24;line-height:1;">{{ $kpi['avg_rating'] }}</div>
                    <div style="font-size:1.1rem;color:#fbbf24;letter-spacing:.1em;margin:.2rem 0;">
                        @for($s=1;$s<=5;$s++)<span style="color:{{ $s <= round($kpi['avg_rating']) ? '#fbbf24':'var(--border)' }}">★</span>@endfor
                    </div>
                    <div style="font-size:.7rem;color:var(--mid);">dari {{ $kpi['reviews_total'] }} ulasan</div>
                </div>
                @php $maxR = max(array_values($ratingDist) ?: [1]); $totalR = array_sum($ratingDist) ?: 1; @endphp
                @for($r=5;$r>=1;$r--)
                    @php $cnt = $ratingDist[$r] ?? 0; $pct = round($cnt/$totalR*100); @endphp
                    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.45rem;">
                        <span style="font-size:.68rem;color:#fbbf24;width:.6rem;flex-shrink:0;">{{ $r }}</span>
                        <span style="font-size:.6rem;color:#fbbf24;">★</span>
                        <div style="flex:1;height:6px;background:var(--border);border-radius:3px;overflow:hidden;">
                            <div style="height:100%;border-radius:3px;background:{{ $r>=4?'#fbbf24':($r===3?'rgba(251,191,36,.5)':'rgba(248,113,113,.6)') }};width:{{ $cnt ? round($cnt/$maxR*100) : 0 }}%;transition:width .6s;"></div>
                        </div>
                        <span style="font-size:.68rem;color:var(--mid);width:1.5rem;text-align:right;flex-shrink:0;">{{ $cnt }}</span>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Recent activity feed --}}
        <div class="table-card" style="max-height:400px;overflow:hidden;">
            <div class="table-card-header">
                <div class="table-card-title">Aktivitas Terbaru</div>
                <a href="{{ route('admin.rentals.index') }}" style="font-size:.7rem;color:var(--acid);text-decoration:none;">Lihat Semua</a>
            </div>
            <div style="overflow-y:auto;max-height:320px;">
                @forelse($recentRentals as $rental)
                    @php
                        $dotColor = match($rental->status) {
                            'pending'   => '#fbbf24',
                            'confirmed' => '#60a5fa',
                            'active'    => '#c8f542',
                            'returned'  => '#6b7280',
                            'cancelled' => '#f87171',
                            default     => '#6b7280'
                        };
                    @endphp
                    <a href="{{ route('admin.rentals.show', $rental) }}"
                       style="display:flex;align-items:center;gap:.75rem;padding:.85rem 1.25rem;border-bottom:1px solid var(--border);text-decoration:none;transition:background .15s;"
                       onmouseover="this.style.background='var(--surface)'"
                       onmouseout="this.style.background='transparent'">
                        <div style="width:.55rem;height:.55rem;border-radius:50%;background:{{ $dotColor }};flex-shrink:0;"></div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:.75rem;color:var(--white);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $rental->user->name }}
                            </div>
                            <div style="font-size:.68rem;color:var(--mid);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $rental->equipment->name }}
                            </div>
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            <div style="font-family:var(--font-display);font-size:.85rem;color:var(--acid);">
                                Rp {{ number_format($rental->total_price, 0, ',', '.') }}
                            </div>
                            <div style="font-size:.62rem;color:var(--mid);">{{ $rental->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                @empty
                    <div style="padding:2rem;text-align:center;color:var(--mid);font-size:.82rem;">Belum ada transaksi.</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── Chart.js ── --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        // ── Global defaults ─────────────────────────────────────────────────
        Chart.defaults.color            = '#6b7280';
        Chart.defaults.font.family      = 'DM Sans, sans-serif';
        Chart.defaults.font.size        = 11;
        Chart.defaults.plugins.legend.display = false;
        Chart.defaults.plugins.tooltip.backgroundColor  = '#111';
        Chart.defaults.plugins.tooltip.borderColor      = '#1f2937';
        Chart.defaults.plugins.tooltip.borderWidth      = 1;
        Chart.defaults.plugins.tooltip.padding          = 10;
        Chart.defaults.plugins.tooltip.titleColor       = '#c8f542';
        Chart.defaults.plugins.tooltip.bodyColor        = '#d1d5db';
        Chart.defaults.plugins.tooltip.cornerRadius     = 2;

        const gridColor   = 'rgba(255,255,255,.05)';
        const acidGreen   = '#c8f542';
        const acidFaded   = 'rgba(200,245,66,.12)';

        // ── 1. Revenue Bar Chart ─────────────────────────────────────────────
        const revData = @json($revenueChart);

        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revGradient = revenueCtx.createLinearGradient(0, 0, 0, 200);
        revGradient.addColorStop(0, 'rgba(200,245,66,.35)');
        revGradient.addColorStop(1, 'rgba(200,245,66,.02)');

        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: revData.map(d => d.month + ' ' + d.year),
                datasets: [{
                    data:            revData.map(d => d.amount),
                    backgroundColor: revData.map((d, i) => i === revData.length - 1 ? acidGreen : revGradient),
                    borderColor:     revData.map((d, i) => i === revData.length - 1 ? acidGreen : 'rgba(200,245,66,.5)'),
                    borderWidth:     1,
                    borderRadius:    2,
                    borderSkipped:   false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { maxRotation: 0 } },
                    y: {
                        grid: { color: gridColor },
                        ticks: {
                            callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : (v/1000).toFixed(0)+'rb')
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => ' Rp ' + ctx.parsed.y.toLocaleString('id-ID'),
                        }
                    }
                },
                onHover: (e, items) => {
                    const el = document.getElementById('rev-hover-val');
                    if (items.length) {
                        const val = revData[items[0].index].amount;
                        el.textContent = 'Rp ' + val.toLocaleString('id-ID');
                    } else {
                        el.textContent = '';
                    }
                }
            }
        });

        // ── 2. Status Donut ──────────────────────────────────────────────────
        const statusRaw   = @json($rentalStatus);
        const statusOrder = ['pending','confirmed','active','returned','cancelled'];
        const statusColor = { pending:'#fbbf24', confirmed:'#60a5fa', active:'#c8f542', returned:'#4b5563', cancelled:'#f87171' };
        const statusLabel = { pending:'Menunggu', confirmed:'Dikonfirmasi', active:'Aktif', returned:'Dikembalikan', cancelled:'Dibatalkan' };

        const donutCtx = document.getElementById('statusDonut').getContext('2d');
        const donutChart = new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels:   statusOrder.map(s => statusLabel[s]),
                datasets: [{
                    data:            statusOrder.map(s => statusRaw[s] ?? 0),
                    backgroundColor: statusOrder.map(s => statusColor[s]),
                    borderColor:     '#0a0a0a',
                    borderWidth:     3,
                    hoverOffset:     6,
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' transaksi'
                        }
                    }
                },
                onHover: (e, items) => {
                    const cv  = document.getElementById('donut-center-val');
                    const cl  = document.getElementById('donut-center-label');
                    const tot = statusOrder.reduce((a,s) => a + (statusRaw[s]??0), 0);
                    if (items.length) {
                        const idx = items[0].index;
                        cv.textContent = statusOrder.map(s => statusRaw[s]??0)[idx];
                        cl.textContent = statusLabel[statusOrder[idx]];
                        cv.style.color = statusColor[statusOrder[idx]];
                    } else {
                        cv.textContent = tot;
                        cl.textContent = 'Total';
                        cv.style.color = 'var(--white, #f9fafb)';
                    }
                }
            }
        });

        // ── 3. Daily Activity Line ───────────────────────────────────────────
        const dailyRaw = @json($dailyRentals);

        const dailyCtx  = document.getElementById('dailyChart').getContext('2d');
        const lineGrad  = dailyCtx.createLinearGradient(0, 0, 0, 120);
        lineGrad.addColorStop(0, 'rgba(200,245,66,.2)');
        lineGrad.addColorStop(1, 'rgba(200,245,66,0)');

        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels:   dailyRaw.map(d => d.date),
                datasets: [{
                    data:            dailyRaw.map(d => d.count),
                    borderColor:     acidGreen,
                    borderWidth:     1.5,
                    backgroundColor: lineGrad,
                    fill:            true,
                    tension:         0.4,
                    pointRadius:     0,
                    pointHoverRadius:4,
                    pointHoverBackgroundColor: acidGreen,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: { grid: { color: gridColor }, ticks: { maxTicksLimit: 8 } },
                    y: { grid: { color: gridColor }, ticks: { stepSize: 1 }, min: 0 }
                },
                plugins: {
                    tooltip: {
                        callbacks: { label: ctx => ' ' + ctx.parsed.y + ' transaksi' }
                    }
                }
            }
        });

        // ── 4. Payment Method Horizontal Bar ────────────────────────────────
        const pmRaw   = @json($paymentMethods);
        const pmColors = { transfer: '#c8f542', cash: '#60a5fa', ewallet: '#fbbf24' };

        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'bar',
            data: {
                labels:   pmRaw.map(p => ({ transfer:'Transfer Bank', cash:'Tunai', ewallet:'E-Wallet' }[p.method] || p.method)),
                datasets: [{
                    label:           'Transaksi',
                    data:            pmRaw.map(p => p.total),
                    backgroundColor: pmRaw.map(p => pmColors[p.method] || '#9ca3af'),
                    borderColor:     pmRaw.map(p => pmColors[p.method] || '#9ca3af'),
                    borderWidth:     1,
                    borderRadius:    2,
                    barThickness:    28,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: { grid: { color: gridColor }, ticks: { stepSize: 1 } },
                    y: { grid: { display: false } }
                },
                plugins: {
                    tooltip: {
                        callbacks: { label: ctx => ' ' + ctx.parsed.x + ' transaksi  ·  Rp ' + (pmRaw[ctx.dataIndex]?.revenue || 0).toLocaleString('id-ID') }
                    }
                }
            }
        });
    </script>

    <style>
        .stat-card { position:relative; overflow:hidden; }

        @media (max-width: 1200px) {
            div[style*="grid-template-columns:1fr 300px"] { grid-template-columns:1fr!important; }
            div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns:1fr!important; }
            div[style*="grid-template-columns:1fr 260px 280px"] { grid-template-columns:1fr!important; }
        }
        @media (max-width: 900px) {
            div[style*="grid-template-columns:repeat(4,1fr)"] { grid-template-columns:repeat(2,1fr)!important; }
        }
        @media (max-width: 500px) {
            div[style*="grid-template-columns:repeat(4,1fr)"] { grid-template-columns:1fr!important; }
        }
    </style>
@endsection
<div class="container mt-4">
    {{-- 1. BAGIAN HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">👋 Halo, {{ auth()->user()->name ?? 'Kasir' }}!</h2>
            <p class="text-muted mb-0">Ini ringkasan toko Anda hari ini.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark border px-3 py-2">
                <i class="bi bi-calendar-event"></i> {{ now()->format('d M Y') }}
            </span>
        </div>
    </div>

    {{-- 2. BAGIAN KARTU STATISTIK --}}
    <div class="row g-3">
        {{-- Kartu Omzet --}}
        <div class="col-md-3">
            {{-- Hapus bg-primary, ganti dengan style warna manual --}}
            <div class="card text-white shadow-sm border-0 h-100" style="background-color: #0d6efd !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">Omzet Hari Ini</h6>
                            <h3 class="fw-bold mb-0">Rp {{ number_format($todayOmzet) }}</h3>
                        </div>
                        <i class="bi bi-cash-coin fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Transaksi --}}
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">Transaksi Hari Ini</h6>
                            <h3 class="fw-bold mb-0">{{ $todayCount }} <span class="fs-6 fw-normal">Nota</span></h3>
                        </div>
                        <i class="bi bi-receipt fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Total Produk --}}
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">Total Produk</h6>
                            <h3 class="fw-bold mb-0">{{ $totalProducts }} <span class="fs-6 fw-normal">Jenis</span></h3>
                        </div>
                        <i class="bi bi-box-seam fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Stok Menipis --}}
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">Stok Menipis</h6>
                            <h3 class="fw-bold mb-0">{{ $lowStock }} <span class="fs-6 fw-normal">Item</span></h3>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. BAGIAN GRAFIK PENJUALAN --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-secondary">
                        <i class="bi bi-graph-up-arrow text-primary me-2"></i>Statistik Penjualan 7 Hari Terakhir
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Canvas Grafik --}}
                    <canvas id="salesChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. BAGIAN TOMBOL AKSI --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-5">
                    <h4 class="mb-3 fw-bold">Apa yang ingin Anda lakukan?</h4>
                    <div class="d-flex justify-content-center gap-3">

                        {{-- TOMBOL KASIR (Semua bisa lihat) --}}
                        <a href="/transaksi" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="bi bi-cart-plus"></i> Transaksi Baru
                        </a>

                        {{-- TOMBOL RIWAYAT (Semua bisa lihat) --}}
                        <a href="/riwayat" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="bi bi-clock-history"></i> Lihat Riwayat
                        </a>

                        {{-- TOMBOL KELOLA PRODUK (HANYA ADMIN YANG BISA LIHAT) --}}
                        @if (auth()->user()->role === 'admin')
                            {{-- UBAH href="/produk" MENJADI href="{{ route('products') }}" --}}
                            <a href="{{ route('products') }}" class="btn btn-outline-info btn-lg px-4">
                                <i class="bi bi-box-seam"></i> Kelola Produk
                            </a>
                        @endif
                        {{-- Batas Akhir Admin --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 5. SCRIPT CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('livewire:initialized', () => {
        const ctx = document.getElementById('salesChart');

        // Cek jika elemen canvas ada sebelum menggambar
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: @json($chartValues),
                        borderWidth: 3,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#0d6efd',
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR'
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5]
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>

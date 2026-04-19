<div class="container-fluid py-4">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <div class="row">
        {{-- BAGIAN KIRI: DAFTAR PRODUK (DENGAN FITUR MULTI SATUAN) --}}
        <div class="col-md-7">
            <div class="card shadow-sm h-100">
                {{-- Search Bar --}}
                <div class="card-header bg-white">
                    <input type="text" class="form-control form-control-lg" id="searchInput"
                        placeholder="Scan Barcode / Cari Nama...[F2]" wire:model.live="search"
                        wire:keydown.enter.prevent="handleScan" autofocus autocomplete="off">
                </div>

                {{-- Grid Produk --}}
                <div class="card-body overflow-auto" style="height: 70vh;">
                    <div class="row g-3">
                        @foreach ($products as $product)
                            <div class="col-md-4 col-sm-6">
                                <div class="card h-100 border-primary-subtle hover-shadow overflow-hidden">

                                    {{-- 1. Gambar (Klik gambar = Masuk Satuan Kecil/Pcs) --}}
                                    <div class="d-flex justify-content-center align-items-center bg-light position-relative"
                                        style="height: 140px; cursor: pointer;"
                                        wire:click="addToCart({{ $product->id }}, 'unit')">

                                        @if ($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                alt="{{ $product->name }}" class="w-100 h-100"
                                                style="object-fit: cover;">
                                        @else
                                            <div class="text-secondary text-center">
                                                <i class="bi bi-image fs-1 d-block mb-1"></i>
                                                <small class="small">No Image</small>
                                            </div>
                                        @endif

                                        {{-- Badge Stok --}}
                                        <span class="position-absolute top-0 end-0 badge bg-dark m-2 opacity-75">
                                            {{ $product->stock }} {{ $product->unit ?? 'Pcs' }}
                                        </span>
                                    </div>

                                    {{-- 2. Info & Tombol Multi Satuan --}}
                                    <div class="card-body p-2 text-center d-flex flex-column">
                                        <h6 class="card-title fw-bold text-dark text-truncate mb-1"
                                            title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </h6>

                                        {{-- Opsi 1: Satuan Kecil (Default) --}}
                                        <button
                                            class="btn btn-outline-primary btn-sm w-100 mb-1 d-flex justify-content-between align-items-center"
                                            wire:click="addToCart({{ $product->id }}, 'unit')">
                                            <small>{{ $product->unit ?? 'Pcs' }}</small>
                                            <span class="fw-bold">Rp {{ number_format($product->price) }}</span>
                                        </button>

                                        {{-- Opsi 2: Satuan Besar (Hanya muncul jika disetting di DB) --}}
                                        @if ($product->unit_2 && $product->price_2)
                                            {{-- Cek Stok Cukup untuk 1 Dus --}}
                                            @php $canBuyDus = $product->stock >= $product->conversion; @endphp

                                            <button
                                                class="btn btn-sm w-100 d-flex justify-content-between align-items-center {{ $canBuyDus ? 'btn-outline-success' : 'btn-secondary disabled' }}"
                                                wire:click="{{ $canBuyDus ? "addToCart($product->id, 'unit_2')" : '' }}"
                                                {{ !$canBuyDus ? 'disabled' : '' }}>

                                                <div class="text-start" style="line-height: 1;">
                                                    <small class="d-block">{{ $product->unit_2 }}</small>
                                                    <small style="font-size: 0.6rem;">(Isi
                                                        {{ $product->conversion }})</small>
                                                </div>
                                                <span class="fw-bold">Rp {{ number_format($product->price_2) }}</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: KERANJANG KASIR --}}
        <div class="col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-cart"></i> Keranjang Kasir</h4>
                </div>
                <div class="card-body d-flex flex-column">

                    {{-- Notifikasi --}}
                    @if (session()->has('success'))
                        <div class="alert alert-success py-2">{{ session('success') }}</div>
                    @endif
                    @if (session()->has('error'))
                        <div class="alert alert-danger py-2">{{ session('error') }}</div>
                    @endif

                    {{-- Tabel Keranjang --}}
                    <div class="table-responsive flex-grow-1" style="max-height: 40vh; overflow-y: auto;">
                        <table class="table table-striped align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th class="text-end">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cart as $id => $item)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $item['name'] }}</div>
                                            <small class="text-muted">@ {{ number_format($item['price']) }}</small>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm" style="width: 100px;">
                                                {{-- PERHATIKAN: ID dibungkus tanda kutip '' karena formatnya string (misal '1_unit') --}}
                                                <button class="btn btn-outline-secondary"
                                                    wire:click="updateQty('{{ $id }}', 'min')">-</button>
                                                <input type="text" class="form-control text-center"
                                                    value="{{ $item['qty'] }}" readonly>
                                                <button class="btn btn-outline-secondary"
                                                    wire:click="updateQty('{{ $id }}', 'plus')">+</button>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            Rp {{ number_format($item['price'] * $item['qty']) }}
                                        </td>
                                        <td>
                                            <button wire:click="removeItem('{{ $id }}')"
                                                class="btn btn-sm btn-danger">&times;</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Keranjang Kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Bagian Pembayaran & Fitur Hutang --}}
                    <div class="mt-auto border-top pt-3 bg-light p-3 rounded">

                        {{-- 1. TOTAL HARGA --}}
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fs-4 fw-bold">Total</span>
                            <span class="fs-4 fw-bold text-primary">Rp {{ number_format($this->total) }}</span>
                        </div>

                        {{-- 2. PILIHAN PELANGGAN --}}
                        <div class="mb-2">
                            <label class="small fw-bold text-muted">Pelanggan</label>
                            <div class="input-group">
                                <select wire:model.live="customerId"
                                    class="form-select {{ $isDebt && empty($customerId) ? 'is-invalid' : '' }}">
                                    <option value="">-- Tamu / Pembeli Umum --</option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                                    @endforeach
                                </select>
                                {{-- Tombol (+) Pelanggan Baru --}}
                                <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#addCustomerModal" title="Tambah Pelanggan Baru">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>
                            </div>
                            @if ($isDebt && empty($customerId))
                                <div class="text-danger small mt-1">Wajib pilih pelanggan untuk hutang!</div>
                            @endif
                        </div>

                        {{-- 3. TOGGLE KASBON / HUTANG --}}
                        <div
                            class="form-check form-switch mb-3 p-2 border rounded bg-white d-flex align-items-center justify-content-between">
                            <div>
                                <label class="form-check-label fw-bold {{ $isDebt ? 'text-danger' : 'text-dark' }}"
                                    for="debtSwitch">
                                    <i class="bi bi-journal-x"></i> Mode Kasbon (Hutang)
                                </label>
                            </div>
                            <input class="form-check-input ms-2" style="width: 3em; height: 1.5em; cursor: pointer;"
                                type="checkbox" id="debtSwitch" wire:model.live="isDebt">
                        </div>

                        {{-- 4. INPUT PEMBAYARAN (Hanya muncul jika BUKAN Hutang) --}}
                        @if (!$isDebt)
                            <div class="mb-3">
                                <label>Uang Diterima (Rp)</label>
                                <input wire:model.live="payAmount" type="number"
                                    class="form-control form-control-lg" placeholder="0">
                            </div>

                            <div class="d-flex justify-content-between mb-3 text-success">
                                <span class="fw-bold">Kembalian</span>
                                <span class="fw-bold">Rp {{ number_format($this->change) }}</span>
                            </div>
                        @else
                            <div class="alert alert-warning py-2 mb-3 small">
                                <i class="bi bi-info-circle"></i> Transaksi akan dicatat ke buku hutang pelanggan.
                            </div>
                        @endif

                        {{-- 5. TOMBOL EKSEKUSI --}}
                        <button id="btnBayar" wire:click="submitTransaction"
                            class="btn w-100 btn-lg {{ $isDebt ? 'btn-danger' : 'btn-primary' }}"
                            {{ empty($cart) ? 'disabled' : '' }}>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>{{ $isDebt ? 'SIMPAN HUTANG' : 'PROSES BAYAR' }}</span>
                                <span
                                    class="badge bg-white {{ $isDebt ? 'text-danger' : 'text-primary' }} fw-bold">F9</span>
                            </div>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL SUKSES & STRUK --}}
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white d-print-none">
                    <h5 class="modal-title">Transaksi Berhasil!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        wire:click="resetTransaction"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="p-2" id="printArea">
                        {{-- Header Struk Dinamis --}}
                        <div class="text-center mb-3">
                            <h5 class="fw-bold mb-0 text-uppercase">{{ $shop_name ?? 'NAMA TOKO' }}</h5>
                            <small class="d-block">{{ $shop_address ?? 'Alamat belum diatur' }}</small>
                            <small>{{ $shop_phone ?? '' }}</small>
                        </div>

                        @if ($lastTransaction)
                            <div class="border-bottom border-dark border-1 border-dotted mb-2"></div>
                            <div class="d-flex justify-content-between mb-1">
                                <small>No: {{ $lastTransaction->invoice_number }}</small>
                                <small>{{ \Carbon\Carbon::parse($lastTransaction->created_at)->format('d/m/Y H:i') }}</small>
                            </div>
                            @if ($lastTransaction->customer)
                                <div class="text-start mb-1">
                                    <small>Plg: {{ $lastTransaction->customer->name }}</small>
                                </div>
                            @endif

                            <div class="border-bottom border-dark border-1 border-dotted mb-2"></div>

                            {{-- List Item Struk --}}
                            <div class="mb-2">
                                @foreach ($lastTransaction->items as $item)
                                    <div class="mb-1">
                                        <small class="fw-bold d-block">{{ $item->product->name }}</small>
                                        <div class="d-flex justify-content-between">
                                            <small>{{ $item->qty }} x {{ number_format($item->price) }}</small>
                                            <small>{{ number_format($item->price * $item->qty) }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-bottom border-dark border-1 border-dotted mb-2"></div>

                            {{-- Footer Struk --}}
                            <div class="d-flex justify-content-between fw-bold">
                                <small>Total</small>
                                <small>Rp {{ number_format($lastTransaction->total_amount) }}</small>
                            </div>

                            @if ($lastTransaction->payment_status == 'debt')
                                <div class="text-center fw-bold my-2 border border-dark p-1">
                                    -- KASBON / BELUM LUNAS --
                                </div>
                            @else
                                <div class="d-flex justify-content-between">
                                    <small>Bayar</small>
                                    <small>Rp {{ number_format($lastTransaction->pay_amount) }}</small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small>Kembali</small>
                                    <small>Rp {{ number_format($lastTransaction->change_amount) }}</small>
                                </div>
                            @endif

                            <div class="border-bottom border-dark border-1 border-dotted mt-2 mb-3"></div>
                            <div class="text-center mt-2">
                                <small>Terima Kasih!</small>
                            </div>
                        @endif
                    </div>

                    {{-- Tombol Cetak --}}
                    <div class="p-3 border-top mt-3 d-print-none">
                        <button onclick="window.print()" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-printer"></i> Cetak Struk
                        </button>
                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal"
                            wire:click="resetTransaction">
                            Tutup & Transaksi Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH PELANGGAN CEPAT --}}
    <div class="modal fade" id="addCustomerModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-person-plus"></i> Pelanggan Baru
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="small fw-bold">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" wire:model="newCustomerName" class="form-control"
                            placeholder="Nama Lengkap">
                        @error('newCustomerName')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">No. HP (Opsional)</label>
                        <input type="number" wire:model="newCustomerPhone" class="form-control"
                            placeholder="08xxx">
                    </div>
                    <button wire:click="addCustomer" class="btn btn-primary w-100 btn-sm">
                        <i class="bi bi-save"></i> Simpan & Pilih
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT & SHORTCUTS --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Modal Sukses
            @this.on('transaction-success', (event) => {
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();
                setTimeout(() => {
                    document.getElementById('searchInput').focus();
                }, 500);
            });

            // Tutup Modal Pelanggan setelah save
            @this.on('close-customer-modal', (event) => {
                var modalEl = document.getElementById('addCustomerModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
            });
        });

        document.addEventListener('keydown', function(event) {
            // F2: Fokus Search
            if (event.key === 'F2') {
                event.preventDefault();
                document.getElementById('searchInput').focus();
            }
            // F9: Bayar
            if (event.key === 'F9') {
                event.preventDefault();
                let btn = document.getElementById('btnBayar');
                if (btn && !btn.disabled) {
                    btn.click();
                }
            }
        });
    </script>

    {{-- STYLE CSS KHUSUS --}}
    <style>
        /* === TAMBAHAN WARNA UNGU KHUSUS HALAMAN POS === */
        :root {
            --bs-primary: #57a0d3 !important;
            --bs-primary-rgb: 142, 68, 173 !important;
        }

        .bg-primary {
            background-color: var(--bs-primary) !important;
        }

        .text-primary {
            color: var(--bs-primary) !important;
        }

        .btn-primary {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: white !important;
        }

        .btn-primary:hover {
            background-color: #2266cc !important;
            border-color: #2266cc !important;
        }

        /* Memaksa tombol outline (pilihan satuan produk) berubah ungu */
        .btn-outline-primary {
            color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }

        .btn-outline-primary:hover {
            background-color: var(--bs-primary) !important;
            color: white !important;
        }

        /* Memaksa garis pinggir kartu produk berubah ungu muda */
        .border-primary-subtle {
            border-color: rgba(142, 68, 173, 0.5) !important;
        }

        /* === AKHIR TAMBAHAN WARNA === */

        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            transform: translateY(-2px);
            transition: all 0.2s;
        }

        .border-dotted {
            border-style: dotted !important;
        }

        @media print {
            @page {
                margin: 0;
                size: 58mm auto;
            }

            body {
                margin: 0;
                padding: 0;
                background: white;
            }

            body * {
                visibility: hidden;
                height: 0;
            }

            #successModal,
            #successModal .modal-dialog,
            #successModal .modal-content,
            #successModal .modal-body,
            #printArea,
            #printArea * {
                visibility: visible;
                height: auto;
            }

            #successModal {
                position: absolute;
                left: 0;
                top: 0;
                width: 58mm;
                margin: 0;
                padding: 0;
                overflow: hidden;
            }

            .d-print-none {
                display: none !important;
            }
        }
    </style>
</div>

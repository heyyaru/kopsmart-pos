<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-primary mb-0">Riwayat Penjualan</h5>
                    <div class="w-25">
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control form-control-sm"
                            placeholder="Cari No Invoice...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Total Belanja</th>
                                    <th>Bayar</th>
                                    <th>Kembalian</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $trx)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $trx->invoice_number }}</td>
                                        <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="fw-bold">Rp {{ number_format($trx->total_amount) }}</td>
                                        <td>Rp {{ number_format($trx->pay_amount) }}</td>
                                        <td class="text-success fw-bold">Rp {{ number_format($trx->change_amount) }}
                                        </td>
                                        <td>
                                            <button wire:click="showDetail({{ $trx->id }})"
                                                class="btn btn-sm btn-info text-white">
                                                <i class="bi bi-printer"></i> Detail / Cetak
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-print-none">
                    <h5 class="modal-title">Detail Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    @if ($selectedTransaction)
                        <div class="p-3" id="printArea">

                            @php $setting = \App\Models\Setting::first(); @endphp

                            <div class="text-center mb-3">
                                <h5 class="fw-bold mb-0">{{ $setting->shop_name ?? 'NAMA TOKO' }}</h5>
                                <small>{{ $setting->address ?? 'Alamat Toko Belum Diatur' }}</small>
                            </div>

                            <div class="border-bottom border-dark border-1 border-dotted mb-2"></div>

                            <div class="d-flex justify-content-between mb-1">
                                <small>No: {{ $selectedTransaction->invoice_number }}</small>
                                <small>{{ $selectedTransaction->created_at->format('d/m/Y H:i') }}</small>
                            </div>

                            <div class="border-bottom border-dark border-1 border-dotted mb-2"></div>

                            <div class="mb-2">
                                @foreach ($selectedTransaction->items as $item)
                                    <div class="mb-1">
                                        <small
                                            class="fw-bold d-block">{{ $item->product->name ?? 'Produk Dihapus' }}</small>
                                        <div class="d-flex justify-content-between">
                                            <small>{{ $item->qty }} x {{ number_format($item->price) }}</small>
                                            <small>{{ number_format($item->qty * $item->price) }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-bottom border-dark border-1 border-dotted mb-2"></div>

                            <div class="d-flex justify-content-between fw-bold">
                                <small>Total</small>
                                <small>Rp {{ number_format($selectedTransaction->total_amount) }}</small>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small>Bayar</small>
                                <small>Rp {{ number_format($selectedTransaction->pay_amount) }}</small>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small>Kembali</small>
                                <small>Rp {{ number_format($selectedTransaction->change_amount) }}</small>
                            </div>

                            <div class="border-bottom border-dark border-1 border-dotted mt-2 mb-3"></div>

                            <div class="text-center mt-2">
                                <small>Telp: {{ $setting->phone ?? '-' }}</small><br>
                                <small>-- Copy Receipt --</small>
                            </div>
                        </div>

                        <div class="p-3 border-top d-print-none">
                            <button onclick="window.print()" class="btn btn-primary w-100">
                                <i class="bi bi-printer"></i> Cetak Struk
                            </button>
                        </div>
                    @else
                        <div class="p-4 text-center">Sedang memuat...</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('open-detail-modal', (event) => {
                var myModal = new bootstrap.Modal(document.getElementById('detailModal'));
                myModal.show();
            });
        });
    </script>

    <style>
        .border-dotted {
            border-style: dotted !important;
        }

        @media print {
            @page {
                margin: 0;
                size: 58mm auto;
            }

            /* Ukuran Kertas Thermal */
            body {
                background: white;
            }

            body * {
                visibility: hidden;
                height: 0;
            }

            /* Sembunyikan semua elemen website */

            /* Hanya tampilkan modal */
            #detailModal,
            #detailModal * {
                visibility: visible;
                height: auto;
            }

            #detailModal {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .modal-dialog {
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
            }

            .modal-content {
                border: none !important;
                box-shadow: none !important;
            }

            /* Sembunyikan tombol tutup/print saat dicetak */
            .d-print-none {
                display: none !important;
            }
        }
    </style>
</div>

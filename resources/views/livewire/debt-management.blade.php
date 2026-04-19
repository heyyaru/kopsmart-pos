<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-journal-x"></i> Buku Hutang / Kasbon</h5>
            <input wire:model.live="search" type="text" class="form-control form-control-sm w-25" placeholder="Cari Pelanggan...">
        </div>
        <div class="card-body">
            
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total Belanja</th>
                            <th>Sudah Dibayar</th>
                            <th>Sisa Hutang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debts as $debt)
                            <tr>
                                <td>{{ $debt->created_at->format('d M Y') }}</td>
                                <td class="fw-bold">{{ $debt->customer->name ?? 'Tanpa Nama' }}</td>
                                <td>Rp {{ number_format($debt->total_amount) }}</td>
                                <td class="text-success">Rp {{ number_format($debt->amount_paid) }}</td>
                                <td class="text-danger fw-bold">
                                    Rp {{ number_format($debt->total_amount - $debt->amount_paid) }}
                                </td>
                                <td>
                                    <button wire:click="openPayModal({{ $debt->id }})" class="btn btn-sm btn-primary">
                                        <i class="bi bi-cash-coin"></i> Bayar / Cicil
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-emoji-smile fs-1"></i><br>
                                    Tidak ada data hutang. Semua lunas!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $debts->links() }}
        </div>
    </div>

    {{-- MODAL BAYAR HUTANG --}}
    <div class="modal fade" id="payDebtModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bayar Hutang: {{ $customerName }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Hutang Awal:</span>
                        <span class="fw-bold">Rp {{ number_format($totalDebt) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-success">
                        <span>Sudah Dibayar:</span>
                        <span>- Rp {{ number_format($alreadyPaid) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3 fs-5">
                        <span>Sisa Tanggungan:</span>
                        <span class="text-danger fw-bold">Rp {{ number_format((int)$totalDebt - (int)$alreadyPaid) }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Masukkan Nominal Bayar</label>
                        <input wire:model="payAmount" type="number" class="form-control form-control-lg border-primary">
                        @error('payAmount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button wire:click="submitPayment" class="btn btn-primary">Simpan Pembayaran</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const myModal = new bootstrap.Modal(document.getElementById('payDebtModal'));
            @this.on('open-modal', () => myModal.show());
            @this.on('close-modal', () => myModal.hide());
        });
    </script>
</div>
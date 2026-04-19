<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">📑 Laporan Keuangan</h3>
    </div>

    {{-- Filter Tanggal & Tombol Export --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-end g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Dari Tanggal</label>
                    <input type="date" wire:model.live="startDate" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" wire:model.live="endDate" class="form-control">
                </div>
                <div class="col-md-3">
                    <button wire:click="downloadExcel" class="btn btn-success w-100">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Download Excel
                    </button>
                </div>
                
                {{-- Total Omzet Summary --}}
                <div class="col-md-3 text-end">
                    <small class="text-muted d-block">Total Omzet (Periode Ini)</small>
                    <h4 class="fw-bold text-primary">Rp {{ number_format($totalOmzet) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Preview Data --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Preview Data</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>No Invoice</th>
                            <th>Kasir</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                            <tr>
                                <td>{{ $t->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $t->invoice_number }}</td>
                                <td>{{ $t->user->name ?? '-' }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($t->total_amount) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Tidak ada transaksi pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
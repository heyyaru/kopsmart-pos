<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-box-seam"></i> Stok Opname (Penyesuaian)</h5>
                    <div style="width: 300px;">
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Cari Produk / Scan Barcode...">
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Stok Sistem</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $product->name }}</div>
                                        <small class="text-muted">{{ $product->barcode }}</small>
                                    </td>
                                    <td>{{ $product->category ?? '-' }}</td>
                                    <td class="text-center">
                                        {{-- Warna merah jika stok tipis --}}
                                        <span class="badge {{ $product->stock <= 5 ? 'bg-danger' : 'bg-success' }} fs-6">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button wire:click="openAdjustModal({{ $product->id }})" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil-square"></i> Sesuaikan
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PENYESUAIAN STOK --}}
    <div class="modal fade" id="adjustModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Sesuaikan Stok Fisik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="alert alert-info py-2">
                        <small>Produk: <strong>{{ $productName }}</strong></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stok Tercatat di Sistem</label>
                        <input type="text" class="form-control" value="{{ $systemStock }}" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Stok Fisik (Hasil Hitung Manual)</label>
                        <input wire:model="physicalStock" type="number" class="form-control form-control-lg border-primary">
                        @error('physicalStock') <span class="text-danger small">{{ $message }}</span> @enderror
                        
                        {{-- Kalkulator Selisih Otomatis --}}
                        <div class="mt-1 text-end">
                            <small class="text-muted">Selisih: 
                                @php $diff = (int)$physicalStock - (int)$systemStock; @endphp
                                <span class="{{ $diff < 0 ? 'text-danger' : ($diff > 0 ? 'text-success' : 'text-dark') }} fw-bold">
                                    {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                </span>
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alasan Penyesuaian <span class="text-danger">*</span></label>
                        <textarea wire:model="note" class="form-control" rows="2" placeholder="Contoh: Barang rusak, Kadaluarsa, Salah input..."></textarea>
                        @error('note') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button wire:click="saveAdjustment" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT BUKA/TUTUP MODAL --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            const myModal = new bootstrap.Modal(document.getElementById('adjustModal'));

            @this.on('open-modal', () => {
                myModal.show();
            });

            @this.on('close-modal', () => {
                myModal.hide();
            });
        });
    </script>
</div>
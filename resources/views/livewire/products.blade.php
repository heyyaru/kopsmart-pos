<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold text-primary mb-0">Daftar Produk</h4>
                    <button wire:click="create" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Produk
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th width="10%">Gambar</th>
                                    <th>Barcode</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        {{-- MENAMPILKAN GAMBAR --}}
                                        <td class="text-center">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    class="rounded"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center mx-auto"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- MENAMPILKAN BARCODE (DENGAN GAMBAR GARIS) --}}
                                        <td>
                                            @php
                                                $generator = new Picqer\Barcode\BarcodeGeneratorSVG();
                                            @endphp

                                            <div style="height: 30px; margin-bottom: 5px;">
                                                {!! $generator->getBarcode($product->barcode, $generator::TYPE_CODE_128) !!}
                                            </div>

                                            <div class="text-center small fw-bold">
                                                {{ $product->barcode }}
                                            </div>
                                        </td>

                                        <td>
                                            {{ $product->name }}
                                            {{-- Tampilkan info grosir kecil di bawah nama jika ada --}}
                                            @if($product->wholesale_unit)
                                                <br>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="bi bi-box-seam"></i>
                                                    Grosir: {{ number_format($product->wholesale_price) }}/{{ $product->wholesale_unit }}
                                                </small>
                                            @endif
                                        </td>

                                        {{-- MENAMPILKAN KATEGORI --}}
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                {{ $product->category ?? '-' }}
                                            </span>
                                        </td>

                                        <td>Rp {{ number_format($product->price) }}</td>

                                        {{-- STOK DENGAN SATUAN --}}
                                        <td>
                                            <span class="fw-bold">{{ $product->stock }}</span>
                                            <span class="text-muted small">{{ $product->unit }}</span>
                                        </td>

                                        <td>
                                            {{-- TOMBOL CETAK BARCODE --}}
                                            <button wire:click="openPrintModal({{ $product->id }})" class="btn btn-sm btn-info text-white" title="Cetak Barcode">
                                                <i class="bi bi-printer"></i>
                                            </button>

                                            <button wire:click="edit({{ $product->id }})"
                                                class="btn btn-sm btn-warning text-white" title="Edit Produk">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button wire:click="delete({{ $product->id }})"
                                                wire:confirm="Yakin ingin menghapus produk ini?"
                                                class="btn btn-sm btn-danger" title="Hapus Produk">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FORM CREATE / EDIT --}}
    <div class="modal fade" id="productModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditMode ? 'Edit Produk' : 'Tambah Produk Baru' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            {{-- KOLOM KIRI: INFO UTAMA --}}
                            <div class="col-md-6">
                                {{-- INPUT GAMBAR --}}
                                <div class="mb-3">
                                    <label class="fw-bold">Gambar Produk</label>
                                    <input type="file" class="form-control" wire:model="image">
                                    <div wire:loading wire:target="image" class="text-primary small mt-1">
                                        Sedang mengupload gambar...
                                    </div>
                                    @error('image') <span class="text-danger small">{{ $message }}</span> @enderror

                                    <div class="mt-2 text-center">
                                        @if ($image)
                                            <p class="small mb-1 text-muted">Preview Gambar Baru:</p>
                                            <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="max-height: 100px;">
                                        @elseif ($oldImage && $isEditMode)
                                            <p class="small mb-1 text-muted">Gambar Saat Ini:</p>
                                            <img src="{{ asset('storage/' . $oldImage) }}" class="img-thumbnail" style="max-height: 100px;">
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold">Nama Produk</label>
                                    <input type="text" class="form-control" wire:model="name" placeholder="Contoh: Indomie Goreng">
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold">Barcode</label>
                                    <input type="text" class="form-control" wire:model="barcode">
                                    @error('barcode') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- KOLOM KANAN: DETAIL HARGA & SATUAN --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">Kategori</label>
                                    <select wire:model="category" class="form-select">
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Makanan">Makanan</option>
                                        <option value="Minuman">Minuman</option>
                                        <option value="Snack">Snack</option>
                                        <option value="ATK">ATK</option>
                                        <option value="Sembako">Sembako</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                    @error('category') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold">Harga Ecer</label>
                                        <input type="number" class="form-control" wire:model="price" placeholder="0">
                                        @error('price') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold">Stok Awal</label>
                                        <input type="number" class="form-control" wire:model="stock" placeholder="0">
                                        @error('stock') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- KONFIGURASI SATUAN --}}
                                <hr class="my-2">

                                <div class="mb-3">
                                    <label class="fw-bold">Satuan Dasar</label>
                                    <select wire:model="unit" class="form-select">
                                        <option value="Pcs">Pcs</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Liter">Liter</option>
                                        <option value="Box">Box</option>
                                        <option value="Botol">Botol</option>
                                        <option value="Bungkus">Bungkus</option>
                                    </select>
                                    <small class="text-muted">Satuan terkecil stok (Default: Pcs)</small>
                                </div>

                                {{-- CHECKBOX GROSIR --}}
                                <div class="form-check form-switch mb-3 bg-light p-2 rounded border">
                                    <input class="form-check-input" type="checkbox" wire:model.live="has_wholesale" id="wholesaleCheck">
                                    <label class="form-check-label fw-bold ms-2" for="wholesaleCheck">
                                        Jual Grosir / Satuan Besar?
                                    </label>
                                </div>

                                {{-- FORM GROSIR --}}
                                @if($has_wholesale)
                                    <div class="card card-body bg-light border-primary p-2">
                                        <h6 class="text-primary fw-bold small mb-2">Konfigurasi Grosir (Dus/Pack)</h6>

                                        <div class="mb-2">
                                            <label class="small">Nama Satuan Besar</label>
                                            <input type="text" wire:model="wholesale_unit" class="form-control form-control-sm" placeholder="Cth: Dus">
                                            @error('wholesale_unit') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-6 mb-2">
                                                <label class="small">Isi per Dus</label>
                                                <input type="number" wire:model="wholesale_qty" class="form-control form-control-sm" placeholder="Jml Pcs">
                                                @error('wholesale_qty') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="col-6 mb-2">
                                                <label class="small">Harga Dus</label>
                                                <input type="number" wire:model="wholesale_price" class="form-control form-control-sm" placeholder="Rp">
                                                @error('wholesale_price') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Produk' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL CETAK BARCODE (PERBAIKAN) --}}
<div class="modal fade" id="printModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h6 class="modal-title"><i class="bi bi-printer"></i> Cetak Barcode</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-1">Produk:</p>
                <h6 class="fw-bold text-primary">{{ $printProductName }}</h6>

                <div class="mt-3">
                    <label class="form-label small fw-bold">Jumlah Stiker/Barcode:</label>
                    <input type="number" class="form-control text-center" min="1" wire:model="printQty" required>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-info text-white w-100"
                    onclick="window.open('/admin/products/print-barcode/' + @js($printProductId) + '?qty=' + @js($printQty), '_blank'); document.querySelector('#printModal .btn-close').click();">
                    <i class="bi bi-printer"></i> Buka Halaman Cetak
                </button>
            </div>
        </div>
    </div>
</div>

</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Inisialisasi Modal Create/Edit
        const productModalEl = document.getElementById('productModal');
        const myModal = new bootstrap.Modal(productModalEl);

        // Inisialisasi Modal Cetak Barcode
        const printModalEl = document.getElementById('printModal');
        const printModal = new bootstrap.Modal(printModalEl);

        // Event Listener Modal Create/Edit
        @this.on('open-modal', (event) => { myModal.show(); });
        @this.on('close-modal', (event) => { myModal.hide(); });

        // Event Listener Modal Cetak
        @this.on('open-print-modal', (event) => { printModal.show(); });
    });
</script>

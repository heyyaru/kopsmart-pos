<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class Products extends Component
{
    use WithPagination;
    use WithFileUploads;

    // === 1. VARIABEL UTAMA ===
    public $productId;
    public $isEditMode = false;

    // Data Produk Standar
    public $name, $category, $price, $stock, $barcode;
    
    // Data Gambar
    public $image;      
    public $oldImage;   

    // === 2. VARIABEL MULTI-SATUAN ===
    public $unit = 'Pcs'; // Default
    public $has_wholesale = false; // Checkbox toggle
    public $wholesale_unit;
    public $wholesale_qty;
    public $wholesale_price;

    // === 3. VARIABEL CETAK BARCODE (BARU) ===
    public $printProductId;
    public $printProductName;
    public $printQty = 1;

    public function render()
    {
        return view('livewire.products', [
            'products' => Product::latest()->paginate(10)
        ])->layout('layouts.app');
    }

    // === 4. RESET INPUT ===
    private function resetInput()
    {
        $this->productId = null;
        $this->isEditMode = false;

        $this->name = '';
        $this->category = '';
        $this->price = '';
        $this->stock = '';
        $this->barcode = '';
        
        $this->image = null; 
        $this->oldImage = null;

        // Reset Satuan
        $this->unit = 'Pcs';
        $this->has_wholesale = false;
        $this->wholesale_unit = null;
        $this->wholesale_qty = null;
        $this->wholesale_price = null;
    }

    public function create()
    {
        $this->resetInput();
        $this->dispatch('open-modal');
    }

    // === 5. SIMPAN PRODUK BARU ===
    public function store()
    {
        $this->validate([
            'name' => 'required',
            'category' => 'nullable',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'barcode' => 'required|unique:products,barcode',
            'image' => 'nullable|image|max:2048', 
            
            // Validasi Satuan
            'unit' => 'required',
            'wholesale_unit' => 'nullable|required_if:has_wholesale,true',
            'wholesale_qty' => 'nullable|numeric|required_if:has_wholesale,true',
            'wholesale_price' => 'nullable|numeric|required_if:has_wholesale,true',
        ]);

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
        }

        Product::create([
            'name' => $this->name,
            'category' => $this->category,
            'image' => $imagePath,
            'price' => $this->price,
            'stock' => $this->stock,
            'barcode' => $this->barcode,

            // Simpan Satuan
            'unit' => $this->unit,
            'wholesale_unit' => $this->has_wholesale ? $this->wholesale_unit : null,
            'wholesale_qty' => $this->has_wholesale ? $this->wholesale_qty : null,
            'wholesale_price' => $this->has_wholesale ? $this->wholesale_price : null,
        ]);

        $this->resetInput();
        $this->dispatch('close-modal');
        session()->flash('success', 'Produk berhasil ditambahkan!');
    }

    // === 6. EDIT PRODUK ===
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        
        $this->productId = $id;
        $this->name = $product->name;
        $this->category = $product->category;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->barcode = $product->barcode;
        $this->oldImage = $product->image; 

        // Load Data Satuan
        $this->unit = $product->unit;

        // Cek apakah produk punya satuan grosir/dus?
        if ($product->wholesale_unit) {
            $this->has_wholesale = true;
            $this->wholesale_unit = $product->wholesale_unit;
            $this->wholesale_qty = $product->wholesale_qty;
            $this->wholesale_price = $product->wholesale_price;
        } else {
            $this->has_wholesale = false;
            $this->wholesale_unit = null;
            $this->wholesale_qty = null;
            $this->wholesale_price = null;
        }

        $this->isEditMode = true;
        $this->dispatch('open-modal');
    }

    // === 7. UPDATE PRODUK ===
    public function update()
    {
        $this->validate([
            'name' => 'required',
            'category' => 'nullable',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'barcode' => 'required|unique:products,barcode,' . $this->productId,
            'image' => 'nullable|image|max:2048',

            // Validasi Satuan
            'unit' => 'required',
            'wholesale_unit' => 'nullable|required_if:has_wholesale,true',
            'wholesale_qty' => 'nullable|numeric|required_if:has_wholesale,true',
            'wholesale_price' => 'nullable|numeric|required_if:has_wholesale,true',
        ]);

        if ($this->productId) {
            $product = Product::find($this->productId);
            
            $imagePath = $product->image; 

            if ($this->image) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $this->image->store('products', 'public');
            }

            $product->update([
                'name' => $this->name,
                'category' => $this->category,
                'image' => $imagePath,
                'price' => $this->price,
                'stock' => $this->stock,
                'barcode' => $this->barcode,

                // Update Satuan
                'unit' => $this->unit,
                'wholesale_unit' => $this->has_wholesale ? $this->wholesale_unit : null,
                'wholesale_qty' => $this->has_wholesale ? $this->wholesale_qty : null,
                'wholesale_price' => $this->has_wholesale ? $this->wholesale_price : null,
            ]);

            $this->resetInput();
            $this->dispatch('close-modal');
            session()->flash('success', 'Produk berhasil diperbarui!');
        }
    }

    // === 8. HAPUS PRODUK ===
    public function delete($id)
    {
        $product = Product::find($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        session()->flash('success', 'Produk berhasil dihapus!');
    }

    // === 9. BUKA MODAL CETAK BARCODE (BARU) ===
    public function openPrintModal($id)
    {
        $product = Product::findOrFail($id);
        
        $this->printProductId = $product->id;
        $this->printProductName = $product->name;
        $this->printQty = 1; // Default jumlah stiker
        
        $this->dispatch('open-print-modal');
    }
}
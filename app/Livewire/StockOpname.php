<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\StockAdjustment;

class StockOpname extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    
    // Variabel untuk Modal
    public $selectedProductId;
    public $productName;
    public $systemStock = 0;
    public $physicalStock = 0; // Inputan User
    public $note = '';

    // Ketika search diketik, reset halaman
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Buka Modal Edit
    public function openAdjustModal($id)
    {
        $product = Product::find($id);
        
        $this->selectedProductId = $product->id;
        $this->productName = $product->name;
        $this->systemStock = $product->stock;
        
        // Default stok fisik disamakan dulu dengan sistem
        $this->physicalStock = $product->stock;
        $this->note = '';

        $this->dispatch('open-modal'); // Trigger JS buka modal
    }

    public function saveAdjustment()
    {
        $this->validate([
            'physicalStock' => 'required|numeric|min:0',
            'note' => 'required|string|min:3', // Wajib isi alasan!
        ]);

        $product = Product::find($this->selectedProductId);
        $difference = $this->physicalStock - $this->systemStock;

        // 1. Simpan Riwayat (PENTING!)
        StockAdjustment::create([
            'product_id' => $product->id,
            'previous_stock' => $this->systemStock,
            'actual_stock' => $this->physicalStock,
            'difference' => $difference,
            'note' => $this->note,
        ]);

        // 2. Update Stok Asli Produk
        $product->update(['stock' => $this->physicalStock]);

        session()->flash('success', 'Stok berhasil diperbarui!');
        $this->dispatch('close-modal'); // Trigger JS tutup modal
    }

    public function render()
    {
        $products = Product::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('barcode', 'like', '%' . $this->search . '%')
            ->orderBy('stock', 'asc') // Urutkan dari stok terkecil (biar kelihatan yg habis)
            ->paginate(10);

        return view('livewire.stock-opname', [
            'products' => $products
        ])->layout('layouts.app');
    }
}
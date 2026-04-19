<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;

class TransactionHistory extends Component
{
    use WithPagination;

    // 1. TAMBAHKAN VARIABEL SEARCH
    public $search = '';

    public $selectedTransaction = null;
    
    // Agar pagination rapi pakai style bootstrap
    protected $paginationTheme = 'bootstrap';

    // 2. RESET PAGINATION SAAT MENGETIK
    // (Penting: agar saat cari data baru, halaman kembali ke 1)
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function showDetail($id)
    {
        // Cari transaksi berdasarkan ID, sekalian ambil detail & nama produknya
        // Pastikan nama relasi di model Transaction adalah 'items' atau 'details'
        // Sesuaikan dengan Model Anda (biasanya 'items' atau 'details')
        $this->selectedTransaction = Transaction::with('items.product')->find($id);

        // Jika error, coba ganti 'items.product' menjadi 'details.product' tergantung model Anda
        // $this->selectedTransaction = Transaction::with('details.product')->find($id);

        $this->dispatch('open-detail-modal');
    }

    public function render()
    {
        // 3. LOGIKA PENCARIAN DITERAPKAN DI SINI
        $transactions = Transaction::query()
            ->where('invoice_number', 'like', '%' . $this->search . '%') // Cari Invoice
            ->latest() // Urutkan terbaru
            ->paginate(10);

        return view('livewire.transaction-history', [
            'transactions' => $transactions
        ])->layout('layouts.app');
    }
}
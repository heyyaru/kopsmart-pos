<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Customer;

class DebtManagement extends Component
{
    public $search = '';
    
    // Variabel Pembayaran
    public $selectedTransactionId;
    public $customerName;
    public $totalDebt;
    public $alreadyPaid;
    public $payAmount; // Inputan nominal bayar

    public function openPayModal($id)
    {
        $trx = Transaction::with('customer')->find($id);
        $this->selectedTransactionId = $id;
        $this->customerName = $trx->customer ? $trx->customer->name : 'Tanpa Nama';
        $this->totalDebt = $trx->total_amount;
        $this->alreadyPaid = $trx->amount_paid;
        $this->payAmount = ''; // Reset input
        
        $this->dispatch('open-modal');
    }

    public function submitPayment()
    {
        $this->validate([
            'payAmount' => 'required|numeric|min:1000'
        ]);

        $trx = Transaction::find($this->selectedTransactionId);
        $sisaHutang = $trx->total_amount - $trx->amount_paid;

        if ($this->payAmount > $sisaHutang) {
            session()->flash('error', 'Pembayaran melebihi sisa hutang!');
            return;
        }

        // Tambah jumlah bayar
        $trx->amount_paid += $this->payAmount;

        // Cek jika sudah lunas
        if ($trx->amount_paid >= $trx->total_amount) {
            $trx->payment_status = 'paid';
        }

        $trx->save();

        session()->flash('success', 'Pembayaran berhasil diterima!');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        // Ambil transaksi yang statusnya 'debt' (Belum Lunas)
        $debts = Transaction::with('customer')
            ->where('payment_status', 'debt')
            ->whereHas('customer', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.debt-management', [
            'debts' => $debts
        ])->layout('layouts.app');
    }
}
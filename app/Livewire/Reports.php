<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class Reports extends Component
{
    // Default tanggal: Awal bulan s/d Hari ini
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    // Fungsi Download Excel
    public function downloadExcel()
    {
        return Excel::download(
            new TransactionExport($this->startDate, $this->endDate), 
            'laporan_penjualan.xlsx'
        );
    }

    public function render()
    {
        // Ambil data untuk PREVIEW di layar sebelum didownload
        $transactions = Transaction::whereBetween('created_at', [
                $this->startDate . ' 00:00:00', 
                $this->endDate . ' 23:59:59'
            ])
            ->latest()
            ->get();

        $totalOmzet = $transactions->sum('total_amount');

        return view('livewire.reports', [
            'transactions' => $transactions,
            'totalOmzet' => $totalOmzet
        ])->layout('layouts.app');
    }
}
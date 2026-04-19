<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    // Terima input tanggal dari Controller
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    // Ambil data dari database sesuai tanggal
    public function collection()
    {
        return Transaction::with('user')
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->get();
    }

    // Judul Kolom di Excel (Header)
    public function headings(): array
    {
        return [
            'Tanggal & Jam',
            'No Invoice',
            'Total Belanja (Rp)',
            'Kasir',
        ];
    }

    // Mengatur isi baris per baris
    public function map($transaction): array
    {
        return [
            $transaction->created_at->format('d/m/Y H:i'),
            $transaction->invoice_number,
            $transaction->total_amount, // Biarkan angka agar bisa dijumlah di Excel
            $transaction->user->name ?? 'Unknown',
        ];
    }
}
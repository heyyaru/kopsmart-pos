<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        // --- 1. DATA KARTU STATISTIK (Kode Lama Anda) ---

        // Hitung Omzet Hari Ini
        $todayOmzet = Transaction::whereDate('created_at', Carbon::today())->sum('total_amount');

        // Hitung Jumlah Transaksi Hari Ini
        $todayCount = Transaction::whereDate('created_at', Carbon::today())->count();

        // Hitung Total Produk di Database
        $totalProducts = Product::count();

        // Hitung Stok Menipis (Kurang dari 10)
        $lowStock = Product::where('stock', '<=', 10)->count();


        // --- 2. TAMBAHAN BARU: DATA UNTUK GRAFIK (7 HARI TERAKHIR) ---
        $chartLabels = [];
        $chartValues = [];

        // Loop dari 6 hari yang lalu sampai hari ini (0)
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            // Masukkan Tanggal ke Label (Contoh: "25 Jan")
            $chartLabels[] = $date->format('d M');

            // Hitung total uang masuk pada tanggal tersebut
            $total = Transaction::whereDate('created_at', $date)->sum('total_amount');
            $chartValues[] = $total;
        }

        // --- 3. KIRIM SEMUA DATA KE VIEW ---
        return view('livewire.dashboard', [
            'todayOmzet' => $todayOmzet,
            'todayCount' => $todayCount,
            'totalProducts' => $totalProducts,
            'lowStock' => $lowStock,
            // Jangan lupa kirim data chart ini:
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
        ])->layout('layouts.app'); // Sesuaikan layout ini dengan file layout Anda
    }
}

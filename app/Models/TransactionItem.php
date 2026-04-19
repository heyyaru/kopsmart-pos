<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi (Mass Assignment)
    protected $guarded = [];

    // Relasi ke Produk (opsional tapi bagus)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

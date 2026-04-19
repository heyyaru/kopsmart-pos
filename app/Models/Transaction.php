<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi ke Item Transaksi
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // Relasi ke User (Kasir)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Helper untuk hitung sisa hutang
    public function getRemainingDebtAttribute()
    {
        return $this->total_amount - $this->amount_paid;
    }
}

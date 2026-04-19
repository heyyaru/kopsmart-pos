<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // INI KUNCI PERBAIKANNYA:
    // Memberikan izin agar semua kolom bisa diisi
    protected $guarded = []; 
}
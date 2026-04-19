<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('previous_stock'); // Stok sebelum diubah (Sistem)
            $table->integer('actual_stock');   // Stok fisik (Nyata)
            $table->integer('difference');     // Selisih (Misal: -2 atau +5)
            $table->string('note')->nullable(); // Alasan (Misal: Barang Rusak)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};

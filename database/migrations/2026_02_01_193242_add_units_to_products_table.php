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
        Schema::table('products', function (Blueprint $table) {
            $table->string('unit')->default('Pcs');      // Satuan terkecil (Pcs)
            $table->string('unit_2')->nullable();        // Satuan besar (Dus/Pak/Slop)
            $table->integer('conversion')->default(1);   // 1 Dus = berapa Pcs?
            $table->integer('price_2')->nullable();      // Harga untuk satuan besar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['unit', 'unit_2', 'conversion', 'price_2']);
        });
    }
};

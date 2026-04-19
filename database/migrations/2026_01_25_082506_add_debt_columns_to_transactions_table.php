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
        Schema::table('transactions', function (Blueprint $table) {
            // Menyimpan ID Pelanggan (Boleh kosong jika pembeli umum)
            $table->foreignId('customer_id')->nullable()->after('id');

            // Status: 'paid' (Lunas) atau 'debt' (Kasbon)
            $table->string('payment_status')->default('paid')->after('total_amount');

            // Berapa yang sudah dibayar (Cicilan)
            $table->integer('amount_paid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['customer_id', 'payment_status', 'amount_paid']);
        });
    }
};

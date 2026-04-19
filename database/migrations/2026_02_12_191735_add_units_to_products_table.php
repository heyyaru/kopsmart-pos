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
            // 1. Cek kolom 'unit'
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit')->default('Pcs')->after('stock');
            }

            // 2. Cek kolom 'wholesale_unit'
            if (!Schema::hasColumn('products', 'wholesale_unit')) {
                $table->string('wholesale_unit')->nullable()->after('unit');
            }

            // 3. Cek kolom 'wholesale_qty'
            if (!Schema::hasColumn('products', 'wholesale_qty')) {
                $table->integer('wholesale_qty')->nullable()->after('wholesale_unit');
            }

            // 4. Cek kolom 'wholesale_price'
            if (!Schema::hasColumn('products', 'wholesale_price')) {
                $table->integer('wholesale_price')->nullable()->after('wholesale_qty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['unit', 'wholesale_unit', 'wholesale_qty', 'wholesale_price']);
        });
    }
};

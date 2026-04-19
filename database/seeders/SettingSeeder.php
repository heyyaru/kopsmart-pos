<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'shop_name' => 'TOKO MAJU JAYA',
            'address' => 'Jl. Merdeka No. 45, Jakarta',
            'phone' => '0812-3456-7890'
        ]);
    }
}
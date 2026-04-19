<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Setting;

class Settings extends Component
{
    public $shop_name, $address, $phone;

    public function mount()
    {
        // Ambil data setting pertama (ID 1)
        $setting = Setting::first();
        if ($setting) {
            $this->shop_name = $setting->shop_name;
            $this->address = $setting->address;
            $this->phone = $setting->phone;
        }
    }

    public function save()
    {
        $this->validate([
            'shop_name' => 'required',
        ]);

        // Update data ID 1 (karena setting cuma ada 1 baris)
        $setting = Setting::first();
        $setting->update([
            'shop_name' => $this->shop_name,
            'address' => $this->address,
            'phone' => $this->phone,
        ]);

        session()->flash('success', 'Pengaturan berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.settings');
    }
}

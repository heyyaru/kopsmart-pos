<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Login extends Component
{
    // --- State Tab (Login vs Register) ---
    public $activeTab = 'login'; // Default tab adalah login

    // --- Form Login ---
    public $email;
    public $password;
    public $remember = false; // Fitur Ingat Saya

    // --- Form Register ---
    public $reg_name;
    public $reg_email;
    public $reg_password;
    public $reg_password_confirmation;

    // Fungsi untuk Ganti Tab
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetErrorBag(); // Hapus pesan error saat pindah tab
    }

    // Fungsi Login
    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Auth::attempt menerima parameter ke-2 boolean untuk "remember me"
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return redirect()->route('pos'); // Sesuaikan redirect
        }

        $this->addError('email', 'Email atau password salah.');
    }

    // Fungsi Register
    public function register()
    {
        $this->validate([
            'reg_name' => 'required|string|max:255',
            'reg_email' => 'required|email|unique:users,email',
            'reg_password' => 'required|min:6|same:reg_password_confirmation',
        ], [
            'reg_email.unique' => 'Email ini sudah terdaftar.',
            'reg_password.same' => 'Konfirmasi password tidak cocok.',
            'reg_password.min' => 'Password minimal 6 karakter.'
        ]);

        // Buat User Baru
        $user = User::create([
            'name' => $this->reg_name,
            'email' => $this->reg_email,
            'password' => Hash::make($this->reg_password),
        ]);

        // Langsung Login
        Auth::login($user);
        session()->flash('success', 'Akun berhasil dibuat!');
        
        return redirect()->route('pos');
    }

    public function render()
    {
        return view('livewire.login')->layout('layouts.app'); 
        // Pastikan layout sesuai dengan struktur project Anda (default Laravel 10/11 biasanya components.layouts.app)
    }
}
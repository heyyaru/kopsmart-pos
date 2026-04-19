<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Password;

class ForgotPassword extends Component
{
    public $email;
    public $status = null; // Untuk pesan sukses
    public $emailError = null; // Untuk pesan error manual

    public function sendResetLink()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        // Kirim Link Reset Password
        // Pastikan Anda sudah setting SMTP di .env agar email terkirim
        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->status = __($status);
            $this->email = ''; // Reset input
            $this->emailError = null;
        } else {
            $this->emailError = __($status);
            $this->status = null;
        }
    }

    public function render()
    {
        // Pastikan layout mengarah ke layout Anda (layouts.app)
        return view('livewire.forgot-password')->layout('layouts.app');
    }
}
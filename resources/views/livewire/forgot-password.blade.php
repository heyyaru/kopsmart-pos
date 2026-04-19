<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow-sm border-0" style="width: 400px; border-radius: 15px;">
        <div class="card-body p-4 p-md-5">
            
            {{-- Header --}}
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-key-fill fs-2"></i>
                </div>
                <h4 class="fw-bold">Lupa Password?</h4>
                <p class="text-muted small">
                    Masukkan email Anda, kami akan mengirimkan link untuk mereset password.
                </p>
            </div>

            {{-- Pesan Sukses --}}
            @if ($status)
                <div class="alert alert-success small mb-3">
                    <i class="bi bi-check-circle me-1"></i> {{ $status }}
                </div>
            @endif

            {{-- Form --}}
            <form wire:submit="sendResetLink">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Email Address</label>
                    <input type="email" wire:model="email" class="form-control" placeholder="nama@email.com">
                    
                    {{-- Error Validasi --}}
                    @error('email') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                    
                    {{-- Error dari Password Broker (Misal email tidak ditemukan) --}}
                    @if($emailError) 
                        <span class="text-danger small d-block mt-1">{{ $emailError }}</span> 
                    @endif
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill mb-3">
                    <span wire:loading.remove wire:target="sendResetLink">KIRIM LINK RESET</span>
                    <span wire:loading wire:target="sendResetLink">Mengirim...</span>
                </button>
            </form>

            {{-- Tombol Kembali --}}
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-decoration-none small fw-bold text-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Login
                </a>
            </div>

        </div>
    </div>
</div>
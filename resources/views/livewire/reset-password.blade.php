<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow-sm border-0" style="width: 400px; border-radius: 15px;">
        <div class="card-body p-4 p-md-5">
            
            <div class="text-center mb-4">
                <h4 class="fw-bold">Reset Password</h4>
                <p class="text-muted small">Silakan buat password baru untuk akun Anda.</p>
            </div>

            <form wire:submit="resetPassword">
                {{-- Token & Email (Hidden/Readonly) --}}
                <input type="hidden" wire:model="token">
                
                <div class="mb-3">
                    <label class="form-label fw-bold small">Email Address</label>
                    <input type="email" wire:model="email" class="form-control" readonly style="background-color: #e9ecef;">
                    @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Password Baru</label>
                    <input type="password" wire:model="password" class="form-control" placeholder="Minimal 6 karakter">
                    @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Konfirmasi Password Baru</label>
                    <input type="password" wire:model="password_confirmation" class="form-control" placeholder="Ulangi password">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                    <span wire:loading.remove>UBAH PASSWORD</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </form>

        </div>
    </div>
</div>
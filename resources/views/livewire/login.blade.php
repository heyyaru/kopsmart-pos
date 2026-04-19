<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow-sm border-0" style="width: 420px; border-radius: 15px;">
        <div class="card-body p-4 p-md-5">

            {{-- === 1. LOGO & JUDUL === --}}
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Logo Toko"
                     style="width: 80px; height: auto; object-fit: contain;"
                     class="mb-3">
                <h3 class="fw-bold text-primary">🔐 POS System</h3>
                <p class="text-muted small">
                    {{ $activeTab === 'login' ? 'Silakan masuk ke akun Anda' : 'Daftar akun kasir baru' }}
                </p>
            </div>

            {{-- === 2. NAVIGASI TAB (Livewire) === --}}
            <ul class="nav nav-pills justify-content-center mb-4">
                <li class="nav-item cursor-pointer">
                    <a class="nav-link {{ $activeTab === 'login' ? 'active' : '' }}"
                       href="#" wire:click.prevent="switchTab('login')">
                       Masuk
                    </a>
                </li>
                <li class="nav-item cursor-pointer">
                    <a class="nav-link {{ $activeTab === 'register' ? 'active' : '' }}"
                       href="#" wire:click.prevent="switchTab('register')">
                       Daftar Baru
                    </a>
                </li>
            </ul>

            {{-- === 3. KONTEN FORM === --}}

            {{-- A. FORM LOGIN --}}
            @if($activeTab === 'login')
                <form wire:submit="login">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" wire:model="email" class="form-control" placeholder="admin@toko.com">
                        </div>
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="bi bi-key"></i></span>
                            <input type="password" wire:model="password" class="form-control" placeholder="••••••••">
                        </div>
                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            {{-- Fitur Ingat Saya --}}
                            <input class="form-check-input" type="checkbox" wire:model="remember" id="rememberMe">
                            <label class="form-check-label small text-muted" for="rememberMe">
                                Ingat Saya
                            </label>
                        </div>
                        {{-- Link Lupa Password (Standar Laravel) --}}
                        <a href="{{ route('password.request') }}" class="small text-decoration-none fw-bold">
                            Lupa Password?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                        <span wire:loading.remove wire:target="login">MASUK APLIKASI</span>
                        <span wire:loading wire:target="login">Memproses...</span>
                    </button>
                </form>
            @endif

            {{-- B. FORM REGISTER --}}
            @if($activeTab === 'register')
                <form wire:submit="register">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Lengkap</label>
                        <input type="text" wire:model="reg_name" class="form-control" placeholder="Nama Anda">
                        @error('reg_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Email Address</label>
                        <input type="email" wire:model="reg_email" class="form-control" placeholder="email@baru.com">
                        @error('reg_email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Password</label>
                        <input type="password" wire:model="reg_password" class="form-control" placeholder="Min 6 karakter">
                        @error('reg_password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Konfirmasi Password</label>
                        <input type="password" wire:model="reg_password_confirmation" class="form-control" placeholder="Ulangi password">
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-pill">
                        <span wire:loading.remove wire:target="register">DAFTAR SEKARANG</span>
                        <span wire:loading wire:target="register">Mendaftar...</span>
                    </button>
                </form>
            @endif

        </div>
        <div class="card-footer bg-white border-0 text-center pb-4">
            <small class="text-muted text-opacity-50">&copy; {{ date('Y') }} Aplikasi POS</small>
        </div>
    </div>

    {{-- Style Khusus untuk Tab Navigasi agar cantik --}}
    <style>
        .nav-pills .nav-link {
            color: #6c757d;
            font-weight: 600;
            border-radius: 50px;
            padding: 8px 25px;
            margin: 0 5px;
            transition: all 0.3s;
        }
        .nav-pills .nav-link.active {
            background-color: #0d6efd;
            color: white;
            box-shadow: 0 4px 6px rgba(13, 110, 253, 0.3);
        }
    </style>
</div>

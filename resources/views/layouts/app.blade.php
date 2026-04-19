<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Aplikasi POS' }}</title>

    {{-- 1. FAVICON --}}
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    {{-- === TAMBAHKAN KODE CSS UBAH WARNA DI SINI === --}}
    <style>
        :root {
            /* Warna Dasar Utama (Ganti #8e44ad dengan warna kesukaan Anda) */
            --bs-primary: #57a0d3; 
            
            /* Warna RGB untuk efek transparansi shadow bawaan Bootstrap */
            --bs-primary-rgb: 142, 68, 173; 
        }

        /* Memaksa background navbar & elemen ber-class bg-primary berubah */
        .bg-primary {
            background-color: var(--bs-primary) !important;
        }

        /* Memaksa warna teks ber-class text-primary berubah */
        .text-primary {
            color: var(--bs-primary) !important;
        }

        /* Memaksa warna tombol berubah */
        .btn-primary {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: white !important;
        }
        
        /* Warna tombol saat kursor diarahkan (hover) - Dibuat sedikit lebih gelap */
        .btn-primary:hover {
            background-color: #2266cc !important; 
            border-color: #2266cc !important;
        }
    </style>
    {{-- === AKHIR KODE UBAH WARNA === --}}

    @livewireStyles
</head>

<body class="bg-light">

    {{-- LOGIKA UTAMA: Hanya tampilkan Navbar jika user SUDAH LOGIN --}}
    @auth
        
        {{-- Ambil nama toko dari database --}}
        @php
            try {
                $setting = \App\Models\Setting::first();
                $namaToko = $setting ? $setting->shop_name : 'AL-BELAJAR POS';
            } catch (\Exception $e) {
                $namaToko = 'Aplikasi POS';
            }
        @endphp

        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
            <div class="container">

                {{-- 2. LOGO DI NAVBAR --}}
                <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                        style="height: 40px; width: auto; object-fit: contain; background-color: white; padding: 2px; border-radius: 4px;">
                    <span>{{ $namaToko }}</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">

                        {{-- === MENU UMUM === --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}"
                                href="{{ route('dashboard') }}">Dashboard</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pos') ? 'active fw-bold' : '' }}"
                                href="{{ route('pos') }}">Kasir</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('history') ? 'active fw-bold' : '' }}"
                                href="{{ route('history') }}">Riwayat</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('debt.index') ? 'active fw-bold' : '' }}"
                                href="{{ route('debt.index') }}">Buku Hutang</a>
                        </li>

                        {{-- === MENU KHUSUS ADMIN === --}}
                        @if (auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('products') ? 'active fw-bold' : '' }}"
                                    href="{{ route('products') }}">Produk</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('stock.opname') ? 'active fw-bold' : '' }}"
                                    href="{{ route('stock.opname') }}">Stok Opname</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports') ? 'active fw-bold' : '' }}"
                                    href="{{ route('reports') }}">Laporan</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('settings') ? 'active fw-bold' : '' }}"
                                    href="{{ route('settings') }}">
                                    <i class="bi bi-gear-fill"></i> Setting
                                </a>
                            </li>
                        @endif

                        {{-- === TOMBOL LOGOUT === --}}
                        <li class="nav-item border-start ms-3 ps-3">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit"
                                    class="nav-link text-white fw-bold bg-danger rounded px-3 border-0">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
    @endauth

    {{-- KONTEN UTAMA --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @livewireScripts
</body>

</html>
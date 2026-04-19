<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode - {{ $product->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 10px; background-color: #f4f4f4; }
        .sticker-container { display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-start; }
        .sticker {
            width: 155px; background: white; border: 1px dashed #ccc;
            padding: 10px; text-align: center; border-radius: 4px;
            page-break-inside: avoid;
        }
        .product-name {
            font-size: 11px; font-weight: bold; margin-bottom: 5px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            overflow: hidden; height: 28px;
        }
        .barcode-wrapper {
            height: 45px; display: flex; align-items: center; justify-content: center; overflow: hidden;
        }
        .barcode-wrapper svg { width: 100% !important; height: auto !important; max-height: 40px; }
        .barcode-number { font-size: 10px; margin-top: 2px; color: #333; }
        .price { font-size: 13px; font-weight: bold; margin-top: 5px; border-top: 1px solid #eee; padding-top: 3px; }
        .no-print {
            margin-bottom: 20px; padding: 12px 24px; background: #2563eb; color: white;
            border: none; border-radius: 6px; cursor: pointer; font-weight: bold;
        }
        @media print {
            body { background: white; margin: 0; }
            .no-print { display: none; }
            .sticker { border: 1px solid #000; margin: 2px; }
        }
    </style>
</head>
<body>

    <button class="no-print" onclick="window.print()">🖨️ Mulai Cetak (Ctrl+P)</button>

    <div class="sticker-container">
        @php
            $generator = null;
            $errorMessage = null;

            // 1. GUNAKAN BACKSLASH MUTLAK (\)
            try {
                if (class_exists('\Picqer\Barcode\BarcodeGeneratorSVG')) {
                    $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
                } else {
                    $errorMessage = "Class \Picqer\Barcode\BarcodeGeneratorSVG tidak ditemukan. Autoload gagal.";
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
            }

            $barcodeValue = $product->barcode ?? $product->code ?? '000000';
        @endphp

        @if($errorMessage)
            <div style="color: red; padding: 20px; background: #fee2e2; border-radius: 8px; width: 100%;">
                <strong>⚠️ DEBUG ERROR:</strong> {{ $errorMessage }} <br><br>
                <strong>Solusi Paksa:</strong>
                <ol>
                    <li>Buka terminal, jalankan: <code>composer dump-autoload</code></li>
                    <li>Jika pakai <code>php artisan serve</code>, matikan (Ctrl+C) lalu jalankan lagi.</li>
                </ol>
            </div>
        @else
            @for ($i = 0; $i < $qty; $i++)
                <div class="sticker">
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="barcode-wrapper">
                        {{-- Gunakan TYPE_CODE_128 dengan backslash --}}
                        {!! $generator->getBarcode($barcodeValue, $generator::TYPE_CODE_128) !!}
                    </div>
                    <div class="barcode-number">{{ $barcodeValue }}</div>
                    <div class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                </div>
            @endfor
        @endif
    </div>

</body>
<script>
    // Menunggu seluruh halaman (termasuk SVG barcode) selesai dimuat
    window.onload = function() {
        window.print();

        // Opsional: Menutup tab otomatis setelah dialog print selesai/dibatalkan
         window.onafterprint = function() {
             window.close();
         };
    }
</script>
</html>


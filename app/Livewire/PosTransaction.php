<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class PosTransaction extends Component
{
    public $shop_name, $shop_address, $shop_phone;
    // --- PROPERTI UTAMA ---
    public $search = '';
    public $cart = [];
    public $payAmount = 0;
    public $lastTransaction;

    // --- PROPERTI HUTANG & PELANGGAN ---
    public $customers = [];
    public $customerId = '';
    public $isDebt = false;

    // --- PROPERTI TAMBAH PELANGGAN BARU ---
    public $newCustomerName = '';
    public $newCustomerPhone = '';

    public function mount()
    {
        // Ambil keranjang dari session jika ada
        $this->cart = session()->get('cart', []);

        // Ambil daftar pelanggan untuk dropdown
        $this->refreshCustomerList();

        $settings = \App\Models\Setting::first();

        if ($settings) {
            $this->shop_name = $settings->shop_name;
            $this->shop_address = $settings->address; // sesuaikan nama kolom di DB
            $this->shop_phone = $settings->phone;    // sesuaikan nama kolom di DB
        }
    }

    // --- LOGIKA MULTI-SATUAN (PCS / DUS) ---
    public function addToCart($productId, $type = 'unit')
    {
        $product = Product::find($productId);

        if (!$product) {
            session()->flash('error', 'Produk tidak ditemukan');
            return;
        }

        // Tentukan variabel berdasarkan tipe satuan
        if ($type == 'unit_2' && $product->unit_2) {
            // Logika Satuan Besar (Dus)
            $price = $product->price_2;
            $name = $product->name . ' (' . $product->unit_2 . ')';
            $cartId = $product->id . '_2'; // ID Unik untuk Dus
            $deductStock = $product->conversion; // Kurangi stok sebanyak isi dus
        } else {
            // Logika Satuan Kecil (Pcs) - Default
            $price = $product->price;
            $name = $product->name . ' (' . $product->unit . ')';
            $cartId = $product->id . '_1'; // ID Unik untuk Pcs
            $deductStock = 1;
        }

        // Cek Stok (Validasi Multi Satuan)
        // Kita cek apakah stok total di DB cukup untuk pengurangan ini
        if ($product->stock < $deductStock) {
            session()->flash('error', 'Stok tidak cukup untuk satuan ini!');
            return;
        }

        // Masukkan / Update Keranjang
        if (isset($this->cart[$cartId])) {
            $this->cart[$cartId]['qty']++;
        } else {
            $this->cart[$cartId] = [
                'id' => $product->id, // ID Asli Database
                'name' => $name,
                'price' => $price,
                'qty' => 1,
                'type' => $type, // unit atau unit_2
                'conversion' => $deductStock // Pengali stok
            ];
        }

        $this->saveCart();
    }

    public function updateQty($cartId, $action)
    {
        if (!isset($this->cart[$cartId])) return;

        if ($action == 'plus') {
            // Cek stok real-time (Support Multi Satuan)
            $item = $this->cart[$cartId];
            $product = Product::find($item['id']);

            // Stok yang dibutuhkan = (Qty Sekarang + 1) * Konversi Satuan
            $needed = ($item['qty'] + 1) * $item['conversion'];

            if ($product->stock >= $needed) {
                $this->cart[$cartId]['qty']++;
            } else {
                session()->flash('error', 'Stok Maksimal!');
            }
        } else {
            if ($this->cart[$cartId]['qty'] > 1) {
                $this->cart[$cartId]['qty']--;
            } else {
                unset($this->cart[$cartId]);
            }
        }
        $this->saveCart();
    }

    public function removeItem($cartId)
    {
        unset($this->cart[$cartId]);
        $this->saveCart();
    }

    public function saveCart()
    {
        session()->put('cart', $this->cart);
    }

    // --- LOGIKA SCAN BARCODE ---
    public function handleScan()
    {
        // Scan barcode otomatis masuk sebagai satuan terkecil (unit)
        $product = Product::where('barcode', $this->search)->first();

        if ($product) {
            $this->addToCart($product->id, 'unit');
            $this->search = '';
            session()->flash('success', 'Berhasil scan: ' . $product->name);
        } else {
            session()->flash('error', 'Barcode tidak terdaftar!');
        }
    }

    // --- LOGIKA TRANSAKSI & DATABASE ---
    public function submitTransaction()
    {
        if (empty($this->cart)) return;

        // 1. Validasi Hutang
        if ($this->isDebt && empty($this->customerId)) {
            session()->flash('error', 'Untuk Hutang/Kasbon, wajib pilih Nama Pelanggan!');
            return;
        }

        // 2. Validasi Pembayaran Tunai
        if (!$this->isDebt && $this->payAmount < $this->getTotalProperty()) {
            session()->flash('error', 'Uang pembayaran kurang!');
            return;
        }

        DB::beginTransaction();

        try {
            // Tentukan status & nominal
            $status = $this->isDebt ? 'debt' : 'paid';
            $amountPaid = $this->isDebt ? 0 : (int)$this->payAmount;
            $changeAmount = $this->isDebt ? 0 : ((int)$this->payAmount - $this->getTotalProperty());

            // A. Simpan Transaksi Utama
            $transaction = Transaction::create([
                'invoice_number' => 'INV/' . date('Ymd') . '/' . rand(1000, 9999),
                'user_id' => auth()->id() ?? 1,
                'customer_id' => $this->customerId ?: null,
                'total_amount' => $this->getTotalProperty(),
                'pay_amount' => $amountPaid,
                'change_amount' => $changeAmount,
                'payment_status' => $status,
            ]);

            // B. Simpan Item & Kurangi Stok
            foreach ($this->cart as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'price' => $item['price']
                ]);

                // PENGURANGAN STOK (PENTING: Gunakan faktor konversi)
                // Jika beli Dus, conversion = 12. Jika Pcs, conversion = 1.
                $deductQty = $item['qty'] * $item['conversion'];

                Product::where('id', $item['id'])->decrement('stock', $deductQty);
            }

            // Load relasi agar nama pelanggan muncul di struk
            $this->lastTransaction = Transaction::with(['items.product', 'customer'])->find($transaction->id);

            DB::commit();

            // Reset Semua
            $this->resetTransaction(false); // False artinya jangan hapus lastTransaction dulu biar bisa print
            $this->dispatch('transaction-success');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function resetTransaction($clearLastTrx = true)
    {
        $this->cart = [];
        $this->payAmount = 0;
        $this->customerId = '';
        $this->isDebt = false;

        session()->forget('cart');

        if ($clearLastTrx) {
            $this->lastTransaction = null;
        }
    }

    // --- LOGIKA TAMBAH PELANGGAN ---
    public function addCustomer()
    {
        $this->validate([
            'newCustomerName' => 'required|min:3',
        ]);

        $customer = Customer::create([
            'name' => $this->newCustomerName,
            'phone' => $this->newCustomerPhone ?? '-',
            'address' => '-'
        ]);

        // Refresh list & pilih user baru
        $this->refreshCustomerList();
        $this->customerId = $customer->id;

        // Reset Input Modal
        $this->newCustomerName = '';
        $this->newCustomerPhone = '';

        // Tutup Modal & Beri notif
        $this->dispatch('close-customer-modal');
        session()->flash('success', 'Pelanggan Baru Ditambahkan!');
    }

    private function refreshCustomerList()
    {
        $this->customers = Customer::orderBy('name', 'asc')->get();
    }

    // --- COMPUTED PROPERTIES ---
    public function getTotalProperty()
    {
        return array_reduce($this->cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['qty']);
        }, 0);
    }

    public function getChangeProperty()
    {
        if ($this->isDebt) return 0;
        return max(0, (int)$this->payAmount - $this->getTotalProperty());
    }

    public function render()
    {
        $products = Product::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('barcode', 'like', '%' . $this->search . '%')
            ->latest()
            ->take(12)
            ->get();

        return view('livewire.pos-transaction', [
            'products' => $products
        ]);
    }
}

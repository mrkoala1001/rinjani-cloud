# 3. Isolasi Data Antar Owner (Multi-Tenancy)

**Pertanyaan:**
> Apakah benar, jika saya login sebagai owner perusahaan kedua, data vocher terjual, data vocher distribusi, data pppoe, data billing tetap terlihat? Padahal itu milik owner perusahaan pertama.

**Jawaban: BENAR SEBAGIAN (ADA KEBOCORAN DATA).**

Berikut detilnya berdasarkan audit kode:

### A. Data Billing (Pemasukan, Pengeluaran, Hutang) -> **AMAN**
Data Billing menggunakan Eloquent Model (`Income::`, `Expense::`, `Debt::`) yang sudah dipasang "Global Scope" (`TenantScope`).
Artinya, ketika query dijalankan, sistem otomatis menambahkan `WHERE user_id = ...`.
- **File:** `app/Http/Controllers/BillingController.php`
- **Status:** Owner 2 **TIDAK BISA** melihat data Owner 1.

### B. Data Voucher (Terjual & Distribusi) -> **BOCOR (TIDAK AMAN)**
Data Voucher di halaman laporan menggunakan `DB::table(...)` (Query Builder manual), bukan Eloquent Model.
Query builder **TIDAK** otomatis menerapkan Global Scope.
- **File:** `app/Http/Controllers/VoucherController.php`
- **Method:** `distribution()`, `sold()`
- **Kode Bermasalah:**
  ```php
  // Menggunakan DB::table bypassing TenantScope
  $distributions = DB::table('billing_history as bh')
      ->leftJoin(...)
      // TIDAK ADA filter ->where('bh.user_id', auth()->id())
      ->get();
  ```
- **Status:** Owner 2 **BISA MELIHAT** data voucher milik Owner 1.

### C. Data PPPoE & Router -> **BERPOTENSI SALAH SAMBUNG**
Koneksi ke MikroTik diambil menggunakan kode:
```php
$mkConfig = MikrotikConfig::first(); // Mengambil baris PERTAMA di tabel
```
- **Masalah:** Fungsi `first()` mengambil data paling atas di tabel database tanpa mempedulikan siapa yang login.
- **Skenario:**
  1. Owner 1 simpan router (ID 1).
  2. Owner 2 simpan router (ID 2).
  3. Owner 2 login, sistem memanggil `MikrotikConfig::first()`, yang terambil adalah ID 1 (Punya Owner 1).
- **Status:** Owner 2 bisa terkoneksi ke Router Owner 1 (jika kredensial valid/tersimpan), sehingga melihat data user aktif/PPPoE dari router yang salah.

---

### **Rekomendasi Perbaikan:**

1.  **Perbaiki Koneksi Router:**
    Ubah `MikrotikConfig::first()` menjadi `MikrotikConfig::where('user_id', auth()->id())->first()`.

2.  **Perbaiki Query Laporan Voucher:**
    Tambahkan `.where('bh.user_id', auth()->id())` pada semua query `DB::table('billing_history')`.

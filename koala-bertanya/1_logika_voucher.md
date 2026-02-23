# Dokumentasi Pertanyaan & Jawaban (Koala)

Folder ini berisi rangkuman pertanyaan teknis terkait sistem voucher Mikhmon dan jawabannya berdasarkan analisa kode.

---

## 1. Visibilitas Voucher Terjual vs User Aktif

**Pertanyaan:**
> Apakah benar, ketika saya login menggunakan password yang ada di menu vocher distribusi, akun saya akan terlihat di vocher terjual? Lalu ketika sesi tidak aktif atau habis, maka akun saya itu akan otomatis hilang dari tabel?

**Jawaban: BENAR.**

**Penjelasan Teknis:**
Fitur "Voucher Terjual" (`/voucher/sold`) saat ini dirancang untuk menampilkan status **Live** dari router.
- Sistem melakukan query ke MikroTik untuk mengambil daftar user yang sedang aktif (`/ip/hotspot/active/print`).
- Kemudian, sistem mencocokkan data aktif tersebut dengan database.
- Jika user tidak sedang aktif (logout atau expired), user tersebut tidak akan muncul di tabel ini, meskipun datanya masih tersimpan di database `billing_history`.

**Lokasi Kode:**
File: `app/Http/Controllers/VoucherController.php`
Method: `sold()`

```php
// Mengambil user aktif dari Router
$activeUsers = $client->query('/ip/hotspot/active/print')->read();

// ...

// Filter database agar HANYA menampilkan user yang ada di list aktif
$vouchers = DB::table('billing_history as bh')
    ->whereIn('bh.username', $activeUsernames) // <--- Filter ini penyebabnya
    ->get();
```

---

## 2. Masa Aktif (Validity) vs Kuota Waktu (Uptime)

**Pertanyaan:**
> Apakah benar jika sesi (masa aktif) sudah habis atau expired, akun saya tetap bisa login?

**Jawaban: BENAR.**

**Penjelasan Teknis:**
Saat voucher dibuat `.store()`, sistem saat ini hanya mengirimkan batasan:
1. `limit-uptime`: Batas durasi pemakaian (contoh: 3 jam).
2. `limit-bytes-total`: Batas kuota data (contoh: 1 GB).

Sistem **BELUM** mengirimkan skrip atau scheduler untuk membatasi "Masa Aktif Berjalan" (Validity).
- **Contoh Kasus:** Voucher 3 Jam, Masa Aktif 1 Hari.
- **Perilaku Saat Ini:** Jika user login selama 1 jam, lalu logout. Besoknya (setelah lewat 1 hari), user **MASIH BISA LOGIN** kembali untuk menghabiskan sisa 2 jam voucher-nya. Hal ini terjadi karena MikroTik hanya mengecek sisa waktu (`limit-uptime`), dan tidak ada script yang menghapus user setelah 1 hari sejak login pertama.

**Lokasi Kode:**
File: `app/Http/Controllers/VoucherController.php`
Method: `store()`

```php
// Code hanya set limit-uptime dan limit-bytes
if ($timeLimit) $addData['limit-uptime'] = $timeLimit;
if ($dataLimit) $addData['limit-bytes-total'] = $dataLimit;

// TIDAK ADA kode untuk set 'on-login' script atau scheduler validity
```

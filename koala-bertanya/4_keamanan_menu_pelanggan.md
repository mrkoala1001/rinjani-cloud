# 5. Keamanan Menu Pelanggan (Customer)

**Pertanyaan:**
> Kalau di menu pelanggan?

**Jawaban: BOCOR TOTAL.**

Berdasarkan audit kode pada `app/Http/Controllers/CustomerController.php` dan `app/Models/CustomerMember.php`:

**1. Tidak Ada Filter Pemilik (Tenant)**
Kode saat ini menampilkan **SELURUH** data pelanggan yang ada di database tanpa mempedulikan siapa yang login.
```php
// Baris 11: Mengambil query CustomerMember tanpa scope/filter user_id
$query = CustomerMember::query();
// ...
$customers = $query->get();
```
**Akibatnya:** Owner 2 bisa melihat, mengedit, dan menghapus data pelanggan (Member, Perumahan, Reseller) milik Owner 1.

**2. Tidak Ada Kolom Penanda Pemilik**
Tabel `customer_members` saat ini belum memiliki kolom `user_id`. Ini berarti sistem bahkan tidak tahu pelanggan "A" itu milik Siapa. Semua dianggap milik bersama (public).

---

### **Rekomendasi Perbaikan:**

1.  **Database Migration:** Tambahkan kolom `user_id` ke tabel `customer_members`.
2.  **Model Power-Up:** Tambahkan `use BelongsToTenant` di model `CustomerMember`.
3.  **Data Migration:** Tentukan pemilik untuk data pelanggan yang sudah ada (misal di-assign ke Admin utama/Owner 1 sementara).

# 4. Keamanan Menu & Sub-Menu Voucher

**Pertanyaan:**
> Apakah kebocoran data juga terjadi pada sub-sub menu voucher?

**Jawaban: YA, HAMPIR SEMUA.**

Berdasarkan audit mendalam pada kode `VoucherController` dan Model terkait, berikut status keamanannya:

### 1. Menu Generate (`voucher.generate`) - **BOCOR & SALAH SAMBUNG**
- **Reseller List:** ❌ **BOCOR.** Mengambil data dari tabel `customer_members` yang **BELUM** dipasang pelindung (`TenantScope`). Owner 2 bisa melihat reseller milik Owner 1.
- **Daftar Profil & Server:** ❌ **SALAH SAMBUNG.** Mengambil data langsung dari Router menggunakan `getClient()`. Karena `getClient()` mengambil konfigurasi router pertama di DB, Owner 2 akan melihat Profil & Server dari Router milik Owner 1.
- **Template:** ✅ **AMAN.** Model `VoucherTemplate` sudah menggunakan `use BelongsToTenant`, sehingga hanya template milik user sendiri yang muncul.

### 2. Menu List User (`voucher.list`) - **SALAH SAMBUNG**
- Data diambil langsung dari Router. Karena koneksi router salah sasaran (ke router Owner 1), maka Owner 2 akan melihat daftar user hotspot milik Owner 1.

### 3. Menu User Profile (`voucher.profiles`) - **SALAH SAMBUNG & BOCOR**
- **Data dari Router:** ❌ **SALAH SAMBUNG.** Melihat profil router orang lain.
- **Data Harga/Validity (Metadata):** ❌ **BOCOR.** Tabel `hotspot_profile_metadata` **BELUM** memiliki kolom `user_id` dan `TenantScope`. Artinya harga profile dengan nama yang sama (misal "1JAM") akan bentrok antar owner.

### 4. Menu Template (`voucher.templates`) - **AMAN**
- Menggunakan model `VoucherTemplate` yang sudah terlindungi.

### 5. Menu Voucher Terjual (`voucher.sold`) - **SALAH SAMBUNG & BOCOR**
- **Live Stats:** ❌ **SALAH SAMBUNG.** Mengambil data aktif dari router orang lain.
- **Tabel Data:** ❌ **BOCOR.** Query database menggunakan `DB::table` tanpa filter user, sehingga menampilkan history penjualan admin lain.

### 6. Menu Voucher Distribusi (`voucher.distribution`) - **BOCOR**
- Tabel menggunakan `DB::table` tanpa filter, menampilkan distribusi voucher milik admin lain.

---

### **Ringkasan Perbaikan yang Diperlukan:**

1.  **Model Power-Up:** Tambahkan `TenantScope` dan kolom `user_id` pada model `CustomerMember` dan `HotspotProfileMetadata`.
2.  **Controller Fix:** Ubah semua query `DB::table` menjadi Eloquent Model atau tambahkan filter `where('user_id', auth()->id())`.
3.  **Router Connection Fix:** Perbaiki fungsi `getClient()` agar mengambil router milik user yang login, bukan router pertama di database.

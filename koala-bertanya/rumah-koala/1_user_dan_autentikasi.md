# 1. Database: User & Autentikasi

Dokumen ini menjelaskan struktur tabel yang berkaitan dengan pengguna sistem (Login, Role, dan Sesi).

---

## 1. Tabel `users`
Tabel utama untuk menyimpan data pengguna aplikasi.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT (PK)` | ID Unik Pengguna. |
| `name` | `VARCHAR` | Nama Lengkap atau Nama Perusahaan (Mitra). |
| `email` | `VARCHAR` | Alamat Email (digunakan untuk Login). |
| `password` | `VARCHAR` | Password (Terenkripsi Bcrypt). |
| `role` | `VARCHAR` | Peran Pengguna: `isp` (Admin Pusat) atau `owner` (Mitra). |
| `location` | `VARCHAR` | Lokasi/Alamat Mitra (Khusus Mitra). |
| `notes` | `TEXT` | Catatan Tambahan (Khusus Mitra). |
| `wa_number` | `VARCHAR` | Nomor WhatsApp (Untuk notifikasi/kontak). |
| `username` | `VARCHAR` | Username Alternatif (Selain Email). |
| `is_active` | `BOOLEAN` | Status Akun (`1` = Aktif, `0` = Suspend). |
| `remember_token` | `VARCHAR` | Token untuk fitur "Remember Me". |
| `created_at` | `TIMESTAMP` | Waktu pembuatan akun. |
| `updated_at` | `TIMESTAMP` | Waktu terakhir update data. |

---

## 2. Tabel `password_resets`
Tabel sementara untuk fitur Lupa Password.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `email` | `VARCHAR` | Email yang merequest reset password. |
| `token` | `VARCHAR` | Token rahasia yang dikirim ke email. |
| `created_at` | `TIMESTAMP` | Waktu request dibuat. |

---

## 3. Tabel `sessions`
Menyimpan data sesi login pengguna (jika menggunakan driver database).

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `VARCHAR` | Session ID. |
| `user_id` | `BIGINT` | ID User yang sedang login. |
| `ip_address` | `VARCHAR` | Alamat IP User. |
| `user_agent` | `TEXT` | Info Browser & OS User. |
| `payload` | `TEXT` | Data sesi terenkripsi. |
| `last_activity` | `INTEGER` | Timestamp aktivitas terakhir. |

# 3. Database: Voucher & Hotspot

Dokumen ini menjelaskan struktur tabel yang berkaitan dengan Bisnis Voucheran dan Hotspot.

---

## 1. Tabel `billing_history` (Data Voucher)
Ini adalah tabel **Paling Kritis**. Menyimpan setiap voucher yang digenerate dan dijual.
**Fitur Terkait:** Voucher Generate, Voucher List, Voucher Terjual.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT (PK)` | ID Transaksi/Voucher. |
| `user_id` | `BIGINT` | **(PENTING)** ID Mitra Pemilik Voucher. |
| `username` | `VARCHAR` | Kode Voucher / Username Hotspot. |
| `password` | `VARCHAR` | Password Hotspot. |
| `profile` | `VARCHAR` | Nama Profil Paket (Contoh: `1JAM`, `1BULAN`). |
| `price` | `DECIMAL` | Harga Jual Voucher. |
| `generated_at` | `DATETIME` | Waktu Voucher dibuat (dicetak). |
| `date_sold` | `DATE` | Tanggal Voucher terjual (Digunakan untuk laporan harian). |
| `first_login_at` | `DATETIME` | Waktu User pertama kali login (Aktif). |
| `batch_id` | `VARCHAR` | Kode Batch (Untuk kelompok generate massal). |
| `reseller_id` | `BIGINT` | ID Reseller (Jika dijual lewat reseller). |
| `template_id` | `BIGINT` | ID Template Desain Voucher yang digunakan. |
| `comment` | `TEXT` | Komentar Mikrotik. |

---

## 2. Tabel `voucher_templates`
Menyimpan desain visual (HTML/CSS) untuk cetak voucher.
**Fitur Terkait:** Menu Template Editor.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT (PK)` | ID Template. |
| `user_id` | `BIGINT` | ID Pemilik Template. |
| `name` | `VARCHAR` | Nama Template (Contoh: `Biru Keren`). |
| `html_content` | `TEXT` | Kode HTML Template. |
| `css_content` | `TEXT` | Kode CSS Template. |
| `image_url` | `VARCHAR` | Background Image URL (Opsional). |

---

## 3. Tabel `hotspot_profile_metadata`
Menyimpan data tambahan untuk Profil Hotspot yang tidak disimpan di Mikrotik (misal: Harga Dashboard).
**Fitur Terkait:** Menu Hotspot Profile.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT (PK)` | ID Data. |
| `user_id` | `BIGINT` | **(PENTING)** ID Pemilik Profil. |
| `profile_name` | `VARCHAR` | Nama Profil di Mikrotik (Kunci Relasi). |
| `alias` | `VARCHAR` | Nama Alias (Tampilan Cantik). |
| `price` | `DECIMAL` | Harga Modal/Jual di Laporan. |
| `selling_price` | `DECIMAL` | Harga Jual ke User (Opsional). |
| `validity` | `VARCHAR` | Masa Aktif (Format Human Readable: `30d`). |
| `limit_uptime` | `VARCHAR` | Limit Waktu Mikrotik. |
| `limit_bytes` | `VARCHAR` | Limit Kuota Mikrotik. |

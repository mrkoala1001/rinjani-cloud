# 4. Database: Pelanggan & Jaringan

Dokumen ini menjelaskan struktur tabel untuk manajemen Pelanggan dan Koneksi Alat (Router).

---

## 1. Tabel `customer_members` (Data Pelanggan)
Menyimpan data semua orang yang terhubung ke jaringan (Member, Rumah, Reseller).
**Fitur Terkait:** Menu Pelanggan (Customer).

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT (PK)` | ID Pelanggan. |
| `user_id` | `BIGINT` | **(PENTING)** ID Mitra Pemilik Pelanggan. |
| `type` | `ENUM` | Tipe: `MEMBER` (Hotspot), `PERUMAHAN` (PPPoE), `RESELLER`. |
| `name` | `VARCHAR` | Nama Lengkap. |
| `phone` | `VARCHAR` | Nomor HP / WhatsApp. |
| `address` | `TEXT` | Alamat Lengkap. |
| `location` | `VARCHAR` | Koordinat Lokasi (Lat, Long). |
| `notes` | `TEXT` | Catatan Tambahan. |
| `bill_amount` | `DECIMAL` | Tagihan Bulanan (Khusus PPPoE/Member). |
| `is_active` | `BOOLEAN` | Status Berlangganan. |
| `device_ip` | `VARCHAR` | IP Address Perangkat User. |
| `device_mac` | `VARCHAR` | MAC Address Perangkat User. |

---

## 2. Tabel `mikrotik_configs` (Alat / Router)
Menyimpan kredensial untuk login ke Router Mikrotik via API.
**Fitur Terkait:** Dashboard -> Add Router / Settings.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT (PK)` | ID Konfigurasi. |
| `user_id` | `BIGINT` | **(PENTING)** ID Mitra Pemilik Router. |
| `host` | `VARCHAR` | IP Address Router / Domain DDNS. |
| `port` | `INTEGER` | Port API (Default: 8728). |
| `user` | `VARCHAR` | Username Login Mikrotik. |
| `pass` | `VARCHAR` | Password Login Mikrotik (Disimpan Plaintext/Encrypted tergantung setting). |
| `sitename` | `VARCHAR` | Nama Lokasi / Router (Label). |
| `dns_name` | `VARCHAR` | Nama DNS Hotspot (ex: `hotspot.net`). |
| `currency` | `VARCHAR` | Mata Uang (Rp, $, dll). |
| `idle_timeout` | `INTEGER` | Timeout sesi admin ke router. |

# 📩 SISTEM LAPORAN MR. KOALA

### Tujuan
Memberikan jalur komunikasi langsung antara pengguna (Owner/ISP) dengan Pembuat Aplikasi (Builder).

### Struktur Database:
Tabel `reports` menyimpan data-data berikut:
- `user_id`: ID user yang melapor.
- `sender_name`: Nama tampilan pengirim.
- `sender_role`: Peran pengirim (owner/isp/builder).
- `subject`: Judul laporan.
- `message`: Isi pesan.
- `status`: `unread`, `read`, atau `archived`.

### Alur Kerja (Workflow):
1.  **Pengiriman**: User klik tombol "Report to Mr. Koala" -> Mengisi Form -> Disimpan ke tabel `reports`.
2.  **Notifikasi**: Builder melihat badge angka merah di menu "Reports Inbox" jika ada laporan berstatus `unread`.
3.  **Manajemen**: Builder membaca detail laporan, dan status otomatis berubah menjadi `read`.

### Tampilan:
Didesain menyerupai aplikasi Email Modern (Gmail/Apple Mail style) untuk memberikan kesan profesional dan bersih.

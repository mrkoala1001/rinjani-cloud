# 🐨 KOALA BUILDER DASHBOARD

### Apa itu Builder Mode?
Builder Mode adalah tingkatan akses tertinggi (di atas Superduper Admin) yang dikhususkan untuk **Pembuat / Developer Utama (Mr. Koala)**.

### Fitur Utama Dashboard Builder:
1.  **Server Monitoring**: Melihat beban CPU, Uptime, dan penggunaan disk secara realtime.
2.  **Database Summary**: Rekap jumlah User, ISP, Owner, dan total transaksi voucher secara global.
3.  **ISP Management**: Tombol cepat untuk membuat akun ISP (Superduper Admin) baru tanpa lewat pendaftaran publik.
4.  **Reports Inbox**: Pusat kendali untuk membaca semua laporan/pesan yang masuk dari semua pengguna aplikasi.

### Cara Kerja:
Sistem mendeteksi role `builder` pada tabel `users`. Saat user dengan role ini login, middleware akan otomatis mengarahkan ke `/builder` dan menampilkan dashboard yang didesain dengan tema gelap (dark mode) premium.

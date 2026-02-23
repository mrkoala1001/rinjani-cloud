# 📧 INTEGRASI EMAIL REPORT (PROPOSAL)

### Cara Kerja Integrasi Email
Sistem dapat dikonfigurasi agar setiap ada laporan masuk, Mr. Koala langsung mendapatkan pemberitahuan di email pribadinya.

### Komponen Teknis:
1.  **SMTP (Simple Mail Transfer Protocol)**: Protokol untuk mengirim email. Kita bisa menggunakan Gmail SMTP atau Mailtrap.
2.  **Laravel Mail (Mailable)**: Class khusus di Laravel untuk mengatur template dan pengiriman email.
3.  **Queue System (Optional)**: Pengiriman email bisa dilakukan di background (antrian) agar user tidak perlu menunggu proses loading saat mengirim laporan.

### Persiapan Konfigurasi (.env):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=email-pengirim@gmail.com
MAIL_PASSWORD=app-password-khusus-aplikasi
MAIL_ENCRYPTION=tls
```

### Keunggulan:
- Mr. Koala tidak perlu standby di dashboard 24/7.
- Notifikasi langsung muncul di handphone.
- Memiliki arsip pesan selain di database aplikasi.

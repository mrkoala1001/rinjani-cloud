# Dokumentasi AI Blog Generator (CrewAI & Gemini)

Sistem ini menggunakan CrewAI dan Google Gemini untuk mengotomatisasi pembuatan artikel blog yang berkualitas tinggi dan relevan.

## Fitur Utama
- **Multi-Agent Research**: Agen khusus mencari tren dan fakta terbaru sebelum menulis.
- **Bahasa Indonesia**: Konten dihasilkan secara profesional dalam Bahasa Indonesia.
- **Optimasi SEO**: Agen editor memastikan konten ramah mesin pencari.
- **Integrasi Laravel**: Terhubung langsung ke database `blogs_blog`.

## Cara Menjalankan
Gunakan perintah terminal berikut:
```bash
php artisan blog:auto-generate "Judul Artikel Anda"
```

### Jalur Pintas (Shortcut)
Anda bisa menjalankan perintah ini dari folder mana pun dengan ketik:
```bash
agent-koala "Judul Artikel Anda"
```

### Gambar Otomatis (AI Image)
Sistem sekarang secara otomatis membuat gambar unik untuk setiap artikel:
1. AI (Visual Designer) membuat deskripsi gambar (prompt) berdasarkan isi artikel.
2. Sistem mengambil gambar dari layanan AI gratis (Pollinations.ai).
3. Gambar disimpan di folder `storage/app/public/blog_images/` dan dipasang sebagai gambar utama.

## Jadwal Otomatis (Cron Job)
Artikel dihasilkan secara otomatis dua kali sehari:
- **03:00 Pagi**: Riset dan posting artikel pertama.
- **14:00 Siang**: Riset dan posting artikel kedua.
- Konfigurasi ini ada di file: `routes/console.php`.

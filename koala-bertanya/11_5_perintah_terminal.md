# 💻 11. 5 Perintah Utama Terminal

Untuk menjalankan project MIKHMON (Laravel Edition) ini secara sempurna dengan fitur sinkronisasi otomatis dan dashboard realtime, ada **5 perintah** yang harus berjalan di terminal:

### 🚀 Cara Cepat: Perintah Gabungan (Satu Terminal)
Anda bisa menjalankan **semua 5 perintah di atas sekaligus** dalam satu tab terminal saja dengan perintah:
```bash
composer dev
```
*Ini akan membuka 5 proses secara paralel (server, queue, vite, reverb, dan scheduler) dengan warna yang berbeda-beda agar mudah dipantau.*

---

### 1. Web Server
```bash
php artisan serve
```
*Tujuan: Menjalankan aplikasi agar bisa diakses di browser (`http://localhost:8000`).*

### 2. Frontend Assets (Vite)
```bash
npm run dev
```
*Tujuan: Memproses tampilan CSS (Tailwind) dan JavaScript secara realtime.*

### 3. Queue Worker (Latar Belakang)
```bash
php artisan queue:listen
```
*Tujuan: Menjalankan proses antrean di belakang layar, sangat penting untuk sinkronisasi data MikroTik.*

### 4. WebSocket (Realtime Data)
```bash
php artisan reverb:start
```
*Tujuan: Mengaktifkan fitur update data tanpa refresh di dashboard.*

### 5. Task Scheduler (Penjadwal)
```bash
php artisan schedule:work
```
*Tujuan: Menjalankan tugas rutin seperti sinkronisasi tiap 10 detik secara otomatis.*

---

### 💡 Tips Cepat
Anda bisa menjalankan beberapa perintah sekaligus (kecuali Reverb & Scheduler) dalam satu terminal menggunakan perintah:
```bash
composer dev
```

> [!IMPORTANT]
> Pastikan file `.env` sudah terisi dengan konfigurasi Database dan IP MikroTik yang benar sebelum menjalankan perintah-perintah ini.

# 6. Cara Membuat Akun Superduper Admin (ISP)

**Pertanyaan:**
> Kalau Superduper admin (ISP) bisa membuat account untuk OWNER.
> Lalu saya Mr.Koala (builder) bisa gak buatin superduper admin account?

**Jawaban: BISA BANGET!**

Sebagai "Builder" (Developer/SysAdmin), Anda memegang kendali penuh atas database. Ada 2 cara untuk membuat akun Superduper Admin (ISP):

### Cara 1: Menggunakan "Kunci Penerobos" (Seeder)
Saya sudah menyiapkan skript otomatis ("Seeder") untuk membuat akun admin default.

1.  Buka Terminal.
2.  Jalankan perintah ini:
    ```bash
    php artisan db:seed
    ```
3.  **SELESAI.**
    Ini akan otomatis membuat akun:
    -   **Email:** `admin@isp.com`
    -   **Password:** `12345678`
    -   **Role:** `isp` (Superduper Admin)

### Cara 2: Manual via Database (Tinker)
Jika Anda ingin membuat akun dengan nama khusus tanpa mereset database lain:

1.  Buka Terminal.
2.  Masuk ke mode interaktif:
    ```bash
    php artisan tinker
    ```
3.  Ketik perintah ini (Baris per baris):
    ```php
    User::create([
        'name' => 'Mr. Koala',
        'email' => 'koala@builder.com',
        'password' => bcrypt('passwordrahasia'),
        'role' => 'isp',
        'is_active' => true,
    ]);
    ```
4.  Tekan Enter. Akun `koala@builder.com` sekarang sudah jadi Superduper Admin.

---
**Penting:**
Posisi "Builder" itu di atas "ISP". Anda yang menciptakan langit dan bumi (server dan aplikasi), jadi Anda bebas menciptakan siapa saja, termasuk Superduper Admin.

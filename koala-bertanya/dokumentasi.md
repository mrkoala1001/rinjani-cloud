# 📄 DOKUMENTASI SISTEM HOT POT
> **STATUS:** OFFICIAL RELEASE V1.0  
> **COPYRIGHT:** © 2026 DEPOOTCOM - BUILDER MR. KOALA  
> **CONFIDENTIALITY:** INTERNAL & OWNER ACCESS ONLY

---

## 🐨 PENGANTAR (INTRODUCTION)

**HOT POT** adalah platform manajemen ISP dan Hotspot terintegrasi yang dirancang oleh **DEPOOTCOM** untuk menghadirkan solusi profesional bagi penyedia layanan internet. Sistem ini mengkombinasikan kecepatan performa, estetika modern, dan fungsionalitas tingkat tinggi dalam satu ekosistem yang solid.

Dokumentasi ini disusun untuk memberikan panduan komprehensif bagi para *Owner* dan administrator dalam mengoperasikan fitur-fitur mutakhir yang telah diimplementasikan oleh **Builder Mr. Koala**.

---

## 🚀 FITUR UTAMA SISTEM (CORE FEATURES)

### 1. Landing Page Premium & Interaktif
Halaman depan bukan sekadar wajah, melainkan representasi teknologi tingkat tinggi.
*   **Aesthetics Driven Design**: Menggunakan skema warna *Rose-Brand* yang elegan dengan font *Outfit* (Google Fonts) untuk keterbacaan premium.
*   **Interactive Visuals**:
    *   **Canvas Network Animation**: Animasi partikel yang merespons pergerakan *mouse* (repulsion) dan klik (ripple), mensimulasikan konektivitas jaringan secara *real-time*.
    *   **AOS (Animate On Scroll)**: Transisi mulus antar bagian saat pengguna menjelajahi halaman.
*   **MikroTik Terminal Simulator**: Widget di pojok kanan bawah yang mensimulasikan sesi terminal MikroTik (RouterOS), memberikan kesan teknis yang kuat dan profesional.
*   **Call to Action (CTA)**: Integrasi langsung ke WhatsApp Support dan sistem Login Member.

### 2. HOT SUPPORT: Advanced Ticketing System
Sistem manajemen keluhan dua arah yang menggantikan pelaporan tradisional.
*   **Unique Ticket Identifier**: Setiap laporan secara otomatis diberikan ID unik (misal: `#TICKET-E4B2A`) dengan metadata waktu pembuatan.
*   **Workflow Terstruktur**:
    *   **Dashboard My Tickets**: Memantau status seluruh tiket (Pending, Processed, Resolved, Closed).
    *   **Two-Way Interaction**: Kemampuan admin untuk membalas tiket (*reply*) dengan instruksi teknis dan owner untuk memberikan umpan balik.
    *   **Status Management**: Perubahan status tiket memberikan kejelasan pada progress perbaikan.

### 3. Integrated Notification Center
Sistem pemberitahuan terpusat yang terletak di ikon lonceng pada *navbar* utama.
*   **Real-time Updates**: Notifikasi muncul seketika saat admin memberikan balasan pada tiket yang dibuat.
*   **Broadcast Messaging**: Pesan pengumuman global yang dapat dikirim oleh Builder untuk menginformasikan jadwal pemeliharaan (*maintenance*) atau fitur baru.
*   **Read/Unread Tracking**: Menandai notifikasi secara otomatis saat pengguna melihat detail tiket.

### 4. Builder Dashboard (Administrative Level)
Pusat kontrol bagi admin tingkat tinggi untuk mengelola ekosistem secara keseluruhan.
*   **Management ISP/Mitra**: Membuat dan mengawasi akun-akun owner di bawah naungan sistem.
*   **Broadcast Command**: Antarmuka khusus untuk mengirimkan pesan ke seluruh pengguna dalam satu kali klik.
*   **Support Center**: Manajemen seluruh tiket yang masuk dari berbagai owner secara efisien.

### 5. Security & Data Protection (Danger Zone)
Fitur perlindungan privasi tingkat lanjut bagi para *Owner*.
*   **Putuskan Jaringan (Disconnect)**: Menghapus konfigurasi akses MikroTik dari sistem seketika untuk keamanan.
*   **Bersihkan Seluruh Data (Full Wipe)**: Fitur "Penghancuran Data" yang menghapus secara permanen seluruh riwayat transaksi, data pelanggan, dan profil dari database, memastikan privasi total saat tidak lagi menggunakan layanan.
*   **Tenant Isolation**: Arsitektur database yang menggunakan `TenantScope` untuk menjamin data satu owner tidak pernah terlihat oleh owner lainnya.

---

## 🛠️ PANDUAN PENGOPERASIAN (USER GUIDES)

### A. Alur Pelaporan Kendala (Ticketing)
1.  **Akses Menu**: Navigasi ke menu **Report** atau **My Tickets** di dashboard samping.
2.  **Submit Laporan**: Klik **Create New Ticket**, isi subjek pendek namun jelas, dan deskripsikan kendala teknis secara detail. Pilih tingkat prioritas (Low/Medium/High).
3.  **Pemantauan**: Gunakan ikon lonceng untuk mengecek apakah ada balasan dari tim support.
4.  **Penyelesaian**: Setelah kendala teratasi, admin akan mengubah status menjadi **Resolved**.

### B. Membaca Statistik Dashboard
*   **Income Summary**: Memantau pendapatan harian dan bulanan hasil penjualan voucher.
*   **Active Users**: Melihat jumlah sesi aktif yang terhubung ke RouterBoard secara *real-time*.
*   **Network Performance**: Memantau trafik *upload* dan *download* secara visual untuk memastikan kesehatan jaringan.

---

## 📝 CATATAN TEKNIS & KEAMANAN

| Komponen | Teknologi / Standar |
| :--- | :--- |
| **Framework** | Laravel 10 (PHP 8.x) |
| **Front-end** | Tailwind CSS & Alpine.js |
| **Animation** | AOS, Custom Canvas JS |
| **Database** | MySQL (Eloquent ORM) |
| **Icons** | Font Awesome 6 |

> [!IMPORTANT]
> Sistem ini diproteksi oleh protokol keamanan berlapis termasuk *impersonation protection* dan *role-based access control* (RBAC).

---

## 🐨 COPYRIGHT & WATERMARK
**Sistem ini dibangun dan dikembangkan dengan dedikasi tinggi oleh:**

```text
  __  __      _  __           _       
 |  \/  |    | |/ /          | |      
 | \  / |_ __| ' / ___   __ _| | __ _ 
 | |\/| | '__|  < / _ \ / _` | |/ _` |
 | |  | | |  | . \ (_) | (_| | | (_| |
 |_|  |_|_|  |_|\_\___/ \__,_|_|\__,_|
                                      
      DEPOOTCOM - QUALITY SERVICE
```

*Seluruh hak cipta dilindungi undang-undang. Penggunaan atau penggandaan kode tanpa izin tertulis dari **Mr. Koala (DEPOOTCOM)** dilarang keras.*

---
*Dibuat pada: {{ date('d F Y') }}*  
*Oleh: Builder Mr. Koala*

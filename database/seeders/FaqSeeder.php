<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Apa itu HOT POT?',
                'answer' => 'HOT POT adalah platform manajemen hotspot dan ISP yang dirancang untuk mempermudah pengelolaan jaringan Anda. Dengan fitur lengkap mulai dari manajemen user, billing, hingga laporan keuangan, HOT POT membantu Anda fokus mengembangkan bisnis tanpa pusing memikirkan teknis.',
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara mendaftar menjadi mitra?',
                'answer' => 'Sangat mudah! Anda cukup klik tombol "Daftar Sekarang" atau hubungi kami via WhatsApp. Tim kami akan memandu Anda melalui proses registrasi dan setup awal hingga sistem siap digunakan.',
                'is_active' => true,
            ],
            [
                'question' => 'Apakah saya perlu keahlian coding?',
                'answer' => 'Sama sekali tidak. HOT POT didesain dengan antarmuka yang ramah pengguna (user-friendly). Semua konfigurasi dapat dilakukan melalui dashboard grafis tanpa perlu menyentuh baris kode.',
                'is_active' => true,
            ],
            [
                'question' => 'Perangkat apa saja yang didukung?',
                'answer' => 'HOT POT mendukung berbagai jenis router MikroTik. Kami merekomendasikan penggunaan RouterOS versi terbaru untuk kinerja yang optimal. Jika Anda ragu mengenai kompatibilitas perangkat Anda, silakan konsultasikan dengan tim support kami.',
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana jika saya mengalami kendala teknis?',
                'answer' => 'Jangan khawatir! Kami menyediakan layanan support 24/7. Anda bisa membuat tiket bantuan langsung melalui dashboard atau menghubungi kami via WhatsApp untuk respon cepat. Tim teknis kami siap membantu menyelesaikan masalah Anda.',
                'is_active' => true,
            ],
            [
                'question' => 'Berapa biaya berlangganan HOT POT?',
                'answer' => 'Kami menawarkan berbagai paket berlangganan yang fleksibel sesuai dengan skala bisnis Anda, mulai dari paket pemula hingga enterprise. Silakan hubungi tim sales kami untuk mendapatkan penawaran terbaik.',
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}

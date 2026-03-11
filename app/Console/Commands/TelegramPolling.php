<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramPolling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:polling {--timeout=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll Telegram updates (for development/testing)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeout = $this->option('timeout');
        $lastUpdate = 0;

        $this->info('🤖 Telegram Bot Polling Started...');
        $this->info('Press Ctrl+C to stop');
        $this->newLine();

        while (true) {
            try {
                // Get updates
                $updates = Telegram::getUpdates([
                    'offset' => $lastUpdate + 1,
                    'timeout' => $timeout,
                ]);

                foreach ($updates as $update) {
                    $lastUpdate = $update->getUpdateId();

                    if ($update->isType('message')) {
                        $message = $update->getMessage();
                        $chatId = $message->getChat()->getId();
                        $text = $message->getText();
                        $firstName = $message->getChat()->getFirstName();

                        $this->info("📨 Message from $firstName: $text");

                        // Handle commands
                        if ($text === '/start') {
                            $this->sendStartMessage($chatId);
                        } elseif ($text === '/help') {
                            $this->sendHelpMessage($chatId);
                        } elseif ($text === '/status') {
                            $this->sendStatusMessage($chatId);
                        } elseif (!empty($text) && !str_starts_with($text, '/')) {
                            // Generate article
                            $this->info("⏳ Generating article for: $text");
                            $this->handleArticleGeneration($chatId, $text);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Telegram polling error: ' . $e->getMessage());
                $this->error('Error: ' . $e->getMessage());
                sleep(5); // Retry after 5 seconds
            }
        }
    }

    /**
     * Send start message
     */
    private function sendStartMessage($chatId)
    {
        $text = "🤖 *Selamat datang di Agent-Koala Bot!*\n\n" .
                "Saya adalah bot AI yang siap membantu membuat artikel blog otomatis.\n\n" .
                "Cara penggunaan:\n" .
                "1. Ketik judul artikel yang ingin dibuat\n" .
                "2. Bot akan menghasilkan artikel lengkap + gambar\n" .
                "3. Hasil akan tersimpan di blog depootcom.site\n\n" .
                "Gunakan /help untuk instruksi lebih detail.";

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);

        $this->info("✅ Start message sent");
    }

    /**
     * Send help message
     */
    private function sendHelpMessage($chatId)
    {
        $text = "📖 *Panduan Penggunaan*\n\n" .
                "*Command:*\n" .
                "/start - Tampilkan pesan sambutan\n" .
                "/help - Tampilkan panduan ini\n" .
                "/status - Cek status sistem\n\n" .
                "*Cara Membuat Artikel:*\n" .
                "Cukup kirim teks dengan judul artikel. Bot akan:\n" .
                "✅ Melakukan riset tentang topik\n" .
                "✅ Menulis artikel profesional dalam Bahasa Indonesia\n" .
                "✅ Optimalkan SEO\n" .
                "✅ Buat gambar AI\n" .
                "✅ Simpan ke database\n\n" .
                "*Contoh:*\n" .
                "Keamanan Cyber di Era 2026";

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);

        $this->info("✅ Help message sent");
    }

    /**
     * Send status message
     */
    private function sendStatusMessage($chatId)
    {
        $text = "✅ *Sistem Status*\n\n" .
                "Agent-Koala: *Online*\n" .
                "Database: *Connected*\n" .
                "AI Model: *Siap digunakan*\n\n" .
                "Silakan kirim judul artikel untuk mulai.";

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);

        $this->info("✅ Status message sent");
    }

    /**
     * Handle article generation
     */
    private function handleArticleGeneration($chatId, $topic)
    {
        try {
            // Send "processing" message
            $processingMsg = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "⏳ *Sedang membuat artikel...* \n\nTopik: *$topic*\n\nProses ini membutuhkan 2-5 menit.\nMohon tunggu...",
                'parse_mode' => 'Markdown',
            ]);

            $messageId = $processingMsg->getMessageId();

            // Run the artisan command
            $exitCode = Artisan::call('blog:auto-generate', ['topic' => $topic]);
            $output = Artisan::output();

            $this->info("📝 Article generated. Exit code: $exitCode");

            // Parse image URL from output
            $imageUrl = '';
            if (preg_match('/---IMAGE_URL_START---(.*?)---IMAGE_URL_END---/s', $output, $matches)) {
                $imageUrl = trim($matches[1]);
            }

            // Check if successful
            if ($exitCode === 0 || strpos($output, 'Error') === false) {
                $successText = "✅ *Artikel Berhasil Dibuat!*\n\n" .
                               "*Judul:* " . ucfirst($topic) . "\n\n" .
                               "Artikel telah disimpan dan akan muncul di blog depootcom.site";

                Telegram::editMessageText([
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'text' => $successText,
                    'parse_mode' => 'Markdown',
                ]);

                // Send article link as button
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Lihat artikel:",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [[
                            ['text' => '📰 https://depootcom.site/blog', 'url' => 'https://depootcom.site/blog']
                        ]]
                    ]),
                ]);

                // Send generated image if available
                if (!empty($imageUrl)) {
                    try {
                        Telegram::sendPhoto([
                            'chat_id' => $chatId,
                            'photo' => $imageUrl,
                            'caption' => '🎨 *Gambar AI untuk artikel ini*',
                            'parse_mode' => 'Markdown',
                        ]);
                        $this->info("✅ Image sent to Telegram");
                    } catch (\Exception $e) {
                        $this->warn("Failed to send image: " . $e->getMessage());
                    }
                }

                $this->info("✅ Article success message sent");
            } else {
                // Error occurred
                Telegram::editMessageText([
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'text' => "❌ *Gagal membuat artikel* \n\nCoba ulangi atau hubungi admin.",
                    'parse_mode' => 'Markdown',
                ]);

                $this->error("❌ Article generation failed");
            }
        } catch (\Exception $e) {
            Log::error('Article generation error: ' . $e->getMessage());
            $this->error('Error: ' . $e->getMessage());

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ *Error:* " . $e->getMessage(),
                'parse_mode' => 'Markdown',
            ]);
        }
    }
}

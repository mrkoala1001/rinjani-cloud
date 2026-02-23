<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    /**
     * Handle incoming webhook from Telegram
     */
    public function handleWebhook(Request $request)
    {
        try {
            $update = Telegram::commandsHandler(true);
            
            if ($update->isType('message')) {
                $message = $update->getMessage();
                $chatId = $message->getChat()->getId();
                $text = $message->getText();

                // Command: /start
                if ($text === '/start') {
                    $this->sendStartMessage($chatId);
                    return response('OK', 200);
                }

                // Command: /help
                if ($text === '/help') {
                    $this->sendHelpMessage($chatId);
                    return response('OK', 200);
                }

                // Command: /status
                if ($text === '/status') {
                    $this->sendStatusMessage($chatId);
                    return response('OK', 200);
                }

                // Regular message = article topic (auto-generate)
                if (!empty($text) && !$text->startsWith('/')) {
                    $this->handleArticleGeneration($chatId, $text);
                    return response('OK', 200);
                }
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage());
            return response('Error: ' . $e->getMessage(), 500);
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
                "3. Hasil akan tersimpan di blog depootcom.com\n\n" .
                "Gunakan /help untuk instruksi lebih detail.";

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
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
            Artisan::call('blog:auto-generate', ['topic' => $topic]);
            $output = Artisan::output();

            // Parse output to get article details
            $result = $this->parseArtisanOutput($output);

            if ($result['success']) {
                // Update the processing message
                $successText = "✅ *Artikel Berhasil Dibuat!*\n\n" .
                               "*Judul:* " . $result['title'] . "\n" .
                               "*URL:* " . $result['url'] . "\n\n" .
                               "Artikel telah disimpan dan akan muncul di blog depootcom.com";

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
                            ['text' => '📰 Baca Artikel', 'url' => $result['url']]
                        ]]
                    ]),
                ]);

                // Send generated image if available
                if (!empty($result['image_url'])) {
                    try {
                        Telegram::sendPhoto([
                            'chat_id' => $chatId,
                            'photo' => $result['image_url'],
                            'caption' => '🎨 *Gambar AI untuk artikel ini*',
                            'parse_mode' => 'Markdown',
                        ]);
                    } catch (\Exception $e) {
                        Log::warn('Failed to send image: ' . $e->getMessage());
                    }
                }
            } else {
                // Error occurred
                Telegram::editMessageText([
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'text' => "❌ *Gagal membuat artikel*\n\nError: " . $result['error'],
                    'parse_mode' => 'Markdown',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Article generation error: ' . $e->getMessage());

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ *Error:* " . $e->getMessage(),
                'parse_mode' => 'Markdown',
            ]);
        }
    }

    /**
     * Parse artisan command output
     */
    private function parseArtisanOutput($output)
    {
        // Check for success markers in output
        if (strpos($output, 'successfully') !== false || strpos($output, 'created') !== false) {
            // Extract image URL if available
            $imageUrl = '';
            if (preg_match('/---IMAGE_URL_START---(.*?)---IMAGE_URL_END---/s', $output, $matches)) {
                $imageUrl = trim($matches[1]);
            }
            
            return [
                'success' => true,
                'title' => 'Artikel Baru',
                'url' => 'https://depootcom.com/blog',
                'image_url' => $imageUrl,
            ];
        }

        return [
            'success' => false,
            'error' => 'Gagal membuat artikel. Cek log untuk detail.',
            'image_url' => '',
        ];
    }
}

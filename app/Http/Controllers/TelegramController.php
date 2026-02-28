<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class TelegramController extends Controller
{
    protected $token;
    protected $apiUrl;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    public function handle(Request $request)
    {
        $update = $request->all();
        Log::info('Telegram Update Received', $update);

        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->handleCallback($update['callback_query']);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $allowedUsers = explode(',', env('TELEGRAM_ALLOWED_USERS', ''));

        // If not allowed, just tell the user their ID and log it
        if (!in_array($chatId, $allowedUsers)) {
            $this->sendMessage($chatId, "⚠️ Akses Ditolak.\n\nID Telegram Anda: `{$chatId}`\nSilakan tambahkan ID ini ke `TELEGRAM_ALLOWED_USERS` di file .env");
            Log::warning("Unauthorized Telegram access attempt from ID: {$chatId}");
            return;
        }

        if ($text == '/start' || strtolower($text) == 'menu') {
            $this->sendMenu($chatId);
        } else {
            // Handle as AI Question
            $this->handleAIQuestion($chatId, $text);
        }
    }

    protected function handleCallback($callback)
    {
        $chatId = $callback['message']['chat']['id'];
        $data = $callback['data'];
        $callbackId = $callback['id'];
        $allowedUsers = explode(',', env('TELEGRAM_ALLOWED_USERS', ''));

        if (!in_array($chatId, $allowedUsers)) {
            $this->answerCallback($callbackId, "Akses dilarang!");
            return;
        }

        switch ($data) {
            case 'stats':
                $this->answerCallback($callbackId, "Mengambil data...");
                $this->sendStats($chatId);
                break;
            case 'services_menu':
                $this->answerCallback($callbackId, "Menu Servis");
                $this->sendServicesMenu($chatId);
                break;
            case 'top_proc':
                $this->answerCallback($callbackId, "Menganalisa proses...");
                $this->sendTopProcesses($chatId);
                break;
            case 'restart_php':
                $this->answerCallback($callbackId, "Restarting PHP...");
                $this->executeCommand($chatId, "sudo systemctl restart php*-fpm", "PHP-FPM berhasil di-restart.");
                break;
            case 'restart_mysql':
                $this->answerCallback($callbackId, "Restarting MySQL...");
                $this->executeCommand($chatId, "sudo systemctl restart mysql", "MySQL berhasil di-restart.");
                break;
            case 'restart_nginx':
                $this->answerCallback($callbackId, "Restarting Nginx...");
                $this->executeCommand($chatId, "sudo systemctl restart nginx", "Nginx berhasil di-restart.");
                break;
            case 'restart_radius':
                $this->answerCallback($callbackId, "Restarting FreeRADIUS...");
                $this->executeCommand($chatId, "sudo systemctl restart freeradius", "FreeRADIUS berhasil di-restart.");
                break;
            case 'clear_cache':
                $this->answerCallback($callbackId, "Cleaning cache...");
                $this->executeCommand($chatId, "sync; echo 3 | sudo tee /proc/sys/vm/drop_caches", "Cache RAM berhasil dibersihkan.");
                break;
            case 'back_to_menu':
                $this->answerCallback($callbackId, "Kembali");
                $this->sendMenu($chatId, true, $callback['message']['message_id']);
                break;
        }
    }

    protected function sendMenu($chatId, $edit = false, $messageId = null)
    {
        $text = "🐧 *Asisten Server HP* (HotPot)\nSelalu siap melayani Juragan. Silakan pilih menu:";
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📊 Status Server', 'callback_data' => 'stats'],
                    ['text' => '🔍 Top Proses', 'callback_data' => 'top_proc']
                ],
                [
                    ['text' => '🛠️ Kelola Servis', 'callback_data' => 'services_menu'],
                    ['text' => '🧹 Bersih Cache', 'callback_data' => 'clear_cache']
                ]
            ]
        ];

        if ($edit && $messageId) {
            $this->editMessage($chatId, $messageId, $text, $keyboard);
        } else {
            $this->sendMessage($chatId, $text, $keyboard);
        }
    }

    protected function sendServicesMenu($chatId)
    {
        $text = "🛠️ *Kelola Layanan Server*\n\nHati-hati dalam melakukan restart layanan kritis.";
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🐘 Restart PHP', 'callback_data' => 'restart_php'],
                    ['text' => '🐬 Restart MySQL', 'callback_data' => 'restart_mysql']
                ],
                [
                    ['text' => '🌐 Restart Nginx', 'callback_data' => 'restart_nginx'],
                    ['text' => '📻 Restart Radius', 'callback_data' => 'restart_radius']
                ],
                [
                    ['text' => '⬅️ Kembali', 'callback_data' => 'back_to_menu']
                ]
            ]
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    protected function sendStats($chatId)
    {
        $load = sys_getloadavg();
        $uptime = shell_exec('uptime -p');
        
        // RAM usage
        $free = shell_exec('free -m');
        $freeArr = explode("\n", trim($free));
        $mem = preg_split('/\s+/', $freeArr[1]);
        $ramUsed = $mem[2];
        $ramTotal = $mem[1];
        $ramPercent = round(($ramUsed / $ramTotal) * 100, 2);

        // Disk usage
        $disk = disk_free_space("/");
        $total = disk_total_space("/");
        $usedPercent = round((($total - $disk) / $total) * 100, 2);

        // Radius Sessions & Vouchers
        $activeSessions = 0;
        $totalVouchers = 0;
        try {
            $activeSessions = DB::table('radacct')->whereNull('acctstoptime')->count();
            $totalVouchers = DB::table('radcheck')->count();
        } catch (\Exception $e) {}

        // Service Status
        $nginxStatus = trim(shell_exec('systemctl is-active nginx') ?: 'unknown');
        $mysqlStatus = trim(shell_exec('systemctl is-active mysql') ?: 'unknown');
        $radiusStatus = trim(shell_exec('systemctl is-active freeradius') ?: 'unknown');
        
        $statusStr = ($nginxStatus == 'active' && $mysqlStatus == 'active' && $radiusStatus == 'active') ? "✅ Semua Normal" : "⚠️ Cek Layanan";

        $text = "📊 *STATUS SERVER*\n\n" .
               "🌡️ *Load:* {$load[0]}, {$load[1]}, {$load[2]}\n" .
               "🧠 *RAM:* {$ramUsed}MB / {$ramTotal}MB ({$ramPercent}%)\n" .
               "💾 *Disk:* {$usedPercent}% Used\n" .
               "🕒 *Uptime:* " . trim($uptime) . "\n\n" .
               "🌐 *Nginx:* " . ($nginxStatus == 'active' ? '🟢' : '🔴') . " {$nginxStatus}\n" .
               "🐬 *MySQL:* " . ($mysqlStatus == 'active' ? '🟢' : '🔴') . " {$mysqlStatus}\n" .
                "📻 *Radius:* " . ($radiusStatus == 'active' ? '🟢' : '🔴') . " {$radiusStatus}\n" .
               "👥 *Sesi Aktif:* {$activeSessions} user\n" .
               "🎟️ *Total Voucher:* {$totalVouchers}\n\n" .
               "📝 *Status:* {$statusStr}";

        $this->sendMessage($chatId, $text);
    }

    protected function sendTopProcesses($chatId)
    {
        $top = shell_exec('ps aux --sort=-%cpu | head -n 6');
        $text = "🔍 *TOP 5 PROSES (CPU)*\n\n```\n{$top}\n```";
        $this->sendMessage($chatId, $text);
    }

    protected function executeCommand($chatId, $command, $successMsg)
    {
        $output = shell_exec($command . " 2>&1");
        $text = $successMsg . "\n\nOutput:\n`" . ($output ?: 'Success (No output)') . "`";
        $this->sendMessage($chatId, $text);
    }

    protected function sendMessage($chatId, $text, $keyboard = null)
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }

        return Http::post("{$this->apiUrl}/sendMessage", $data);
    }

    protected function editMessage($chatId, $messageId, $text, $keyboard = null)
    {
        $data = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }

        return Http::post("{$this->apiUrl}/editMessageText", $data);
    }

    protected function handleAIQuestion($chatId, $question)
    {
        Log::info('AI Question Started: ' . $question);
        $this->sendMessage($chatId, "� Sedang mengamati server...");

        $intel = $this->getIntelligenceContext();

        $context = "=== DATA INTELIJEN SERVER ===\n" .
                  "- Uptime: {$intel['uptime']}\n" .
                  "- Load: " . implode(', ', $intel['load']) . "\n" .
                  "- Services: [Nginx: {$intel['services']['nginx']}, MySQL: {$intel['services']['mysql']}, PHP: {$intel['services']['php']}, Radius: {$intel['services']['radius']}]\n" .
                  "- Statistics: [Active Radius Sessions: {$intel['metrics']['active_sessions']}, Total Vouchers: {$intel['metrics']['total_vouchers']}]\n" .
                  "- Git Info: [Branch: {$intel['git']['branch']}, Last Commit: {$intel['git']['commit']}]\n" .
                  "\n=== RAM STATUS ===\n{$intel['ram']}\n" .
                  "\n=== DISK STATUS ===\n{$intel['disk']}\n" .
                  "\n=== TOP PROCESSES ===\n{$intel['top']}\n" .
                  "\n=== RECENT ERROR LOGS (Laravel) ===\n" . ($intel['logs']['laravel'] ?: 'No recent errors.') . "\n" .
                  "\n=== RECENT RADIUS LOGS ===\n" . ($intel['logs']['radius'] ?: 'No recent radius logs.') . "\n";

        $prompt = "Anda adalah Agent Koala, asisten AI tingkat tinggi untuk server HotPot milik Juragan.\n\n" .
                 "IDENTITAS & GAYA:\n" .
                 "- Anda bukan chatbot biasa, Anda adalah 'Otak' dari server ini.\n" .
                 "- Gaya bicara ramah, santai (panggil 'Juragan'), tapi sangat teknis dan presisi.\n" .
                 "- Gunakan emoji yang relevan.\n\n" .
                 "KEMAMPUAN ANDA:\n" .
                 "1. Analisa Data: Anda bisa membaca logs, status RAM, Disk, dan proses yang membebankan CPU.\n" .
                 "2. Troubleshooting: Jika ada error di logs, jelaskan kemungkinannya dan cara memperbaikinya.\n" .
                 "3. Wawasan Bisnis: Anda tahu berapa banyak user yang online (Radius Sessions).\n" .
                 "4. Pengingat: Beritahu Juragan jika RAM hampir penuh (>85%) atau Disk hampir penuh (>90%).\n" .
                 "5. Navigasi: Ingatkan ada menu tombol untuk tindakan cepat (Restart, Clear Cache).\n\n" .
                 "DATA SERVER SAAT INI:\n{$context}\n\n" .
                 "PERTANYAAN JURAGAN: {$question}\n\n" .
                 "Berikan jawaban yang mendalam dan informatif.";

        Log::info('Sending enhanced prompt to Groq...');

        try {
            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'Anda adalah Agent Koala, asisten AI tingkat tinggi untuk server HotPot. Anda memiliki akses ke logs, statistik, dan status server secara real-time. Anda sangat teknis, ramah, dan proaktif.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                Log::info('Groq API Success');
                $reply = $response->json()['choices'][0]['message']['content'];
                $this->sendMessage($chatId, $reply);
            } else {
                Log::error('Groq API Failed: ' . $response->status() . ' - ' . $response->body());
                $this->sendMessage($chatId, "⚠️ Maaf Juragan, sepertinya otak AI saya sedang gangguan. Coba lagi nanti ya.");
            }
        } catch (\Exception $e) {
            $this->sendMessage($chatId, "⚠️ Error saat menghubungi Agent Koala.");
            Log::error('AI Question Error: ' . $e->getMessage());
        }
    }

    protected function getIntelligenceContext()
    {
        $load = sys_getloadavg();
        $uptime = shell_exec('uptime -p');
        $free = shell_exec('free -m');
        $df = shell_exec('df -h /');
        $top = shell_exec('ps aux --sort=-%cpu | head -n 10');
        
        $nginxStatus = trim(shell_exec('systemctl is-active nginx') ?: 'unknown');
        $mysqlStatus = trim(shell_exec('systemctl is-active mysql') ?: 'unknown');
        $phpStatus = trim(shell_exec('systemctl is-active php8.2-fpm') ?: 'unknown');
        $radiusStatus = trim(shell_exec('systemctl is-active freeradius') ?: 'unknown');
        
        $activeSessions = 0;
        $totalVouchers = 0;
        try {
            $activeSessions = DB::table('radacct')->whereNull('acctstoptime')->count();
            $totalVouchers = DB::table('radcheck')->count();
        } catch (\Exception $e) {}

        $laravelLog = shell_exec('tail -n 12 storage/logs/laravel.log | grep -iE "error|exception" | head -n 10');
        $radiusLog = shell_exec('tail -n 12 /var/log/freeradius/radius.log 2>&1 | head -n 10');

        $gitBranch = shell_exec('git rev-parse --abbrev-ref HEAD 2>/dev/null') ?: 'main';
        $gitCommit = shell_exec('git log -1 --pretty=format:"%h - %s" 2>/dev/null') ?: 'no git info';

        return [
            'load' => $load,
            'uptime' => trim($uptime),
            'ram' => trim($free),
            'disk' => trim($df),
            'top' => trim($top),
            'services' => [
                'nginx' => $nginxStatus,
                'mysql' => $mysqlStatus,
                'php' => $phpStatus,
                'radius' => $radiusStatus
            ],
            'metrics' => [
                'active_sessions' => $activeSessions,
                'total_vouchers' => $totalVouchers,
            ],
            'logs' => [
                'laravel' => trim($laravelLog),
                'radius' => trim($radiusLog)
            ],
            'git' => [
                'branch' => trim($gitBranch),
                'commit' => trim($gitCommit)
            ]
        ];
    }

    protected function answerCallback($callbackId, $text)
    {
        return Http::post("{$this->apiUrl}/answerCallbackQuery", [
            'callback_query_id' => $callbackId,
            'text' => $text,
        ]);
    }
}

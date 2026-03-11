<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaConfig;
use Illuminate\Support\Facades\Http;
use Exception;
use App\Models\CustomerMember;

class WaGatewayController extends Controller
{
    public function index()
    {
        $config = WaConfig::where('user_id', auth()->id())->first();
        return view('wa_gateway.index', compact('config'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'api_key' => 'required|string',
            'sender_number' => 'nullable|string',
            'billing_template' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['api_key'] = trim($validated['api_key']); // Hapus spasi otomatis
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['user_id'] = auth()->id();

        WaConfig::updateOrCreate(
            ['user_id' => auth()->id()],
            $validated
        );

        return redirect()->route('wa_gateway.index')->with('success', 'Konfigurasi WhatsApp Gateway berhasil disimpan!');
    }

    public function testConnection(Request $request)
    {
        $config = WaConfig::where('user_id', auth()->id())->first();
        
        if (!$config) {
            return redirect()->back()->with('error', 'Konfigurasi belum disimpan!');
        }

        if ($config->provider === 'fonnte') {
            try {
                // Gunakan Header Authorization sesuai dokumentasi resmi Fonnte
                $response = Http::withHeaders([
                    'Authorization' => $config->api_key
                ])->post('https://api.fonnte.com/device');

                $data = $response->json();
                
                // Logging untuk debug jika masih gagal (Cek storage/logs/laravel.log)
                \Illuminate\Support\Facades\Log::info('Fonnte Test Result:', ['data' => $data]);

                if ($response->successful() && isset($data['status']) && $data['status'] === true) {
                    $deviceName = $data['device'] ?? 'Unknown';
                    $deviceStatus = $data['device_status'] ?? 'connect';
                    return redirect()->back()->with('success', "✅ Koneksi Fonnte Berhasil!\n\nDevice: {$deviceName}\nStatus: {$deviceStatus}");
                }

                $reason = $data['reason'] ?? 'API Key tidak valid atau device tidak terhubung.';
                return redirect()->back()->with('error', "❌ Koneksi Gagal!\nPesan: " . $reason);

            } catch (Exception $e) {
                return redirect()->back()->with('error', "❌ Error: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Provider belum didukung untuk pengetesan otomatis.');
    }

    public function sendBilling($id)
    {
        $customer = CustomerMember::findOrFail($id);
        $config = WaConfig::where('user_id', auth()->id())->first();

        if (!$config || !$config->is_active) {
            return redirect()->back()->with('error', 'WhatsApp Gateway belum dikonfigurasi atau tidak aktif!');
        }

        if (!$customer->whatsapp) {
            return redirect()->back()->with('error', 'Nomor WhatsApp pelanggan belum diisi!');
        }

        $defaultTemplate = "Halo *{name}*,\n\nIni adalah pengingat tagihan internet Anda.\n\n" .
                   "*Detail Tagihan:*\n" .
                   "• Jumlah: *Rp {amount}*\n" .
                   "• Jatuh Tempo: *{due_date}*\n\n" .
                   "Mohon segera melakukan pembayaran agar layanan tetap aktif. Terima kasih.";

        $template = $config->billing_template ?: $defaultTemplate;

        $message = str_replace(
            ['{name}', '{amount}', '{due_date}'],
            [
                $customer->name,
                number_format($customer->bill_amount, 0, ',', '.'),
                ($customer->payment_date ? date('d M Y', strtotime($customer->payment_date)) : '-')
            ],
            $template
        );

        if ($config->provider === 'fonnte') {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $config->api_key
                ])->asForm()->post('https://api.fonnte.com/send', [
                    'target' => $customer->whatsapp,
                    'message' => $message,
                ]);

                $data = $response->json();

                if ($response->successful() && isset($data['status']) && $data['status'] === true) {
                    return redirect()->back()->with('success', "✅ Tagihan berhasil dikirim ke {$customer->name}!");
                }

                return redirect()->back()->with('error', "❌ Gagal mengirim WA: " . ($data['reason'] ?? 'Unknown error'));

            } catch (Exception $e) {
                return redirect()->back()->with('error', "❌ Error: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Provider tidak didukung untuk pengiriman pesan.');
    }

    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'broadcast_message' => 'required|string',
        ]);

        $config = WaConfig::where('user_id', auth()->id())->first();
        if (!$config || !$config->is_active) {
            return redirect()->back()->with('error', 'WhatsApp Gateway belum dikonfigurasi atau tidak aktif!');
        }

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $successCount = 0;
        $failCount = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $number = trim($data[0] ?? '');
            $name = trim($data[1] ?? 'Pelanggan');

            if (empty($number)) continue;

            $message = str_replace('{name}', $name, $request->broadcast_message);

            if ($config->provider === 'fonnte') {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => $config->api_key,
                    ])->post('https://api.fonnte.com/send', [
                        'target' => $number,
                        'message' => $message,
                    ]);

                    if ($response->successful()) {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                } catch (\Exception $e) {
                    $failCount++;
                }
            } else {
                // Implement other providers if needed
                $failCount++;
            }
        }

        fclose($handle);

        return redirect()->back()->with('success', "Broadcast selesai! Berhasil: $successCount, Gagal: $failCount");
    }
}

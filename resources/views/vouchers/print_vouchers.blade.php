<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Vouchers - {{ $batchInfo->profile ?? 'Batch' }}</title>
    <style>
        :root {
            --print-scale: 1.0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .print-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .print-header p {
            font-size: 14px;
            color: #666;
        }
        
        .voucher-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
            /* Apply scaling and layout reflow to adjust to paper size */
            zoom: var(--print-scale);
            /* Fallback for browsers that do not support zoom */
            -moz-transform: scale(var(--print-scale));
            -moz-transform-origin: top left;
        }
        
        .voucher-card {
            border: 2px solid #333;
            padding: 15px;
            text-align: center;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            page-break-inside: avoid;
        }
        
        .voucher-username {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .voucher-password {
            font-size: 14px;
            margin-bottom: 10px;
            color: #555;
        }
        
        .voucher-info {
            font-size: 12px;
            color: #777;
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        
        .qrcode {
            width: 120px;
            height: 120px;
            margin: 10px auto;
        }
        
        /* Fixed Control Panel */
        .control-panel {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(0,0,0,0.05);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .scale-control {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .scale-control label {
            font-size: 10px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .scale-control input[type=range] {
            width: 150px;
            cursor: pointer;
        }

        @media print {
            body {
                padding: 10px;
            }
            
            .no-print, .control-panel {
                display: none;
            }
            
            .voucher-card {
                box-shadow: none;
            }
        }
        
        .print-button {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .print-button:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="control-panel no-print">
        <div class="scale-control">
            <label for="scaleSlider">Voucher Scale: <span id="scaleValue">100</span>%</label>
            <input type="range" id="scaleSlider" min="50" max="200" value="100" step="1">
        </div>
        <button class="print-button" onclick="window.print()">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
    
    <div class="print-header">
        <h1>Voucher Batch Print</h1>
        <p>
            Profile: <strong>{{ $batchInfo->profile ?? '-' }}</strong> | 
            Reseller: <strong>{{ $batchInfo->reseller_name ?? 'Admin' }}</strong> | 
            Generated: <strong>{{ $batchInfo->generated_at ? \Carbon\Carbon::parse($batchInfo->generated_at)->format('d M Y H:i') : '-' }}</strong>
        </p>
        <p>Total Vouchers: <strong>{{ $vouchers->count() }}</strong></p>
    </div>
    
    <div class="voucher-container">
        @foreach($vouchers as $voucher)
            @if($template && $template->html_content)
                {{-- Use template if available --}}
                @php
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http://hotspot.mikhmon/login?username={$voucher->username}&password={$voucher->password}";
                    
                    $replacements = [
                        '{{username}}' => $voucher->username,
                        '{{password}}' => $voucher->password,
                        '{{price}}' => 'Rp ' . number_format($voucher->price, 0, ',', '.'),
                        '{{selling_price}}' => 'Rp ' . number_format($voucher->selling_price ?? $voucher->price, 0, ',', '.'),
                        '{{validity}}' => $voucher->validity ?? '-',
                        '{{profile}}' => $voucher->profile,
                        '{{qrcode}}' => "<img src='{$qrUrl}' class='qrcode'>",
                        '{{server}}' => $voucher->server ?? 'all',
                        '{{reseller}}' => $batchInfo->reseller_name ?? 'Admin',
                        '{{comment}}' => $voucher->comment ?? '',
                        '{{timelimit}}' => !empty($voucher->timelimit) ? $voucher->timelimit : '-',
                        '{{datalimit}}' => !empty($voucher->validity) ? $voucher->validity : '-', // Instruction: replace datalimit with validity
                        '{{hotspotname}}' => !empty($voucher->hotspotname) ? $voucher->hotspotname : 'HOTSPOT',
                        '{{cs_number}}' => auth()->user()->whatsapp ?? auth()->user()->phone ?? '0817200386',
                    ];
                    
                    $html = str_replace(array_keys($replacements), array_values($replacements), $template->html_content);
                @endphp
                {!! $html !!}
            @else
                {{-- Default voucher card - Professional --}}
                <div class="voucher-card" style="width:260px; border-radius:12px; border:2px solid #cbd5e1; overflow:hidden; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align:left; padding:0; background:#fff; position:relative; box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <div style="background:#0f172a; color:white; padding:12px 10px; text-align:center;">
                        <div style="font-size:16px; font-weight:900; letter-spacing:1px; text-transform:uppercase; text-shadow:1px 1px 2px rgba(0,0,0,0.5);">{{ !empty($voucher->hotspotname) ? $voucher->hotspotname : (auth()->user()->name ?? 'HOTSPOT') }}</div>
                    </div>
                    
                    <!-- Content -->
                    <div style="padding:15px; display:flex; gap:12px; align-items:center;">
                        @php
                            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&margin=0&data=".urlencode("http://hotspot.mikhmon/login?username={$voucher->username}&password={$voucher->password}");
                        @endphp
                        <img src="{{ $qrUrl }}" style="width:75px; height:75px; border-radius:6px; border:1px solid #e2e8f0; padding:2px; background:#fff;">
                        <div style="flex:1;">
                            <div style="font-size:9px; color:#64748b; text-transform:uppercase; font-weight:bold; letter-spacing:0.5px;">Kode Login / Username</div>
                            <div style="font-size:18px; font-weight:900; color:#0f172a; letter-spacing:1px; line-height:1.2;">{{ $voucher->username }}</div>
                            
                            <div style="font-size:9px; color:#64748b; margin-top:6px; text-transform:uppercase; font-weight:bold; letter-spacing:0.5px;">Password</div>
                            <div style="font-size:14px; font-weight:bold; color:#334155; line-height:1.2;">{{ $voucher->password }}</div>
                        </div>
                    </div>
                    
                    <!-- Details Box -->
                    <div style="padding:10px 15px; background:#f8fafc; border-top:1px dashed #cbd5e1; border-bottom:1px dashed #cbd5e1; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-size:13px; font-weight:900; color:#ef4444;">Rp {{ number_format($voucher->selling_price ?? $voucher->price, 0, ',', '.') }}</div>
                            <div style="font-size:10px; font-weight:bold; color:#334155; margin-top:2px;">{{ $voucher->profile }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:10px; color:#475569;"><span style="font-weight:bold;">Aktif:</span> {{ !empty($voucher->validity) ? $voucher->validity : '-' }}</div>
                            <div style="font-size:10px; color:#475569; margin-top:2px;"><span style="font-weight:bold;">Waktu:</span> {{ !empty($voucher->timelimit) ? $voucher->timelimit : 'Unlim' }}</div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div style="padding:12px 15px; text-align:center; background:#fff;">
                        <div style="font-size:11px; color:#1e293b; font-weight:bold; margin-bottom:5px;">
                            Login Link: <span style="color:#2563eb;">hotspot.mikhmon</span>
                        </div>
                        <div style="font-size:9px; color:#94a3b8; font-weight:600;">
                            Dicetak/Reseller: <span style="color:#64748b;">{{ $batchInfo->reseller_name ?? 'Admin' }}</span><br>
                            Komplain/CS: <span style="color:#64748b;">{{ auth()->user()->whatsapp ?? auth()->user()->phone ?? '0817200386' }}</span>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    
    <script>
        const slider = document.getElementById('scaleSlider');
        const scaleValue = document.getElementById('scaleValue');
        const container = document.querySelector('.voucher-container');

        slider.addEventListener('input', (e) => {
            const val = e.target.value;
            scaleValue.textContent = val;
            document.documentElement.style.setProperty('--print-scale', val / 100);
        });

        // Optional: Auto print on load
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

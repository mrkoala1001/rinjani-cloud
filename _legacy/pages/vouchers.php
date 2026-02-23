<?php
// pages/vouchers.php

$config = get_mikrotik_config($conn);
$router_connected = false;
$profiles = [];
$generated_vouchers = [];

// Fetch Resellers for Dropdown
$resellers_result = $conn->query("SELECT id, name FROM resellers ORDER BY name ASC");
$resellers_list = [];
if ($resellers_result->num_rows > 0) {
    while($r = $resellers_result->fetch_assoc()) {
        $resellers_list[] = $r;
    }
}
// Fetch Templates
$templates_result = $conn->query("SELECT id, name FROM voucher_templates ORDER BY name ASC");
$templates_list = [];
while($t = $templates_result->fetch_assoc()) {
    $templates_list[] = $t;
}

if ($config) {
    $API = new RouterOS\Client();
    if ($API->connect($config['host'], $config['user'], $config['pass'])) {
        $router_connected = true;
        
        // Fetch User Profiles
        $profiles = $API->comm('/ip/hotspot/user/profile/print');
        
        // Handle Generation
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
            $qty = intval($_POST['qty']);
            $server = $_POST['server'] ?? 'all';
            $user_mode = $_POST['user_mode'] ?? 'up';
            $user_length = intval($_POST['user_length']);
            $prefix = $_POST['prefix'];
            $char_set = $_POST['char_set'] ?? 'mixed';
            $profile = $_POST['profile'];
            $reseller_id = $_POST['reseller_id'];
            $timelimit = $_POST['timelimit'] ?? '';
            $datalimit = $_POST['datalimit'] ?? '';
            
            // Get Reseller Name
            $reseller_name = 'Admin';
            foreach ($resellers_list as $r) {
                if ($r['id'] == $reseller_id) {
                    $reseller_name = $r['name'];
                    break;
                }
            }

            $full_comment = "VC " . date('d/m/Y') . " [$reseller_name]";

            $count_success = 0;
            
            
            // Fetch Template Content if Selected
            $template_file = null;
            $css_content = '';
            $template_id = $_POST['template_id'] ?? 0;
            
            if ($template_id > 0) {
                $tpl = $conn->query("SELECT * FROM voucher_templates WHERE id = $template_id")->fetch_assoc();
                if ($tpl) {
                    $css_content = $tpl['css_content'];
                    // Create Temp File for Template
                    $template_file = tempnam(sys_get_temp_dir(), 'tpl_');
                    file_put_contents($template_file, $tpl['html_content']);
                }
            }

            // Get Helper Variables (Mock for demo, ideally fetch from settings/profile)
            // Get Helper Variables (Mock for demo, ideally fetch from settings/profile)
            $hotspotname = $_POST['hotspot_name'] ?? ($config['hotspot_name'] ?? 'Mikhmon Hotspot');
            $dnsname = $_POST['dns_name'] ?? ($config['dns_name'] ?? 'hotspot.mikhmon');
            $logo = 'assets/img/logo.png'; // Default logo path
            
            for ($i = 0; $i < $qty; $i++) {
                $code = generate_code($user_length, $prefix, $char_set);
                $pass = ($user_mode == 'up') ? $code : generate_code($user_length, '', $char_set);

                $add_data = [
                    'server' => $server,
                    'name' => $code,
                    'password' => $pass,
                    'profile' => $profile,
                    'comment' => $full_comment
                ];
                
                if (!empty($timelimit)) $add_data['limit-uptime'] = $timelimit;
                if (!empty($datalimit)) $add_data['limit-bytes-total'] = $datalimit;

                $API->comm('/ip/hotspot/user/add', $add_data);
                
                // Get Metadata for Profile (Price, Validity) to pass to template
                $meta = $conn->query("SELECT price, validity FROM hotspot_profile_metadata WHERE profile_name = '$profile'")->fetch_assoc();
                $price = format_currency($meta['price'] ?? 0);
                $getsprice = $meta['price'] ?? 0; // Raw price for logic
                $validity = $meta['validity'] ?? '-';
                $num = $i + 1;
                $username = $code;
                $password = $pass;
                $usermode = ($user_mode == 'up') ? 'vc' : 'up'; // vc = voucher code mode
                $qr = 'yes'; // Default to yes
                // Generate QR Code URL
                $qrcode = '<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http://'.$dnsname.'/login?username='.$username.'&password='.$password.'" class="qrcode">';
                
                // Render Template to String
                ob_start();
                if ($template_file) {
                    include $template_file;
                } else {
                    // Fallback Default Layout if no template selected (or deleted)
                    echo '<div class="border p-2 mb-2 text-center">';
                    echo '<b>'.$username.'</b><br>';
                    echo '<small>'.$pass.'</small>';
                    echo '</div>';
                }
                $rendered_html = ob_get_clean();

                $generated_vouchers[] = [
                    'html' => $rendered_html
                ];
                $count_success++;
            }
            
            if ($template_file && file_exists($template_file)) {
                unlink($template_file); // Cleanup
            }
            
            set_flash_message('success', "Generated $count_success vouchers!");
        }

        $API->disconnect();
    }
}
?>

<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar Generator Form -->
        <div class="w-full md:w-1/3">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border-t-4 border-primary">
                <div class="bg-gray-50 px-4 py-3 border-b flex items-center justify-between">
                    <h3 class="font-bold text-gray-700"><i class="fas fa-magic mr-2"></i>Voucher Generator</h3>
                </div>
                <form method="POST" class="p-4 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Quantity</label>
                        <input type="number" name="qty" required min="1" max="500" value="10" 
                               class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Server</label>
                        <select name="server" class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                            <option value="all">all</option>
                            <option value="hotspot1">hotspot1</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">User Mode</label>
                        <select name="user_mode" class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                            <option value="up">Username = Password</option>
                            <option value="u+p">Username & Password</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Name Length</label>
                            <input type="number" name="user_length" value="6" min="3" max="12"
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Prefix</label>
                            <input type="text" name="prefix" placeholder="VC-" 
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Character Set</label>
                        <select name="char_set" class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                            <option value="mixed">Mixed (A,b,1)</option>
                            <option value="uppercase">Uppercase (A,1)</option>
                            <option value="lowercase">Lowercase (a,1)</option>
                            <option value="numbers">Numbers Only (1)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Profile</label>
                        <select name="profile" required class="w-full border rounded px-3 py-2 text-sm focus:outline-none font-bold text-secondary">
                            <?php foreach ($profiles as $prof): ?>
                                <option value="<?php echo $prof['name']; ?>"><?php echo $prof['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Template</label>
                        <select name="template_id" class="w-full border rounded px-3 py-2 text-sm focus:outline-none font-bold text-purple-600">
                            <option value="0">-- Default --</option>
                            <?php foreach ($templates_list as $tpl): ?>
                                <option value="<?php echo $tpl['id']; ?>"><?php echo $tpl['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Time Limit</label>
                            <input type="text" name="timelimit" placeholder="e.g. 1h" 
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Data Limit</label>
                            <input type="text" name="datalimit" placeholder="e.g. 500M" 
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Seller / Reseller</label>
                        <select name="reseller_id" class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                            <option value="0">Default (Admin)</option>
                            <?php foreach ($resellers_list as $res): ?>
                                <option value="<?php echo $res['id']; ?>"><?php echo $res['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pb-2 border-b mb-2">
                         <div class="col-span-2">
                            <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Template Parameters</label>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Hotspot Name</label>
                            <input type="text" name="hotspot_name" value="<?php echo htmlspecialchars($config['hotspot_name'] ?? 'Mikhmon Hotspot'); ?>" 
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none placeholder-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">DNS Name</label>
                            <input type="text" name="dns_name" value="<?php echo htmlspecialchars($config['dns_name'] ?? 'hotspot.mikhmon'); ?>" 
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none placeholder-gray-300">
                        </div>
                    </div>

                    <button type="submit" name="generate" class="w-full bg-primary hover:bg-red-600 text-white font-bold py-2 rounded transition shadow-md">
                        <i class="fas fa-play mr-2"></i>Generate
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Display Area -->
        <div class="w-full md:w-2/3">
            <?php if (!empty($generated_vouchers)): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b flex items-center justify-between">
                        <h3 class="font-bold text-gray-700">Vouchers Generated</h3>
                        <div class="space-x-2">
                             <button onclick="window.print()" class="bg-secondary hover:bg-teal-600 text-white px-3 py-1 rounded text-sm transition">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                        </div>
                    </div>
                    <div class="p-4 overflow-y-auto max-h-[600px] bg-gray-100" id="voucher-print-area">
                        <!-- Inject Template CSS -->
                        <?php if (!empty($css_content)) echo "<style>$css_content</style>"; ?>
                        
                        <div class="flex flex-wrap justify-center gap-2">
                            <?php foreach ($generated_vouchers as $v): ?>
                                <?php echo $v['html']; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-md p-20 text-center border-2 border-dashed border-gray-300">
                    <i class="fas fa-ticket-alt fa-4x text-gray-200 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-400">Ready to Generate Vouchers</h3>
                    <p class="text-gray-400 text-sm mt-2">Fill the form on the left to start batch generation.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #voucher-print-area, #voucher-print-area * { visibility: visible; }
        #voucher-print-area { position: absolute; left: 0; top: 0; width: 100%; padding: 0; background: white; }
        .bg-gray-100 { background: white !important; }
        .v-card { border: 1px solid #ccc !important; break-inside: avoid; margin-bottom: 5px; }
        .max-h-\[600px\] { max-height: none !important; overflow: visible !important; }
    }
</style>

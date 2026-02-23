<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BillingHistory;
use App\Models\MikrotikConfig;
use RouterOS\Client;
use RouterOS\Query;
use Illuminate\Support\Facades\DB;

$userId = 18;
$mkConfig = MikrotikConfig::where('user_id', $userId)->first();

if (!$mkConfig) {
    die("Config not found\n");
}

echo "1. Connecting to MikroTik...\n";
try {
    $client = new Client([
        'host' => $mkConfig->host,
        'user' => $mkConfig->user,
        'pass' => $mkConfig->pass,
        'port' => (int)($mkConfig->port ?? 8728),
        'timeout' => 5,
    ]);
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

$batchId = 'SIM-' . time();
$prefix = 'sim-';
echo "2. Generating 10 vouchers with batch_id: $batchId\n";

for ($i = 0; $i < 10; $i++) {
    $username = $prefix . bin2hex(random_bytes(3));
    $password = '123';
    
    // Add to MikroTik
    try {
        $q = new Query('/ip/hotspot/user/add');
        $q->add('=name=' . $username);
        $q->add('=password=' . $password);
        $q->add('=comment=SIMULATION');
        $client->query($q)->read();
        echo "   Added Mikrotik: $username\n";
    } catch (Exception $e) {
        echo "   Failed Mikrotik: $username - " . $e->getMessage() . "\n";
        continue;
    }
    
    // Add to DB
    BillingHistory::create([
        'user_id' => $userId,
        'batch_id' => $batchId,
        'username' => $username,
        'password' => $password,
        'voucher_code' => $username,
        'profile' => 'default',
        'price' => 0,
        'generated_at' => now(),
    ]);
}

$dbCount = BillingHistory::where('batch_id', $batchId)->count();
echo "3. Verified DB Count: $dbCount\n";

echo "4. Checking MikroTik for these users...\n";
$allMkUsers = $client->query('/ip/hotspot/user/print')->read();
$foundInMk = 0;
foreach ($allMkUsers as $u) {
    if (str_starts_with($u['name'] ?? '', $prefix)) {
        $foundInMk++;
    }
}
echo "   Found in MikroTik: $foundInMk\n";

echo "5. Simulating Deletion (using Controller logic)...\n";
// SIMULATE deleteBatch logic
$vouchers = BillingHistory::where('batch_id', $batchId)->where('user_id', $userId)->get();
$deletedCount = 0;

$mkUsersMap = [];
foreach ($allMkUsers as $u) {
    if (isset($u['name']) && isset($u['.id'])) {
        $mkUsersMap[$u['name']] = $u['.id'];
    }
}

foreach ($vouchers as $voucher) {
    if (isset($mkUsersMap[$voucher->username])) {
        $mkId = $mkUsersMap[$voucher->username];
        try {
            $client->query('/ip/hotspot/user/remove', ['.id' => $mkId])->read();
            $deletedCount++;
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'Undefined array key')) {
                $deletedCount++;
            } else {
                echo "   Failed to delete $mkId: " . $e->getMessage() . "\n";
            }
        }
    }
}
echo "   Successfully Deleted from MikroTik: $deletedCount\n";

echo "6. Final Verificaton...\n";
$allMkUsersFinal = $client->query('/ip/hotspot/user/print')->read();
$stillInMk = 0;
foreach ($allMkUsersFinal as $u) {
    if (str_starts_with($u['name'] ?? '', $prefix)) {
        $stillInMk++;
    }
}
echo "   Vouchers still in MikroTik: $stillInMk\n";

// Cleanup DB
BillingHistory::where('batch_id', $batchId)->delete();
echo "   DB cleaned up.\n";

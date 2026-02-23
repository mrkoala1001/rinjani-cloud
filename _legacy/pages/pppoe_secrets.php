<?php
// pages/pppoe.php

$config = get_mikrotik_config($conn);
$router_connected = false;
$secrets = [];
$profiles = [];

if ($config) {
    $API = new RouterOS\Client();
    if ($API->connect($config['host'], $config['user'], $config['pass'])) {
        $router_connected = true;

        // Fetch Profiles for Dropdown
        $profiles = $API->comm('/ppp/profile/print');

        // Fetch Secrets
        $secrets = $API->comm('/ppp/secret/print');

        $API->disconnect();
    } else {
        set_flash_message('error', 'Could not connect to MikroTik.');
    }
}
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">PPPoE Secrets</h2>
        <button onclick="document.getElementById('addSecretModal').classList.remove('hidden')"
                class="bg-primary hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow transition">
            <i class="fas fa-plus mr-2"></i>Add Secret
        </button>
    </div>

    <?php if ($router_connected): ?>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Password</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Profile</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Service</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Last Looged Out</th>
                         <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($secrets as $secret): ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center">
                                <div class="ml-3">
                                    <p class="text-gray-900 whitespace-no-wrap font-bold">
                                        <?php echo $secret['name']; ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap"><?php echo $secret['password']; ?></p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                <span aria-hidden="true" class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                <span class="relative"><?php echo $secret['profile']; ?></span>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap"><?php echo $secret['service']; ?></p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-600 whitespace-no-wrap"><?php echo $secret['last-logged-out'] ?? '-'; ?></p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-500 whitespace-no-wrap italic"><?php echo $secret['comment'] ?? ''; ?></p>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Add Secret Modal (Simple Alpine/Vanilla JS Toggle) -->
<div id="addSecretModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Add New PPPoE Secret</h3>
            <form class="mt-2 text-left" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="text" name="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Profile</label>
                    <select name="profile" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <?php foreach($profiles as $prof): ?>
                            <option value="<?php echo $prof['name']; ?>"><?php echo $prof['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Local Address (Opt)</label>
                    <input type="text" name="local_address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Remote Address (Opt)</label>
                    <input type="text" name="remote_address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Comment</label>
                    <input type="text" name="comment" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="items-center px-4 py-3">
                    <button type="submit" name="add_secret" class="px-4 py-2 bg-primary text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        Create Secret
                    </button>
                    <button type="button" onclick="document.getElementById('addSecretModal').classList.add('hidden')" class="mt-3 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

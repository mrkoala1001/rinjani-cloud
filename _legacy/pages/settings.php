<?php
// pages/settings.php
$config = get_mikrotik_config($conn);
?>

<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">MikroTik Settings</h2>

    <?php if ($message): ?>
        <div class="mb-4 p-4 rounded text-white <?php echo ($message_type == 'error') ? 'bg-red-500' : 'bg-green-500'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="host">
                        MikroTik IP / Host
                    </label>
                    <input type="text" name="host" id="host" required
                           value="<?php echo $config['host'] ?? ''; ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary"
                           placeholder="192.168.88.1">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="port">
                        API Port
                    </label>
                    <input type="number" name="port" id="port"
                           value="<?php echo $config['port'] ?? '8728'; ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary"
                           placeholder="8728">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="user">
                        Username
                    </label>
                    <input type="text" name="user" id="user" required
                           value="<?php echo $config['user'] ?? ''; ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary"
                           placeholder="admin">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="pass">
                        Password
                    </label>
                    <input type="password" name="pass" id="pass"
                           value="<?php echo $config['pass'] ?? ''; ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary"
                           placeholder="********">
                </div>
            </div>

            <div class="flex items-center justify-between mt-8">
               <button type="submit" name="test_connection" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                    <i class="fas fa-plug mr-2"></i>Test Connection
                </button>
                <button type="submit" name="save_config" class="bg-primary hover:bg-red-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                    <i class="fas fa-save mr-2"></i>Save Configuration
                </button>
            </div>
        </form>
    </div>
</div>

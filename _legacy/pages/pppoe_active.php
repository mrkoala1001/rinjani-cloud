<?php
// pages/pppoe_active.php
?>
<div class="max-w-6xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">PPPoE Aktif (Active Sessions)</h2>
    
    <?php if ($router_connected): ?>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Service</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Address</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Uptime</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($active_ppp) > 0): ?>
                    <?php foreach ($active_ppp as $session): ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold"><?php echo $session['name']; ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo $session['service']; ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo $session['address']; ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo $session['uptime']; ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <a href="index.php?page=pppoe_active&remove=<?php echo urlencode($session['.id']); ?>" 
                               onclick="return confirm('Remove this session?')"
                               class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash-alt"></i> Remove
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-center">No active PPPoE sessions.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
        <p class="font-bold">Error</p>
        <p>Could not connect to MikroTik.</p>
    </div>
    <?php endif; ?>
</div>

<?php
// pages/pppoe_profiles.php
?>
<div class="max-w-6xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">PPPoE Profil (PPP Profiles)</h2>
    
    <?php if ($router_connected): ?>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Local Address</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Remote Address</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rate Limit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ppp_profiles as $profile): ?>
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold"><?php echo $profile['name']; ?></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo $profile['local-address'] ?? '-'; ?></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo $profile['remote-address'] ?? '-'; ?></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo $profile['rate-limit'] ?? '-'; ?></td>
                </tr>
                <?php endforeach; ?>
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

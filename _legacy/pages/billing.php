<?php
// pages/billing.php

// Fetch History
$history_query = "SELECT * FROM billing_history ORDER BY date_sold DESC LIMIT 100";
$results = $conn->query($history_query);
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Billing & Sales History</h2>
        <form method="POST">
             <button type="submit" name="sync_sales" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow transition">
                <i class="fas fa-sync mr-2"></i>Sync Active Sales
            </button>
        </form>
    </div>
    
    <div class="mb-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4">
        <p class="font-bold">Info</p>
        <p>Sync scans MikroTik's Active Hotspot list. If a user is active and hasn't been recorded this month, they are added here. Ensure your Hotspot User Profiles have prices in their "Comment" field (just numbers) for accurate calculation.</p>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Voucher / User</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Profile</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Price (Income)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($results->num_rows > 0): ?>
                    <?php while($row = $results->fetch_assoc()): ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <?php echo date('d M Y H:i', strtotime($row['date_sold'])); ?>
                        </td>
                         <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-mono">
                            <?php echo $row['voucher_code']; ?>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="bg-gray-200 rounded-full px-3 py-1 text-xs font-semibold text-gray-700">
                                <?php echo $row['profile']; ?>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold text-green-600">
                            <?php echo format_currency($row['price']); ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                            No billing history found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

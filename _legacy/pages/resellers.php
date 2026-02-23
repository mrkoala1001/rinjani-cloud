<?php
// pages/resellers.php

// Fetch Resellers
$query = "SELECT * FROM resellers ORDER BY name ASC";
$result = $conn->query($query);
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Resellers Management</h2>
        <button onclick="document.getElementById('addResellerModal').classList.remove('hidden')"
                class="bg-primary hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow transition">
            <i class="fas fa-plus mr-2"></i>Add Reseller
        </button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Phone</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Balance</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap font-bold"><?php echo htmlspecialchars($row['name']); ?></p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($row['phone']); ?></p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap"><?php echo format_currency($row['balance']); ?></p>
                        </td>
                         <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-500 whitespace-no-wrap"><?php echo date('d M Y', strtotime($row['created_at'])); ?></p>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                 <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No resellers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Reseller Modal -->
<div id="addResellerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Add New Reseller</h3>
            <form class="mt-2 text-left" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
                    <input type="text" name="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="items-center px-4 py-3">
                    <button type="submit" name="add_reseller" class="px-4 py-2 bg-primary text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        Save Reseller
                    </button>
                    <button type="button" onclick="document.getElementById('addResellerModal').classList.add('hidden')" class="mt-3 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

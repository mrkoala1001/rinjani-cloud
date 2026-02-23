<?php
// pages/customer_list.php
?>

<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-users mr-2 text-primary"></i> Data Pelanggan (<?php echo ucfirst($type_filter); ?>)
            </h2>
            <p class="text-sm text-gray-500">Manage your customer database efficiently.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            <!-- Search Form -->
            <form action="" method="GET" class="flex items-center">
                <input type="hidden" name="page" value="customer_list">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type_filter); ?>">
                <div class="relative">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" 
                           placeholder="Search name, location..." 
                           class="pl-8 pr-4 py-2 border rounded-l-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-xs"></i>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded-r-lg hover:bg-indigo-700 text-sm">
                    Search
                </button>
            </form>

            <!-- Add Button -->
            <a href="index.php?page=customer_form&type=<?php echo htmlspecialchars($type_filter); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow text-sm">
                <i class="fas fa-plus mr-1"></i> Add
            </a>

            <!-- CSV Upload Form -->
            <form action="" method="POST" enctype="multipart/form-data" class="flex items-center gap-1">
                <label class="cursor-pointer bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow text-sm flex items-center">
                    <i class="fas fa-file-csv mr-1"></i> Import
                    <input type="file" name="csv_file" accept=".csv" required class="hidden" onchange="this.form.submit()">
                    <input type="hidden" name="import" value="1">
                </label>
            </form>
        </div>
    </div>

    <!-- Customer Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama / Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi / IP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tagihan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perangkat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i><br>
                            No customers found. <br>
                            <span class="text-xs">Try adding a new customer or importing a CSV.</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $c): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($c['name']); ?></div>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo ($c['type'] == 'MEMBER') ? 'bg-green-100 text-green-800' : 
                                              (($c['type'] == 'RESELLER') ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'); ?>">
                                    <?php echo htmlspecialchars($c['type']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($c['location']); ?></div>
                                <div class="text-xs text-xs text-gray-400">
                                    <i class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($c['coordinates'] ?? '-'); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-800">Rp <?php echo number_format($c['bill_amount'], 0, ',', '.'); ?></div>
                                <div class="text-xs text-green-600">Paid: Rp <?php echo number_format($c['paid_amount'], 0, ',', '.'); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-900 font-bold"><?php echo htmlspecialchars($c['device_name']); ?></div>
                                <div class="text-xs text-gray-500 font-mono"><?php echo htmlspecialchars($c['device_ip']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-500 max-w-xs truncate" title="<?php echo htmlspecialchars($c['notes']); ?>">
                                    <?php echo htmlspecialchars($c['notes']); ?>
                                </div>
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="index.php?page=customer_form&id=<?php echo $c['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                    <input type="hidden" name="delete_id" value="<?php echo $c['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// pages/voucher_recap.php
?>
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Laporan Penjualan (Daily Recap)</h2>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-secondary">
        <table class="min-w-full leading-normal text-xs">
            <thead>
                <tr class="bg-gray-50 uppercase text-gray-600">
                    <th class="px-4 py-3 border-b border-gray-200 text-left font-bold">#</th>
                    <th class="px-4 py-3 border-b border-gray-200 text-left font-bold">Tanggal</th>
                    <th class="px-4 py-3 border-b border-gray-200 text-left font-bold text-center">Jumlah Terjual (Qty)</th>
                    <th class="px-4 py-3 border-b border-gray-200 text-left font-bold text-right">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recap_data->num_rows > 0): ?>
                    <?php $i = 1; while($row = $recap_data->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                        <td class="px-4 py-3 text-gray-500 font-mono"><?php echo $i++; ?></td>
                        <td class="px-4 py-3 text-gray-700 font-bold"><?php echo date('d M Y', strtotime($row['sale_date'])); ?></td>
                        <td class="px-4 py-3 text-center text-blue-600 font-bold bg-blue-50 bg-opacity-30"><?php echo $row['qty']; ?></td>
                        <td class="px-4 py-3 text-right font-black text-green-600"><?php echo format_currency($row['total_income']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="px-5 py-20 bg-white text-center text-gray-400 italic">No sales data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- MikroTik Reports Section -->
    <div class="mt-8" x-data="{ activeTab: '<?php echo date('Y'); ?>' }">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                Laporan dari MikroTik (System Scripts)
            </h3>
            <div class="flex space-x-2">
                <?php foreach ($mikrotik_reports as $year => $scripts): ?>
                <button @click="activeTab = '<?php echo $year; ?>'" 
                        :class="activeTab === '<?php echo $year; ?>' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                        class="px-4 py-2 rounded-lg font-bold text-sm transition">
                    Tahun <?php echo $year; ?> 
                    <span class="ml-1 text-xs opacity-75 bg-black bg-opacity-20 px-2 py-0.5 rounded-full"><?php echo count($scripts); ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-indigo-500">
            <?php foreach ($mikrotik_reports as $year => $scripts): ?>
            <div x-show="activeTab === '<?php echo $year; ?>'" x-transition class="w-full">
                <table class="min-w-full leading-normal text-xs">
                    <thead>
                        <tr class="bg-gray-50 uppercase text-gray-600">
                            <th class="px-4 py-3 border-b border-gray-200 text-left font-bold">Nama Script</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-left font-bold">Pembuat (Owner)</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-left font-bold">Konten (Source)</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-left font-bold">Terakhir Dijalankan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($scripts)): ?>
                            <?php foreach ($scripts as $report): ?>
                            <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                                <td class="px-4 py-3 text-gray-700 font-bold font-mono"><?php echo htmlspecialchars($report['name']); ?></td>
                                <td class="px-4 py-3 text-gray-500 font-mono"><?php echo htmlspecialchars($report['owner']); ?></td>
                                <td class="px-4 py-3 text-gray-600 font-mono text-[10px] break-all max-w-lg">
                                    <?php echo nl2br(htmlspecialchars(substr($report['source'], 0, 300))); ?>
                                    <?php if(strlen($report['source']) > 300): ?>
                                        <span class="text-blue-500 cursor-pointer" title="<?php echo htmlspecialchars($report['source']); ?>">...</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-gray-500"><?php echo htmlspecialchars($report['last_started']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 italic">Tidak ada laporan untuk tahun <?php echo $year; ?>.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

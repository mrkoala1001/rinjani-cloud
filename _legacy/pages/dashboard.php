<?php
// pages/dashboard.php
// Logic has been moved to authorized pages/dashboard_logic.php
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Pemasukan -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-600">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <i class="fas fa-money-bill-transfer fa-2x"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Total Pemasukan</p>
                <p class="text-xl font-black text-gray-800"><?php echo format_currency($total_income); ?></p>
            </div>
        </div>
    </div>

    <!-- Card 2: Total User Aktif -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-secondary">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-teal-100 text-secondary mr-4">
                <i class="fas fa-globe fa-2x"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Total User Aktif</p>
                <p class="text-xl font-black text-gray-800"><?php echo $hotspot_active_count; ?> <span class="text-sm font-normal text-gray-400">Online</span></p>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Jumlah User -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-primary">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-primary mr-4">
                <i class="fas fa-users fa-2x"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Total Jumlah User</p>
                <p class="text-xl font-black text-gray-800"><?php echo $total_voucher_count; ?> <span class="text-sm font-normal text-gray-400">Voucher</span></p>
            </div>
        </div>
    </div>

    <!-- Card 4: Monthly Income -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                <i class="fas fa-calendar-check fa-2x"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Pemasukan Bulan Ini</p>
                <p class="text-xl font-black text-gray-800"><?php echo format_currency($monthly_income); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">System Status</h3>
    <div class="flex items-center">
        <div class="w-3 h-3 rounded-full <?php echo ($router_status == 'Connected') ? 'bg-green-500' : 'bg-red-500'; ?> mr-2"></div>
        <span class="text-gray-700">MikroTik Connection: <strong><?php echo $router_status; ?></strong></span>
        <?php if ($router_status == 'Disconnected'): ?>
            <a href="index.php?page=settings" class="ml-4 text-sm text-blue-500 hover:underline">Configure Now</a>
        <?php endif; ?>
    </div>
</div>

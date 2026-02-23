<?php
// pages/voucher_sold.php
?>
<div class="max-w-6xl mx-auto" x-data="{
    search: '',
    vouchers: <?php echo htmlspecialchars(json_encode($vouchers_data), ENT_QUOTES, 'UTF-8'); ?>,
    formatPrice(val) {
        return 'Rp ' + Number(val).toLocaleString('id-ID');
    },
    get filteredVouchers() {
        return this.vouchers.filter(v => 
            (v.voucher_code && v.voucher_code.toLowerCase().includes(this.search.toLowerCase())) || 
            (v.profile && v.profile.toLowerCase().includes(this.search.toLowerCase())) ||
            (v.server && v.server.toLowerCase().includes(this.search.toLowerCase()))
        );
    },
    get totalIncome() {
        return this.filteredVouchers.reduce((sum, v) => sum + Number(v.current_meta_price ?? v.price), 0);
    }
}">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">History Voucher Terjual</h2>
            <div class="text-sm text-gray-500">
                Total Pemasukan (All Time): <span class="font-bold text-green-600"><?php echo format_currency($total_grand_income); ?></span>
                <span x-show="search.length > 0" x-cloak>
                    <span class="mx-2 text-gray-300">|</span>
                    Tertampil: <span class="font-bold text-blue-600" x-text="formatPrice(totalIncome)"></span>
                </span>
            </div>
        </div>
        <div class="relative w-full md:w-auto">
            <input type="text" x-model="search" placeholder="Cari riwayat..." 
                   class="w-full md:w-64 pl-10 pr-4 py-2 border rounded shadow-sm focus:ring-2 focus:ring-secondary focus:outline-none text-sm">
            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-secondary">
        <div class="overflow-x-auto">
            <table class="min-width-full leading-normal text-xs">
                <thead>
                    <tr class="bg-gray-50 uppercase text-gray-600 border-b">
                        <th class="px-4 py-3 text-left font-bold">Waktu</th>
                        <th class="px-4 py-3 text-left font-bold">Nama User</th>
                        <th class="px-4 py-3 text-left font-bold">Profile</th>
                        <th class="px-4 py-3 text-left font-bold">Server</th>
                        <th class="px-4 py-3 text-left font-bold">Harga</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="v in filteredVouchers" :key="v.id">
                        <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                            <td class="px-4 py-3 text-gray-400 font-mono" x-text="new Date(v.date_sold).toLocaleString('id-ID', {day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit'})"></td>
                            <td class="px-4 py-3 font-bold text-gray-800 font-mono" x-text="v.voucher_code"></td>
                            <td class="px-4 py-3 font-bold text-secondary italic" x-text="v.profile"></td>
                            <td class="px-4 py-3 text-gray-500 font-mono" x-text="v.server || 'all'"></td>
                            <td class="px-4 py-3 font-bold text-green-600 font-mono" x-text="formatPrice(v.current_meta_price ?? v.price)"></td>
                        </tr>
                    </template>
                    <tr x-show="filteredVouchers.length === 0">
                        <td colspan="5" class="px-5 py-20 bg-white text-center text-gray-400 italic font-bold">
                            Tidak ada riwayat penjualan ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

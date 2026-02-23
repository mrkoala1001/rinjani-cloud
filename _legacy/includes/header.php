<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOT POT - MikroTik Manager</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF6B6B', // Hot Pot Red
                        secondary: '#4ECDC4', // Teal contrast
                        dark: '#2D3748',
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-dark text-white flex-shrink-0 hidden md:flex flex-col">
            <?php
            // Logic to get active count for initial state (Global for Sidebar)
            $global_active_user_count = 0;
            $global_active_user_data = [];
            
            // 1. If we are on voucher_online page, use the accurate count
            if ($page == 'voucher_online' && isset($active_users)) {
                $global_active_user_count = count($active_users);
                // Extract data for table
                foreach ($active_users as $u) {
                    if (isset($u['user'])) {
                        $global_active_user_data[] = [
                            'user' => $u['user'],
                            'uptime' => $u['uptime'] ?? '-'
                        ];
                    }
                }
                
                // Update cache
                $_SESSION['cache_active_users_count'] = $global_active_user_count;
                $_SESSION['cache_active_users_data'] = $global_active_user_data;
                $_SESSION['cache_active_users_time'] = time();
            } 
            // 2. If we are on dashboard page, use the count from dashboard logic
            elseif ($page == 'dashboard' && isset($hotspot_active_data)) {
                $global_active_user_count = count($hotspot_active_data);
                $global_active_user_data = $hotspot_active_data;
                
                // Update cache
                $_SESSION['cache_active_users_count'] = $global_active_user_count;
                $_SESSION['cache_active_users_data'] = $global_active_user_data;
                $_SESSION['cache_active_users_time'] = time();
            }
            // 3. Otherwise check cache
            elseif (
                isset($_SESSION['cache_active_users_count']) && 
                isset($_SESSION['cache_active_users_data']) &&
                (time() - $_SESSION['cache_active_users_time'] < 60)
            ) {
                // Cache valid for 60 seconds
                $global_active_user_count = $_SESSION['cache_active_users_count'];
                $global_active_user_data = $_SESSION['cache_active_users_data'];
            }
            // 4. Lazy Fetch (Cache expired or missing)
            else {
                // Attempt to fetch
                $global_active_user_data = get_active_hotspot_data($conn);
                $global_active_user_count = count($global_active_user_data);
                
                // Update cache
                $_SESSION['cache_active_users_count'] = $global_active_user_count;
                $_SESSION['cache_active_users_data'] = $global_active_user_data;
                $_SESSION['cache_active_users_time'] = time();
            }
            ?>
            <div class="p-6 border-b border-gray-700 flex items-center justify-center">
                <h1 class="text-2xl font-bold text-primary"><i class="fa-solid fa-fire-burner mr-2"></i>HOT POT</h1>
            </div>
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-2 px-4">
                    <li>
                        <a href="index.php?page=dashboard" class="flex items-center p-2 rounded hover:bg-gray-700 transition <?php echo ($page == 'dashboard') ? 'bg-gray-700 text-primary' : ''; ?>">
                            <i class="fas fa-tachometer-alt w-6"></i> Dashboard
                        </a>
                    </li>
                    <li x-data="{ open: <?php echo (strpos($page, 'voucher') !== false || $page == 'vouchers') ? 'true' : 'false'; ?> }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-2 rounded hover:bg-gray-700 transition <?php echo (strpos($page, 'voucher') !== false || $page == 'vouchers') ? 'bg-gray-700 text-primary' : ''; ?>">
                            <div class="flex items-center">
                                <i class="fas fa-ticket-alt w-6"></i> Vouchers
                            </div>
                            <div class="flex items-center">
                                <?php if ($global_active_user_count > 0): ?>
                                    <span class="bg-primary text-white text-[10px] px-2 py-0.5 rounded-full font-bold mr-2"><?php echo $global_active_user_count; ?></span>
                                <?php endif; ?>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <ul x-show="open" x-cloak x-transition class="mt-2 space-y-1 px-6 text-sm">
                            <li>
                                <a href="index.php?page=vouchers" class="block p-2 rounded hover:text-primary <?php echo ($page == 'vouchers') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-plus-circle mr-2"></i> Generate Voucher
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=voucher_list" class="block p-2 rounded hover:text-primary <?php echo ($page == 'voucher_list') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-list mr-2"></i> Daftar Voucher
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=voucher_profiles" class="block p-2 rounded hover:text-primary <?php echo ($page == 'voucher_profiles') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-id-card-alt mr-2"></i> Buat Profil & Sync
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=voucher_online" class="block p-2 rounded hover:text-primary <?php echo ($page == 'voucher_online') ? 'text-primary' : 'text-gray-400'; ?> flex justify-between items-center">
                                    <div class="flex items-center">
                                        <i class="fas fa-globe mr-2"></i> Voucher Online
                                    </div>
                                    <?php
                                    if ($global_active_user_count > 0) {
                                        echo '<span class="bg-primary text-white text-[10px] px-2 py-0.5 rounded-full font-bold">' . $global_active_user_count . '</span>';
                                    }
                                    ?>
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=voucher_sold" class="block p-2 rounded hover:text-primary <?php echo ($page == 'voucher_sold') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-shopping-cart mr-2"></i> Voucher Terjual
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=voucher_recap" class="block p-2 rounded hover:text-primary <?php echo ($page == 'voucher_recap') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-file-invoice-dollar mr-2"></i> Rekap Voucher
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=voucher_templates" class="block p-2 rounded hover:text-primary <?php echo ($page == 'voucher_templates') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-scroll mr-2"></i> Template Manager
                                </a>
                            </li>
                        </ul>
                    </li>


                    <!-- PPPoE Menu -->
                    <li x-data="{ open: <?php echo (strpos($page, 'pppoe_') !== false) ? 'true' : 'false'; ?> }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-2 rounded hover:bg-gray-700 transition <?php echo (strpos($page, 'pppoe_') !== false) ? 'bg-gray-700 text-primary' : ''; ?>">
                            <div class="flex items-center">
                                <i class="fas fa-network-wired w-6"></i> PPPoE
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open" x-cloak x-transition class="mt-2 space-y-1 px-6 text-sm">
                            <li>
                                <a href="index.php?page=pppoe_active" class="block p-2 rounded hover:text-primary <?php echo ($page == 'pppoe_active') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-bolt mr-2"></i> PPPoE Aktif
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=pppoe_profiles" class="block p-2 rounded hover:text-primary <?php echo ($page == 'pppoe_profiles') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-layer-group mr-2"></i> PPPoE Profil
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=pppoe_secrets" class="block p-2 rounded hover:text-primary <?php echo ($page == 'pppoe_secrets') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-key mr-2"></i> PPPoE Secret
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=pppoe_data" class="block p-2 rounded hover:text-primary <?php echo ($page == 'pppoe_data') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-database mr-2"></i> Data Pelanggan PPPoE
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Pelanggan Menu -->
                    <li x-data="{ open: <?php echo (strpos($page, 'customer_') !== false) ? 'true' : 'false'; ?> }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-2 rounded hover:bg-gray-700 transition <?php echo (strpos($page, 'customer_') !== false) ? 'bg-gray-700 text-primary' : ''; ?>">
                            <div class="flex items-center">
                                <i class="fas fa-users w-6"></i> Pelanggan
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open" x-cloak x-transition class="mt-2 space-y-1 px-6 text-sm">
                            <li>
                                <a href="index.php?page=customer_list&type=all" class="block p-2 rounded hover:text-primary <?php echo ($page == 'customer_list' && (!isset($_GET['type']) || $_GET['type'] == 'all')) ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-list mr-2"></i> Semua Pelanggan
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=customer_list&type=MEMBER" class="block p-2 rounded hover:text-primary <?php echo ($page == 'customer_list' && isset($_GET['type']) && $_GET['type'] == 'MEMBER') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-wifi mr-2"></i> Hotspot (Member)
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=customer_list&type=PERUMAHAN" class="block p-2 rounded hover:text-primary <?php echo ($page == 'customer_list' && isset($_GET['type']) && $_GET['type'] == 'PERUMAHAN') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-home mr-2"></i> PPPoE (Perumahan)
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=customer_list&type=RESELLER" class="block p-2 rounded hover:text-primary <?php echo ($page == 'customer_list' && isset($_GET['type']) && $_GET['type'] == 'RESELLER') ? 'text-primary' : 'text-gray-400'; ?>">
                                    <i class="fas fa-user-tie mr-2"></i> Reseller
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="index.php?page=billing" class="flex items-center p-2 rounded hover:bg-gray-700 transition <?php echo ($page == 'billing') ? 'bg-gray-700 text-primary' : ''; ?>">
                            <i class="fas fa-money-bill-wave w-6"></i> Billing & Sales
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=settings" class="flex items-center p-2 rounded hover:bg-gray-700 transition <?php echo ($page == 'settings') ? 'bg-gray-700 text-primary' : ''; ?>">
                            <i class="fas fa-cog w-6"></i> Settings
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="p-4 border-t border-gray-700 text-center text-xs text-gray-400">
                &copy; 2024 HOT POT Manager
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow">
                <div class="px-6 py-4 flex justify-between items-center">
                    <button class="md:hidden text-gray-600 focus:outline-none">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800 capitalize"><?php echo ucfirst($page); ?></h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Admin</span>
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold">A</div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <?php echo format_flash_message(get_flash_message()); ?>

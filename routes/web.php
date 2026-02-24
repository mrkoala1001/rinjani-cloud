<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VoucherOnlineController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PppoeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\SettingController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotSupportController;
use App\Http\Controllers\ResellerController;

// --------------------------------------------------------------------------
// DPOTCOM.COM - MAIN LANDING PAGE
// --------------------------------------------------------------------------
Route::domain('depootcom.com')->group(function () {
    Route::get('/', function() {
        \Illuminate\Support\Facades\Log::info('Depootcom Landing Closure hit');
        $services = [
            ['title' => 'Hardware Solutions', 'description' => 'Penyediaan dan instalasi perangkat keras mulai dari printer, laptop, cctv, hingga perangkat kasir pintar.', 'icon' => 'microchip'],
            ['title' => 'Software & Development', 'description' => 'Pembuatan sistem berbasis web, mobile app, hingga integrasi API untuk otomatisasi bisnis Anda.', 'icon' => 'code'],
            ['title' => 'Networking', 'description' => 'Melayani jasa konfigurasi internet seperti mikrotik dan perangkat jaringan lainnya.', 'icon' => 'network-wired']
        ];
        $projects = [
            ['name' => 'Rinsride', 'description' => 'Layanan rental motor modern dengan sistem manajemen armada yang terintegrasi.', 'url' => 'https://rinsride.com', 'tag' => 'Automotive Solution'],
            ['name' => 'Hotpot Management', 'description' => 'Solusi manajemen hotspot dan billing otomatis untuk ISP, Cafe, dan RT-RW Net.', 'url' => 'http://hotpot.depootcom.com', 'tag' => 'Network Management']
        ];
        return view('depootcom.index', compact('services', 'projects'));
    })->name('depootcom.landing');
    Route::post('/contact', [\App\Http\Controllers\Depootcom\LandingController::class, 'sendContact'])->name('depootcom.contact.send');
    
    // Blog Frontend
    Route::get('/blog', [\App\Http\Controllers\Depootcom\BlogController::class, 'index'])->name('depootcom.blog.index');
    Route::get('/blog/{slug}', [\App\Http\Controllers\Depootcom\BlogController::class, 'show'])->name('depootcom.blog.show');
    Route::get('/sitemap.xml', [\App\Http\Controllers\Depootcom\SitemapController::class, 'index'])->name('depootcom.sitemap');

    // Admin Access via /login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('depootcom.login');
    Route::post('/login', [AuthController::class, 'login'])->name('depootcom.login.post');
    Route::get('/blog/login', [AuthController::class, 'showBlogLogin'])->name('depootcom.blog.login');
    
    // Admin Dashboard (Role: builder)
    Route::middleware(['auth', 'role:builder'])->prefix('admin')->name('depootcom.admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Depootcom\Admin\BlogController::class, 'dashboard'])->name('dashboard');
        Route::resource('blog', \App\Http\Controllers\Depootcom\Admin\BlogController::class);
        Route::post('blog/generate-ai', [\App\Http\Controllers\Depootcom\Admin\BlogController::class, 'generateAI'])->name('blog.generate-ai');
        Route::resource('categories', \App\Http\Controllers\Depootcom\Admin\CategoryController::class);
        Route::post('upload-image', [\App\Http\Controllers\Depootcom\Admin\ImageUploadController::class, 'upload'])->name('upload-image');
    });
});

Route::domain('www.depootcom.com')->group(function () {
    Route::get('/', function() { return redirect()->route('depootcom.landing'); });
});

// --------------------------------------------------------------------------
// HOTPOT.DEPOOTCOM.COM - APPLICATION (HOTPOT)
// --------------------------------------------------------------------------
Route::domain('hotpot.depootcom.com')->group(function () {
    
    // PWA Manifest for Owner
    Route::get('/manifest-owner.json', function() {
        return response()->json([
            "name" => "HOT POT Manager",
            "short_name" => "HotPot",
            "description" => "Panel Manajemen Hotspot (Owner)",
            "start_url" => "/dashboard",
            "scope" => "/", 
            "display" => "standalone",
            "background_color" => "#4f46e5",
            "theme_color" => "#4f46e5",
            "orientation" => "portrait",
            "icons" => [
                [
                    "src" => "/img/icon-512.png",
                    "sizes" => "512x512",
                    "type" => "image/png",
                    "purpose" => "any maskable"
                ]
            ]
        ]);
    });

    // PWA Manifest for Reseller
    Route::get('/manifest-reseller.json', function() {
        return response()->json([
            "name" => "HOT POT Reseller",
            "short_name" => "Reseller App",
            "description" => "Aplikasi Reseller Hotspot",
            "start_url" => "/app/dashboard",
            "scope" => "/app/",
            "display" => "standalone",
            "background_color" => "#4f46e5",
            "theme_color" => "#4f46e5",
            "orientation" => "portrait",
            "icons" => [
                [
                    "src" => "/img/icon-512.png",
                    "sizes" => "512x512",
                    "type" => "image/png",
                    "purpose" => "any maskable"
                ]
            ]
        ]);
    });
    // Public Landing Page (Old/Legacy if needed)
    Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('landing');
    Route::get('/dokumentasi', [\App\Http\Controllers\LandingController::class, 'documentation'])->name('documentation.index');
    Route::get('/dokumentasi/{slug}', [\App\Http\Controllers\LandingController::class, 'showDocumentation'])->name('documentation.show');
    Route::post('/comment', [\App\Http\Controllers\LandingController::class, 'storeComment'])->name('comment.store');

    // Depootcom Landing Page Internal Access
    Route::get('/depootcom', [\App\Http\Controllers\Depootcom\LandingController::class, 'index'])->name('depootcom.landing.internal');

    // Public Authentication Routes
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout']); // Fallback for easier logout

    // Authenticated Routes
    Route::middleware('auth')->group(function () {
        
        // ISP / HOT SUPPORT Routes
        Route::middleware('role:isp')->prefix('hotsupport')->name('hotsupport.')->group(function () {
             Route::get('/', [HotSupportController::class, 'index'])->name('dashboard');
             Route::get('/impersonate/{id}', [HotSupportController::class, 'impersonate'])->name('impersonate');
             
             // Owner / Mitra Management
             Route::get('/owner/create', [HotSupportController::class, 'createOwner'])->name('owner.create');
             Route::post('/owner', [HotSupportController::class, 'storeOwner'])->name('owner.store');
             Route::get('/owner/{id}/edit', [HotSupportController::class, 'editOwner'])->name('owner.edit');
             Route::put('/owner/{id}', [HotSupportController::class, 'updateOwner'])->name('owner.update');
             Route::get('/owner/{id}', [HotSupportController::class, 'show'])->name('owner.show');
             Route::delete('/owner/{id}', [HotSupportController::class, 'destroyOwner'])->name('owner.destroy');
             
             // Router Management (Admin side)
             Route::get('/owner/{id}/router/create', [HotSupportController::class, 'createRouter'])->name('router.create');
             Route::post('/owner/{id}/router', [HotSupportController::class, 'storeRouter'])->name('router.store');
             Route::get('/router/{id}/edit', [HotSupportController::class, 'editRouter'])->name('router.edit');
             Route::put('/router/{id}', [HotSupportController::class, 'updateRouter'])->name('router.update');
             Route::delete('/router/{id}', [HotSupportController::class, 'destroyRouter'])->name('router.destroy');

             // Report & Ticketing System
             Route::get('/tickets', [HotSupportController::class, 'ticketIndex'])->name('tickets.index');
             Route::get('/tickets/{id}', [HotSupportController::class, 'ticketShow'])->name('tickets.show');
             Route::get('/tickets/create', [HotSupportController::class, 'reportForm'])->name('tickets.create'); // Alias for report.form
             Route::post('/tickets', [HotSupportController::class, 'sendReport'])->name('tickets.store'); // Alias for report.send

             // Keep old routes for compatibility but redirect logic is changed in controller
             Route::get('/report', [HotSupportController::class, 'reportForm'])->name('report.form');
             Route::post('/report', [HotSupportController::class, 'sendReport'])->name('report.send');
        });

        // Shared / Impersonation Exit
        Route::get('/impersonate/leave', [HotSupportController::class, 'leaveImpersonation'])->name('impersonate.leave');

        // Owner / HOT POT Routes
        Route::middleware('role:owner')->group(function () {
            // Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            
            // Voucher Routes
            Route::prefix('voucher')->name('voucher.')->group(function () {
                Route::get('/', [VoucherController::class, 'index'])->name('index');
                Route::get('/online', [VoucherOnlineController::class, 'index'])->name('online');
                Route::get('/online/kick', [VoucherOnlineController::class, 'kick'])->name('kick');
                Route::get('/generate', [VoucherController::class, 'generate'])->name('generate');
                Route::post('/generate', [VoucherController::class, 'store'])->name('store');
                Route::get('/list', [VoucherController::class, 'list'])->name('list');
                Route::post('/update', [VoucherController::class, 'updateUser'])->name('updateUser');
                Route::get('/delete/{id}', [VoucherController::class, 'delete'])->name('delete'); 

                Route::get('/profiles', [VoucherController::class, 'profiles'])->name('profiles');
                Route::post('/profiles', [VoucherController::class, 'storeProfile'])->name('storeProfile');
                Route::post('/profiles/update', [VoucherController::class, 'updateProfile'])->name('updateProfile');
                Route::get('/profiles/delete', [VoucherController::class, 'deleteProfile'])->name('deleteProfile');
                
                Route::get('/sold', [VoucherController::class, 'sold'])->name('sold');
                Route::get('/sold/export', [VoucherController::class, 'exportSold'])->name('sold.export');
                
                Route::get('/distribution', [VoucherController::class, 'distribution'])->name('distribution');
                Route::get('/print/{batchId}', [VoucherController::class, 'printBatch'])->name('printBatch');
                
                // Batch actions
                Route::get('/batch/{batchId}/view', [VoucherController::class, 'viewBatch'])->name('viewBatch');
                Route::delete('/batch/{batchId}', [VoucherController::class, 'deleteBatch'])->name('deleteBatch');
                
                // Distribution actions
                Route::get('/distribution/view', [VoucherController::class, 'viewDistribution'])->name('viewDistribution');
                Route::delete('/distribution/delete', [VoucherController::class, 'deleteDistribution'])->name('deleteDistribution');
                Route::post('/distribution/mark-paid', [VoucherController::class, 'markPaid'])->name('markPaid');
                
                Route::get('/templates', [VoucherController::class, 'templates'])->name('templates');
                Route::post('/templates', [VoucherController::class, 'storeTemplate'])->name('storeTemplate');
                Route::get('/templates/delete/{id}', [VoucherController::class, 'deleteTemplate'])->name('deleteTemplate');
            });

            // PPPoE Routes
            Route::prefix('pppoe')->name('pppoe.')->group(function () {
                Route::get('/active', [PppoeController::class, 'active'])->name('active');
                Route::get('/active/kick/{id}', [PppoeController::class, 'kickActive'])->name('active.kick');
                
                Route::get('/profiles', [PppoeController::class, 'profiles'])->name('profiles');
                Route::post('/profiles', [PppoeController::class, 'storeProfile'])->name('storeProfile');
                Route::post('/profiles/update', [PppoeController::class, 'updateProfile'])->name('updateProfile');
                Route::get('/profiles/delete/{id}', [PppoeController::class, 'deleteProfile'])->name('deleteProfile');
                
                Route::get('/secrets', [PppoeController::class, 'secrets'])->name('secrets');
                Route::post('/secrets', [PppoeController::class, 'storeSecret'])->name('storeSecret');
                Route::post('/secrets/update', [PppoeController::class, 'updateSecret'])->name('updateSecret');
                Route::get('/secrets/delete/{id}', [PppoeController::class, 'deleteSecret'])->name('deleteSecret');
            });

            // Customer Routes
            Route::prefix('customer')->name('customer.')->group(function () {
                Route::get('/list/{type?}', [CustomerController::class, 'list'])->name('list');
                Route::post('/store', [CustomerController::class, 'store'])->name('store');
                Route::get('/delete/{id}', [CustomerController::class, 'delete'])->name('delete');
            });

            // Billing & Settings
            Route::prefix('billing')->name('billing.')->group(function () {
                Route::get('/', [BillingController::class, 'index'])->name('index');
                Route::get('/monitor', [BillingController::class, 'monitor'])->name('monitor');
                Route::get('/income', [BillingController::class, 'income'])->name('income');
                Route::get('/income/sync', [BillingController::class, 'sync'])->name('syncIncome');
                Route::post('/income', [BillingController::class, 'storeIncome'])->name('storeIncome');
                Route::post('/income/update', [BillingController::class, 'updateIncome'])->name('updateIncome');
                Route::delete('/income/{id}', [BillingController::class, 'deleteIncome'])->name('deleteIncome');
                Route::get('/income/print/{id}', [BillingController::class, 'printIncome'])->name('printIncome');
                
                Route::get('/expense', [BillingController::class, 'expenses'])->name('expense');
                Route::post('/expense', [BillingController::class, 'storeExpense'])->name('storeExpense');
                Route::get('/expense/delete/{id}', [BillingController::class, 'deleteExpense'])->name('deleteExpense');
                
                Route::get('/debt', [BillingController::class, 'debts'])->name('debt');
                Route::post('/debt', [BillingController::class, 'storeDebt'])->name('storeDebt');
                Route::get('/debt/delete/{id}', [BillingController::class, 'deleteDebt'])->name('deleteDebt');
            });

            // Reseller Management (by Owner)
            Route::prefix('reseller')->name('owner.reseller.')->group(function () {
                Route::get('/', [ResellerController::class, 'list'])->name('index');
                Route::get('/create', [ResellerController::class, 'create'])->name('create');
                Route::post('/', [ResellerController::class, 'store'])->name('store');
                Route::get('/manage-balance', [ResellerController::class, 'manageBalance'])->name('balance');
                Route::post('/balance', [ResellerController::class, 'addBalance'])->name('addBalance');
                Route::get('/{id}/edit', [ResellerController::class, 'edit'])->name('edit');
                Route::put('/{id}', [ResellerController::class, 'update'])->name('update');
                Route::delete('/{id}', [ResellerController::class, 'destroy'])->name('destroy');
            });

            // Settings
            Route::get('/settings', [SettingController::class, 'index'])->name('settings');
            Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
            Route::post('/settings/test', [SettingController::class, 'testConnection'])->name('settings.test');
            Route::post('/settings/disconnect', [SettingController::class, 'disconnect'])->name('settings.disconnect');
            Route::post('/settings/wipe', [SettingController::class, 'wipeData'])->name('settings.wipe');
        });

        // Reseller Dashboard
        Route::middleware('role:reseller')->prefix('reseller')->name('reseller.')->group(function () {
            Route::get('/dashboard', [ResellerController::class, 'index'])->name('dashboard');
            Route::get('/vouchers', [ResellerController::class, 'soldVouchers'])->name('vouchers');
            Route::get('/generate', [ResellerController::class, 'generateVoucher'])->name('generate');
            Route::get('/wallet', [ResellerController::class, 'balance'])->name('balance');
            Route::get('/distribution', [ResellerController::class, 'distribution'])->name('distribution');
        });

        // Builder Route
        Route::middleware('role:builder')->prefix('builder')->name('builder.')->group(function () {
             Route::get('/', [\App\Http\Controllers\BuilderController::class, 'index'])->name('dashboard');
              Route::get('/create-user', [\App\Http\Controllers\BuilderController::class, 'createUser'])->name('user.create');
              Route::post('/create-user', [\App\Http\Controllers\BuilderController::class, 'storeUser'])->name('user.store');
             Route::get('/reports', [\App\Http\Controllers\BuilderController::class, 'reports'])->name('reports');
             Route::get('/reports/{id}', [\App\Http\Controllers\BuilderController::class, 'viewReport'])->name('reports.show');
             Route::get('/impersonate/{id}', [\App\Http\Controllers\BuilderController::class, 'impersonate'])->name('impersonate');
             Route::get('/impersonate/p3pot/{id}', [\App\Http\Controllers\BuilderController::class, 'impersonateP3pot'])->name('impersonate.p3pot');
             Route::get('/user/{id}/edit', [\App\Http\Controllers\BuilderController::class, 'editUser'])->name('user.edit');
             Route::put('/user/{id}', [\App\Http\Controllers\BuilderController::class, 'updateUser'])->name('user.update');
             Route::delete('/user/{id}', [\App\Http\Controllers\BuilderController::class, 'deleteUser'])->name('user.destroy');
             Route::delete('/user/p3pot/{id}', [\App\Http\Controllers\BuilderController::class, 'deleteP3potUser'])->name('user.p3pot.destroy');
             Route::post('/reports/{id}/read', [\App\Http\Controllers\BuilderController::class, 'markAsRead'])->name('reports.read');
             Route::post('/reports/{id}/reply', [\App\Http\Controllers\BuilderController::class, 'replyTicket'])->name('reports.reply');
             Route::post('/reports/{id}/status', [\App\Http\Controllers\BuilderController::class, 'updateStatus'])->name('reports.status');
             Route::delete('/reports/{id}', [\App\Http\Controllers\BuilderController::class, 'deleteReport'])->name('reports.delete');
             
             // Broadcast Routes
             Route::post('/broadcast', [\App\Http\Controllers\BuilderController::class, 'storeBroadcast'])->name('broadcast.store');
             Route::get('/broadcast/delete/{id}', [\App\Http\Controllers\BuilderController::class, 'deleteBroadcast'])->name('broadcast.delete');

             // Content Management Routes
             Route::resource('documentation', \App\Http\Controllers\Builder\DocumentationController::class);
             Route::resource('comments', \App\Http\Controllers\Builder\CommentController::class)->only(['index', 'destroy']);
             Route::post('comments/{id}/approve', [\App\Http\Controllers\Builder\CommentController::class, 'approve'])->name('comments.approve');
             Route::resource('faqs', \App\Http\Controllers\Builder\FaqController::class)->except(['show']);
             Route::resource('templates', \App\Http\Controllers\Builder\TemplateController::class);
        });

        // Koala Documentation
        Route::get('/koala-bertanya', [\App\Http\Controllers\KoalaController::class, 'index'])->name('koala.index');
        Route::get('/koala-bertanya/{path}', [\App\Http\Controllers\KoalaController::class, 'show'])->where('path', '.*')->name('koala.show');

        // General Report Form (Available for all roles)
        Route::get('/report', [\App\Http\Controllers\DashboardController::class, 'reportForm'])->name('report.form');
        Route::post('/report', [\App\Http\Controllers\DashboardController::class, 'sendReport'])->name('report.send');
        
        // Owner Report Management (New)
        Route::get('/reports', [\App\Http\Controllers\DashboardController::class, 'reports'])->name('report.index');
        Route::get('/reports/{id}', [\App\Http\Controllers\DashboardController::class, 'viewReport'])->name('report.show')->where('id', '[0-9]+');
        
        // Broadcast Read (New)
        Route::post('/broadcast/read/{id}', [\App\Http\Controllers\DashboardController::class, 'markBroadcastAsRead'])->name('broadcast.read');
    });
});

// --------------------------------------------------------------------------
// CUSTOMER APP ROUTES (FOR CLIENT LOGIN)
// --------------------------------------------------------------------------
Route::prefix('app')->group(function () {
    Route::get('/login', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'showLogin'])->name('customer_app.login');
    Route::post('/login', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'login'])->name('customer_app.login.post');
    Route::post('/logout', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'logout'])->name('customer_app.logout');
    Route::get('/logout', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'logout']);

    Route::middleware('customer_app')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'index'])->name('customer_app.dashboard');
        
        // Reseller Special Routes
        Route::get('/vouchers', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'vouchers'])->name('customer_app.reseller.vouchers');
        Route::post('/vouchers/generate', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'generateVoucher'])->name('customer_app.reseller.generate');
        Route::get('/distribution', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'distribution'])->name('customer_app.reseller.distribution');
        Route::get('/distribution/batch/{batchId}', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'viewBatch'])->name('customer_app.reseller.batch.view');
        Route::delete('/distribution/batch/{batchId}', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'deleteBatch'])->name('customer_app.reseller.batch.delete');
        Route::get('/active-users', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'activeUsers'])->name('customer_app.reseller.active');
        Route::get('/transactions', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'transactions'])->name('customer_app.reseller.transactions');
        Route::get('/profile', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'profile'])->name('customer_app.reseller.profile');
        Route::post('/profile', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'updateProfile'])->name('customer_app.reseller.profile.update');
        Route::get('/balance-history', [\App\Http\Controllers\CustomerApp\DashboardController::class, 'balanceLogs'])->name('customer_app.reseller.balance_logs');
        Route::get('/topup', [\App\Http\Controllers\CustomerApp\TopupController::class, 'index'])->name('customer_app.reseller.topup');
        Route::post('/topup', [\App\Http\Controllers\CustomerApp\TopupController::class, 'store'])->name('customer_app.reseller.topup.store');
        Route::get('/topup/finish', [\App\Http\Controllers\CustomerApp\TopupController::class, 'finish'])->name('customer_app.reseller.topup.finish');
    });
});

// Pakasir Callback (Must be outside auth and excluded from CSRF)
Route::post('/pakasir/callback', [\App\Http\Controllers\CustomerApp\TopupController::class, 'callback'])->name('pakasir.callback');

Route::domain('www.hotpot.depootcom.com')->group(function () {
    Route::get('/{any?}', function($any = '') { 
        return redirect('http://hotpot.depootcom.com/' . $any); 
    })->where('any', '.*');
});

// --------------------------------------------------------------------------
// P3POT.DEPOOTCOM.COM - P3POT RADIUS BILLING
// --------------------------------------------------------------------------
Route::domain('p3pot.depootcom.com')->group(function () {
    Route::get('/', function() {
        return view('p3pot.index');
    })->name('p3pot.landing');

    // Auth Routes
    Route::get('/login', [\App\Http\Controllers\P3pot\AuthController::class, 'showLogin'])->name('p3pot.login');
    Route::post('/login', [\App\Http\Controllers\P3pot\AuthController::class, 'login'])->name('p3pot.login.post');
    Route::post('/logout', [\App\Http\Controllers\P3pot\AuthController::class, 'logout'])->name('p3pot.logout');

    // Authenticated P3POT Routes
    Route::middleware('auth:web,p3pot')->group(function () {
        
        // Owner Routes
        Route::prefix('owner')->name('p3pot.owner.')->group(function() {
            Route::get('/dashboard', [\App\Http\Controllers\P3pot\OwnerController::class, 'index'])->name('dashboard');
            Route::get('/pppoe', [\App\Http\Controllers\P3pot\OwnerController::class, 'pppoe'])->name('pppoe');
            Route::get('/payment-gateway', [\App\Http\Controllers\P3pot\OwnerController::class, 'paymentGateway'])->name('payment_gateway');
            Route::post('/payment-gateway', [\App\Http\Controllers\P3pot\OwnerController::class, 'storePaymentGateway'])->name('payment_gateway.store');
            
            Route::get('/customers', [\App\Http\Controllers\P3pot\OwnerController::class, 'customers'])->name('customers');
            Route::get('/billing', [\App\Http\Controllers\P3pot\OwnerController::class, 'billing'])->name('billing');
            Route::get('/settings', [\App\Http\Controllers\P3pot\OwnerController::class, 'settings'])->name('settings');
            Route::get('/reports', [\App\Http\Controllers\P3pot\OwnerController::class, 'reports'])->name('reports');
            Route::post('/send-report', [\App\Http\Controllers\P3pot\OwnerController::class, 'sendReport'])->name('send_report');
        });

        // Customer Routes
        Route::prefix('customer')->name('p3pot.customer.')->group(function() {
            Route::get('/dashboard', [\App\Http\Controllers\P3pot\CustomerController::class, 'index'])->name('dashboard');
        });
    });
});

// --------------------------------------------------------------------------
// TELEGRAM BOT WEBHOOK
// --------------------------------------------------------------------------
Route::post('/telegram/webhook', [\App\Http\Controllers\TelegramBotController::class, 'handleWebhook'])->name('telegram.webhook');

// --------------------------------------------------------------------------
// API ROUTES FOR CUSTOMER APP
// --------------------------------------------------------------------------
Route::prefix('api/customer')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\CustomerAuthController::class, 'login']);
});

// --------------------------------------------------------------------------
// FALLBACK ROUTE - Handle IP access or unknown domains
// --------------------------------------------------------------------------
Route::fallback(function () {
    return redirect()->route('depootcom.landing');
});

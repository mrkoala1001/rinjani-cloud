<?php

namespace App\Helpers;

class PlanHelper
{
    public static function getPlanConfig($plan)
    {
        $configs = [
            'basic' => [
                'name' => 'Basic',
                'color' => 'blue',
                'price' => 'Free / 3 Bulan',
                'menus' => [
                    'dashboard', 'report_to_mr_koala', 'vouchers.generate', 'vouchers.distribution', 
                    'vouchers.online', 'pppoe.active', 'settings', 'my_reports', 'my_profile'
                ],
                'quotas' => [
                    'voucher_generate_max' => 50,
                    'voucher_distribution_max' => 1000,
                    'voucher_online_max' => 50,
                    'pppoe_active_max' => 50,
                    'customer_max' => 0,
                ]
            ],
            'medium' => [
                'name' => 'Medium',
                'color' => 'orange',
                'price' => 'Rp 40.000 / Bulan',
                'menus' => [
                    'dashboard', 'report_to_mr_koala', 'vouchers.generate', 'vouchers.distribution', 
                    'vouchers.online', 'vouchers.list', 'vouchers.profiles', 'vouchers.sold', 'vouchers.templates',
                    'pppoe.active', 'pppoe.profiles', 'pppoe.secrets', 'wan_static', 'customer', 
                    'wa_gateway', 'settings', 'my_reports', 'my_profile'
                ],
                'quotas' => [
                    'voucher_generate_max' => 50,
                    'voucher_distribution_max' => 2000,
                    'voucher_online_max' => 50,
                    'pppoe_active_max' => 50,
                    'customer_max' => 300,
                ]
            ],
            'pro' => [
                'name' => 'Pro',
                'color' => 'green',
                'price' => 'Rp 75.000 / Bulan',
                'menus' => '*', // All menus
                'quotas' => [
                    'voucher_generate_max' => 200,
                    'voucher_distribution_max' => -1, // Unlimited
                    'voucher_online_max' => -1,
                    'pppoe_active_max' => -1,
                    'customer_max' => -1,
                ]
            ],
            'isp' => [
                'name' => 'ISP',
                'color' => 'purple',
                'price' => 'Custom',
                'menus' => '*',
                'quotas' => [
                    'voucher_generate_max' => -1,
                    'voucher_distribution_max' => -1,
                    'voucher_online_max' => -1,
                    'pppoe_active_max' => -1,
                    'customer_max' => -1,
                ]
            ]
        ];

        return $configs[$plan] ?? $configs['basic'];
    }

    public static function canAccess($menu, $user = null)
    {
        $user = $user ?? auth()->user();
        if (!$user) return false;
        
        // Builder and ISP role always have full access
        if (in_array($user->role, ['builder', 'isp'])) return true;

        $plan = $user->plan ?? 'basic';

        // Check if plan is expired (except for basic)
        if ($plan !== 'basic' && (!isset($user->plan_expires_at) || $user->plan_expires_at->isPast())) {
            $plan = 'basic';
        }

        $config = self::getPlanConfig($plan);

        if ($config['menus'] === '*') return true;

        return in_array($menu, $config['menus']);
    }
}

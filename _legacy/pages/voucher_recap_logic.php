<?php
// pages/voucher_recap_logic.php

// Group by Date
// Group by Date - Synchronized with Dashboard & Voucher Sold
$query = "SELECT DATE(bh.date_sold) as sale_date, COUNT(*) as qty, SUM(COALESCE(pm.price, bh.price)) as total_income 
          FROM billing_history bh
          LEFT JOIN hotspot_profile_metadata pm ON bh.profile = pm.profile_name
          GROUP BY DATE(bh.date_sold) 
          ORDER BY sale_date DESC LIMIT 30";
$recap_data = $conn->query($query);

// Fetch MikroTik Scripts (Reports)
$mikrotik_reports = [];
$config = get_mikrotik_config($conn);
if ($config) {
    // Re-use existing API connection or create new if not connected
    if (!isset($API) || !$API) {
        $API = new RouterOS\Client();
        $API->connect($config['host'], $config['user'], $config['pass']);
    }
    
        if ($API) {
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;

        // Use query to filter scripts by comment 'mikhmon' AND owner by year to reduce payload
        // API Logical Query: (comment=mikhmon AND owner~Year)
        $API->write('/system/script/print', false);
        $API->write('?comment=mikhmon', false);
        $API->write('?owner~' . $currentYear, false);
        $API->write('?#&', false); // AND operator
        
        // Optimize: Limit returned columns
        $API->write('=.proplist=name,source,owner,last-started,comment');
        
        $scripts = $API->read();
        
        // User requested "1 data aja" (Just 1 item) for performance debugging
        // We slice the array to 1 item immediately
        if (!empty($scripts)) {
           $scripts = array_slice($scripts, 0, 1);
        }
        
        // Initialize groups
        $mikrotik_reports = [
            $currentYear => [],
            $lastYear => []
        ];

        foreach ($scripts as $script) {
            $owner = $script['owner'] ?? '';
            $yearGroup = '';

            // Check if owner ends with current or last year
            if (str_ends_with($owner, $currentYear)) {
                $yearGroup = $currentYear;
            } elseif (str_ends_with($owner, $lastYear)) {
                $yearGroup = $lastYear;
            }

            if ($yearGroup) {
                $mikrotik_reports[$yearGroup][] = [
                    'name' => $script['name'] ?? '-',
                    'owner' => $owner,
                    'source' => $script['source'] ?? '',
                    'last_started' => $script['last-started'] ?? '-'
                ];
            }
        }
    }
}
?>

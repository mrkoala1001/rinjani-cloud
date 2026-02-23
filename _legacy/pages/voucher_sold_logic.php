<?php
// pages/voucher_sold_logic.php

// Voucher Terjual is basically Billing History filtered or simply just the list from billing_history since it tracks vouchers.
$query = "SELECT bh.*, r.name as reseller_name, pm.price as current_meta_price
          FROM billing_history bh 
          LEFT JOIN resellers r ON bh.reseller_id = r.id 
          LEFT JOIN hotspot_profile_metadata pm ON bh.profile = pm.profile_name
          ORDER BY bh.date_sold DESC LIMIT 100";
// Fetch sold vouchers (limited to last 100)
$res = $conn->query($query);
$vouchers_data = [];
while($row = $res->fetch_assoc()) {
    $vouchers_data[] = $row;
}

// Calculate All-Time Total Income using Current Profile Prices (Synchronized with Dashboard & Recap)
$total_grand_query = "SELECT SUM(COALESCE(pm.price, bh.price)) as total 
                      FROM billing_history bh
                      LEFT JOIN hotspot_profile_metadata pm ON bh.profile = pm.profile_name";
$total_grand_res = $conn->query($total_grand_query);
$total_grand_income = $total_grand_res->fetch_assoc()['total'] ?? 0;
?>

<?php
// pages/pppoe_data_logic.php

$query = "SELECT * FROM pppoe_customers ORDER BY username ASC";
$pppoe_customers = $conn->query($query);
?>

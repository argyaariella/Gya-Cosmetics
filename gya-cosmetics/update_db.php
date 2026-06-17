<?php
require 'config/config.php';
$conn->query("ALTER TABLE transaksi ADD COLUMN promo_id INT(11) NULL AFTER user_id");
$conn->query("ALTER TABLE transaksi ADD COLUMN diskon DECIMAL(15,2) DEFAULT '0.00' AFTER total_harga");
echo "DB Updated!";
?>

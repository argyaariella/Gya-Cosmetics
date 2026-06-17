<?php
require 'config/config.php';

echo "Table: transaksi\n";
$res = $conn->query("DESCRIBE transaksi");
while($row = $res->fetch_assoc()) echo json_encode($row) . "\n";

echo "\nTable: promo\n";
$res = $conn->query("DESCRIBE promo");
while($row = $res->fetch_assoc()) echo json_encode($row) . "\n";
?>

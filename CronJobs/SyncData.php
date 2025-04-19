<?php
require_once __DIR__ . '/../class/HtzoneApi.php';

$api = new HtzoneApi();

$api->fetchAndStoreCategories();
$api->fetchAndStoreItems();

echo "[" . date('Y-m-d H:i:s') . "] Sync completed.\n";

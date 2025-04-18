<?php


require_once __DIR__ . '/../class/HtzoneApi.php';
require_once __DIR__ . '/../class/Category/CategoryService.php';

try {
    $api = new HtzoneApi();
    $api->fetchAndStoreCategories();

    echo "Categories fetched and stored.\n\n";

    $categoryService = new CategoryService();
    $categories = $categoryService->getCategories();

    foreach ($categories as $cat) {
        echo "ID: {$cat->getId()} | Name: {$cat->getName()} | Description: {$cat->getDescription()}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

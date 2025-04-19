<?php


require_once __DIR__ . '/../class/HtzoneApi.php';
require_once __DIR__ . '/../class/Category/CategoryService.php';
//this class is for fetching updated data to the db from api for the first time
// and its not part of the task
// if it was required to update db i would do it one time and use cache
try {
    $api = new HtzoneApi();
    $api->fetchAndStoreItems();

    echo "items fetched and stored.\n\n";

    $itemService = new ItemService();
    $items = $itemService->getItems();

    foreach ($items as $item) {
        echo "ID: {$item->getId()} | Name: {$item->getName()} | Description: {$item->getDescription()} | Price: {$item->getPrice()} | Brand: {$item->getBrand()} | Category ID: {$item->getCategory()} | Image URL: {$item->getImage()} | Stock: {$item->getAvailableStock()}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

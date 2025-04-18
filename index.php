<?php
require_once 'class/Item/ItemService.php';
require_once __DIR__ . '/class/Database/Database.php';

$carouselCategories = [
    1043 => 'Tablets',
    1042 => 'Laptops',
    1554 => 'Smartphones'
];

$itemService = new ItemService();
$carouselItems = [];

foreach ($carouselCategories as $categoryId => $title) {
    $filters = ['category_id' => $categoryId];
    $sort = ['field' => 'name', 'direction' => 'ASC'];
    $items = $itemService->getItems($filters, $sort, 1)['items'] ?? [];
    $carouselItems[$categoryId] = array_slice($items, 0, 5);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTZone PHP Test</title>
    <link rel="stylesheet" href="static/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    <header>
        <h1>HTZone Sale</h1>
    </header>

    <main>
        <!-- Carousels Section -->
        <?php foreach ($carouselCategories as $categoryId => $title): ?>
            <section class="carousel-wrapper">
                <h2><?= htmlspecialchars($title) ?></h2>
                <div class="carousel-container">
                    <button class="carousel-button left" onclick="scrollCarousel(<?= $categoryId ?>, -1)">←</button>
                    <div class="carousel-items" id="carousel-<?= $categoryId ?>">
                        <?php foreach ($carouselItems[$categoryId] as $item): ?>
                            <div class="product-item">
                                <img class="product-image" src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                <h3><?= htmlspecialchars($item['name']) ?></h3>
                                <p class="brand"><?= htmlspecialchars($item['brand']) ?></p>
                                <p class="price">$<?= number_format($item['price'], 2) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-button right" onclick="scrollCarousel(<?= $categoryId ?>, 1)">→</button>
                </div>
            </section>
        <?php endforeach; ?>

        <!-- Filters Section -->
        <section class="filters-wrapper">
            <select id="category-filter">
                <option value="">All Categories</option>
            </select>
            <select id="sort-select">
                <option value="name-ASC">Name (A-Z)</option>
                <option value="name-DESC">Name (Z-A)</option>
                <option value="price-ASC">Price (Low to High)</option>
                <option value="price-DESC">Price (High to Low)</option>
            </select>
            <form id="price-filter">
                <input type="number" id="price-min" placeholder="Min Price">
                <input type="number" id="price-max" placeholder="Max Price">
                <button type="submit">Apply</button>
            </form>
        </section>

        <!-- Product List Section -->
        <section class="products-wrapper">
            <div id="product-list" class="grid-layout"></div>
            <div id="loading-indicator" style="display: none;">Loading...</div>
        </section>
    </main>
</div>

<script>
    function scrollCarousel(categoryId, direction) {
        const carousel = document.getElementById(`carousel-${categoryId}`);
        if (carousel) {
            carousel.scrollBy({
                left: direction * 250,
                behavior: 'smooth'
            });
        }
    }
</script>

<script src="static/js/scripts.js"></script>
</body>
</html>

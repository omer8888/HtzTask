

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
        <section id="carousel-section"></section>

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

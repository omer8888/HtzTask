let currentPage = 1;
const itemsPerPage = 10;
let isLoading = false;
let hasMoreItems = true;

function get_items(options = {}, append = false) {
    if (isLoading || (!append && !hasMoreItems)) return;

    isLoading = true;
    $('#loading-indicator').show();

    const params = {
        act: 'getItems',
        page: append ? currentPage : 1,
        limit: itemsPerPage,
        category: options.category || '',
        price_min: options.price_min || '',
        price_max: options.price_max || '',
        brand: options.brand || '',
        sort_field: options.sort_field || 'name',
        sort_direction: options.sort_direction || 'ASC'
    };

    console.log("AJAX Params:", params);

    $.ajax({
        url: 'ajax/ajax.php',
        method: 'POST',
        data: params,
        dataType: 'json',
        success: function(response) {
            console.log("AJAX Response:", response);

            if (response.status === 'success') {
                if (!response.data || !Array.isArray(response.data.items)) {
                    console.error('Invalid or missing "items" in response:', response.data);
                    return;
                }

                if (!append) {
                    $('#product-list').empty();
                    currentPage = 1;
                }

                if (response.data.items.length === 0) {
                    console.warn('No items returned.');
                    hasMoreItems = false;
                    return;
                }

                response.data.items.forEach(function(item, index) {
                    console.log(`Rendering item ${index + 1}:`, item);

                    const itemHtml = $('<div>').addClass('product-item').append(
                        $('<img>').addClass('product-image')
                            .attr('src', item.image_url || 'placeholder.jpg')
                            .attr('alt', item.name || 'No Name'),
                        $('<h3>').text(item.name || 'Unnamed'),
                        $('<p>').addClass('brand').text(item.brand || 'No Brand'),
                        $('<p>').addClass('price').text('$' + (parseFloat(item.price) || 0).toFixed(2))
                    );

                    if (item.category_id === 1) {
                        $('#carousel-1 .carousel-items').append(itemHtml);
                    } else if (item.category_id === 2) {
                        $('#carousel-2 .carousel-items').append(itemHtml);
                    } else if (item.category_id === 3) {
                        $('#carousel-3 .carousel-items').append(itemHtml);
                    } else {
                        $('#product-list').append(itemHtml);
                    }
                });

                hasMoreItems = response.data.items.length === itemsPerPage;
                if (hasMoreItems) currentPage++;
            } else {
                console.error('Error fetching items:', response.message);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX error:', textStatus, errorThrown);
        },
        complete: function() {
            isLoading = false;
            $('#loading-indicator').hide();
        }
    });
}

$(document).ready(function() {
    get_items();

    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 500) {
            get_items({}, true);
        }
    });

    $('#category-filter').on('change', function() {
        currentPage = 1;
        hasMoreItems = true;
        get_items({ category: $(this).val() });
    });

    $('#sort-select').on('change', function() {
        const [field, direction] = $(this).val().split('-');
        currentPage = 1;
        hasMoreItems = true;
        get_items({ sort_field: field, sort_direction: direction });
    });

    $('#price-filter').on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        hasMoreItems = true;
        get_items({
            price_min: $('#price-min').val(),
            price_max: $('#price-max').val()
        });
    });
});

function loadCategories() {
    $.ajax({
        url: 'ajax/ajax.php',
        method: 'POST',
        data: { act: 'getCategories' },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && Array.isArray(response.data.items)) {
                const $filter = $('#category-filter');
                $filter.empty().append('<option value="">All Categories</option>'); // Optional default

                response.data.items.forEach(function(category) {
                    $filter.append(
                        $('<option>').val(category.id).text(category.name)
                    );
                });
            } else {
                console.error('Failed to load categories:', response.message);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX error loading categories:', textStatus, errorThrown);
        }
    });
}

loadCategories();

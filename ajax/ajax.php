<?php
require_once '../class/Item/ItemService.php';
require_once '../class/Item/ItemEntity.php';
require_once '../class/Category/CategoryService.php';
require_once '../class/Database/Database.php';
require_once '../class/HtzoneApi.php';

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => '',
    'data' => null
];

if (!isset($_POST['act'])) {
    $response['message'] = 'No action specified';
    echo json_encode($response);
    exit;
}


switch ($_POST['act']) {
    case 'getItems':
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
        $filters = [
            'category_id' => $_POST['category'], // Fix key
            'min_price' => $_POST['price_min'],  // Fix key
            'max_price' => $_POST['price_max'],
            'brand' => $_POST['brand']
        ];

        $sort = [
            'field' => $_POST['sort_field'],
            'direction' => $_POST['sort_direction']
        ];

        $itemService = new ItemService();
        $data = $itemService->getItemsForDisplay($filters, $sort,$page);

        $response['status'] = 'success';
        $response['data'] = $data;
        break;

    case 'getCategories':
        $categoryService = new CategoryService();
        $data = $categoryService->getCategoriesForDisplay();

        $response['status'] = 'success';
        $response['data'] = $data;
        break;

    case 'getCarouselItems':
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
            $items = $itemService->getItemsForDisplay($filters, $sort, 1)['items'] ?? [];
            $carouselItems[] = [
                'category_id' => $categoryId,
                'title' => $title,
                'items' => array_slice($items, 0, 5)
            ];
        }

        $response['status'] = 'success';
        $response['data'] = $carouselItems;
        break;

    default:
        $response['message'] = 'Invalid action';
}


echo json_encode($response);

<?php
require_once '../class/Item/ItemService.php';
require_once '../class/Item/ItemEntity.php';
require_once '../class/Category/CategoryService.php';
require_once '../class/Database/Database.php';

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
        $data = $itemService->getItems($filters, $sort,$page);

        $response['status'] = 'success';
        $response['data'] = $data;
        break;

    case 'getCategories':
        //implement the getCategories method in Item class
        $categoryService = new CategoryService();
        $data = $categoryService->getCategories();

        $response['status'] = 'success';
        $response['data'] = $data;
        break;

    default:
        $response['message'] = 'Invalid action';
}


echo json_encode($response);

<?php

/**
 * Implement the HtzoneApi class to fetch item and category data from our API
 * Store the fetched data in the local SQLite database
 * Refer to api_doc.txt for detailed API documentation
 * Ensure proper error handling and data validation
 */
require_once __DIR__ . '/../class/Item/ItemService.php';
require_once __DIR__ . '/../class/Category/CategoryService.php';
require_once __DIR__ . '/../class/Item/ItemEntity.php';
require_once __DIR__ . '/../class/Category/CategoryService.php';
require_once __DIR__ . '/../class/Database/Database.php';

/**
 * This class is responsible for fetching data from the external API
 * and storing it in the local database.
 *
 * In a real-world scenario, this would typically be handled by a scheduled
 * background daemon (e.g., a cron job) that runs at regular intervals
 * to synchronize and update the database.
 *
 * For the purpose of this task, I’ve implemented two integration methods
 * that perform this process manually.
 */
class HtzoneApi
{
    /**
     * @var string
     */
    private $base_url = 'https://storeapi.htzone.co.il/ext/O2zfcVu2t8gOB6nzSfFBu4joDYPH7s';

    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * @var ItemService
     */
    private $itemService;

    private const HTTP_OK = 200;
    private const HTTP_BAD_REQUEST = 400;
    private const HTTP_UNAUTHORIZED = 401;
    private const HTTP_NOT_FOUND = 404;
    private const HTTP_TOO_MANY_REQUESTS = 429;
    private const HTTP_SERVER_ERROR = 500;

    /**
     * @param $url
     * @return array|mixed|null
     */
    private function makeApiRequest($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return null;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== self::HTTP_OK) {
            $this->handleHttpError($httpCode, $url);
            return null;
        }

        $decoded = json_decode($response, true);
        if (!isset($decoded['success']) || !$decoded['success']) return null;

        return $decoded['data'] ?? [];
    }

    /**
     * @param int $httpCode
     * @param string $url
     * @return void
     */
    private function handleHttpError($httpCode, $url)
    {
        switch ($httpCode) {
            case self::HTTP_BAD_REQUEST:
                error_log("400 Bad Request: $url");
                break;
            case self::HTTP_UNAUTHORIZED:
                error_log("401 Unauthorized: Invalid API key - $url");
                break;
            case self::HTTP_NOT_FOUND:
                error_log("404 Not Found: $url");
                break;
            case self::HTTP_TOO_MANY_REQUESTS:
                error_log("429 Too Many Requests: Rate limit exceeded - $url");
                break;
            case self::HTTP_SERVER_ERROR:
                error_log("500 Internal Server Error: $url");
                break;
            default:
                error_log("HTTP Error $httpCode: $url");
                break;
        }
    }

    /**
     * @return void
     */
    public function fetchAndStoreCategories()
    {
        $url = $this->base_url . '/categories';
        $categories = $this->makeApiRequest($url);

        if (!$categories) return;

        foreach ($categories as $cat) {
            if (!isset($cat['category_id']) || !isset($cat['title'])) {
                error_log('[HtzoneApi] Invalid category skipped: ' . print_r($cat, true));
                continue;
            }

            $category = new CategoryModel();
            $category->setId((int)$cat['category_id']);
            $category->setName($cat['title']);
            $category->setDescription($cat['parent_title'] ?? null);

            if (!$this->getCategoryService()->validateCategoryModel($category)) {
                error_log('[HtzoneApi] Validation failed for category: ' . print_r($cat, true));
                continue;
            }

            $this->getCategoryService()->addCategory($category);
        }
    }

    /**
     * @return void
     */
    public function fetchAndStoreItems()
    {
        $categories = $this->getCategoryService()->getCategories();
        foreach ($categories as $category) {
            $url = $this->base_url . '/items/' . $category->getId();
            $items = $this->makeApiRequest($url);

            if (!$items) continue;

            foreach ($items as $item) {

                $itemModel = (new ItemModel())
                    ->setId((int)$item['id'])
                    ->setName($item['title'])
                    ->setDescription(strip_tags($item['brief'] ?? ''))
                    ->setPrice((float)$item['price'])
                    ->setBrand($item['brand_id'] ?? null)
                    ->setCategory((int)($item['category_id'] ?? $category->getId()))
                    ->setImage($item['image'] ?? null)
                    ->setAvailableStock((int)($item['stock'] ?? 0));

                // validate before adding
                if (!$this->getItemService()->validateItemModel($itemModel)) {
                    error_log('[HtzoneApi] Invalid item skipped: ' . print_r($item, true));
                    continue;
                }

                $this->getItemService()->addItem($itemModel);
            }
        }
    }


    /**
     * @return CategoryService
     */
    protected function getCategoryService()
    {
        return $this->categoryService ?? $this->categoryService = new CategoryService();
    }

    /**
     * @return ItemService
     */
    protected function getItemService()
    {
        return $this->itemService ?? $this->itemService = new ItemService();
    }
}

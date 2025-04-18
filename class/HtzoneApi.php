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

        if ($httpCode !== 200) return null;

        $decoded = json_decode($response, true);
        if (!isset($decoded['success']) || !$decoded['success']) return null;

        return $decoded['data'] ?? [];
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
            if (!isset($cat['category_id']) || !isset($cat['title'])) continue;

            $category = new CategoryModel();
            $category->setId((int)$cat['category_id']);
            $category->setName($cat['title']);
            $category->setDescription($cat['parent_title'] ?? null);

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
                if (!isset($item['id'], $item['title'], $item['price'])) continue;

                $itemModel = (new ItemModel())
                    ->setId((int)$item['id'])
                    ->setName($item['title'])
                    ->setDescription(strip_tags($item['brief'] ?? ''))
                    ->setPrice((float)$item['price'])
                    ->setBrand($item['brand_id'] ?? null)
                    ->setCategory((int)($item['category_id'] ?? $category->getId()))
                    ->setImage($item['image'] ?? null)
                    ->setAvailableStock((int)($item['stock'] ?? 0));

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

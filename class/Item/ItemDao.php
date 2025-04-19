<?php

require_once 'ItemEntity.php';

class ItemDao
{
    /**
     * @var SQLite3
     */
    private $dataBaseInstance;

    /**
     * @return SQLite3
     * @throws Exception
     */
    protected function getDatabaseInstance()
    {
        return $this->dataBaseInstance ?? $this->dataBaseInstance = Database::getInstance();
    }

    /**
     * @param ItemEntity $item
     * @return void
     */
    public function insert(ItemEntity $item)
    {
        $stmt = $this->getDatabaseInstance()->prepare('
            INSERT OR REPLACE INTO items (id, name, description, price, brand, category_id, image_url, stock)
            VALUES (:id, :name, :description, :price, :brand, :category_id, :image_url, :stock)
        ');

        $stmt->bindValue(':id', $item->getId());
        $stmt->bindValue(':name', $item->getName());
        $stmt->bindValue(':description', $item->getDescription());
        $stmt->bindValue(':price', $item->getPrice());
        $stmt->bindValue(':brand', $item->getBrand());
        $stmt->bindValue(':category_id', $item->getCategoryId());
        $stmt->bindValue(':image_url', $item->getImageUrl());
        $stmt->bindValue(':stock', $item->getStock());
        $stmt->execute();

    }

    /**
     * @param array $filters
     * @param $sortBy
     * @param $order
     * @return ItemEntity[]
     */
    public function fetch(array $filters = [], $sortBy = 'name', $order = 'ASC', $limit=null)
    {
        $sql = 'SELECT * FROM items';
        $params = [];
        $where = [];

        if (!empty($filters['category_id'])) {
            $where[] = 'category_id = :category_id';
            $params[':category_id'] = $filters['category_id'];
        }
        if (!empty($filters['brand'])) {
            $where[] = 'brand = :brand';
            $params[':brand'] = $filters['brand'];
        }
        if (!empty($filters['min_price'])) {
            $where[] = 'price >= :min_price';
            $params[':min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[] = 'price <= :max_price';
            $params[':max_price'] = $filters['max_price'];
        }

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $allowedSort = ['name', 'price', 'stock'];
        $sql .= ' ORDER BY ' . (in_array($sortBy, $allowedSort) ? $sortBy : 'name') . ' ' . (strtoupper($order) === 'DESC' ? 'DESC' : 'ASC');

        if(!empty($limit)){
            $sql .= ' LIMIT :limit';
            $params[':limit'] = $limit;
        }


        $stmt = $this->getDatabaseInstance()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $result = $stmt->execute();
        $items = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $entity = new ItemEntity();

            $entity->setId($row['id']);
            $entity->setName($row['name']);
            $entity->setDescription($row['description']);
            $entity->setPrice($row['price']);
            $entity->setBrand($row['brand']);
            $entity->setCategoryId($row['category_id']);
            $entity->setImageUrl($row['image_url'] ?? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSjyYKMUJHkIU5D4JfZ7gQ_v41_brsHbEdWl4GEL8_mKpI_mzkwfqJ7kTXoiARNuFrmb5Q&usqp=CAU');
            $entity->setStock($row['stock']);

            $items[] = $entity;
        }

        return $items;
    }
}

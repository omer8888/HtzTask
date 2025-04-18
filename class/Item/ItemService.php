<?php

require_once 'ItemDao.php';
require_once 'ItemConverter.php';

class ItemService
{
    private $dao;

    public function __construct()
    {
        $this->dao = new ItemDao();
    }

    /**
     * @param array $filters
     * @param $sortBy
     * @param $order
     * @return array
     */
    public function getItems(array $filters = [], $sortBy = 'name', $order = 'ASC')
    {
        $entities = $this->dao->fetch($filters, $sortBy, $order);
        $models = [];

        foreach ($entities as $entity) {
            //$models[] = ItemConverter::entityToModel($entity);
            $models[] = [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'image_url' => $entity->getImageUrl(),
                'brand' => $entity->getBrand(),
                'price' => $entity->getPrice(),
                'category_id' => $entity->getCategoryId()
            ];
        }

        //return $models;
        return [
            'items' => $models
        ];
    }

    /**
     * @param ItemModel $model
     * @return void
     */
    public function addItem(ItemModel $model)
    {
        $entity = ItemConverter::modelToEntity($model);
        $this->dao->insert($entity);
    }
}

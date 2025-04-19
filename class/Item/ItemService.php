<?php

require_once 'ItemDao.php';
require_once 'ItemConverter.php';

class ItemService
{
    /**
     * @var ItemDao
     */
    private $dao;


    /**
     * @param array $filters
     * @param $sortBy
     * @param $order
     * @return array
     */
    public function getItemsForDisplay(array $filters = [], $sortBy = 'name', $order = 'ASC')
    {
        $entities = $this->getItemDao()->fetch($filters, $sortBy, $order);
        $models = [];

        foreach ($entities as $entity) {
            $models[] = [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'image_url' => $entity->getImageUrl(),
                'brand' => $entity->getBrand(),
                'price' => $entity->getPrice(),
                'category_id' => $entity->getCategoryId()
            ];
        }

        return [
            'items' => $models
        ];
    }

    /**
     * @param ItemModel $model
     * @return bool
     */
    public function validateItemModel($model)
    {
        if (!is_int($model->getId()) || $model->getId() < 0) {
            error_log('[Validation] Invalid ID: ' . $model->getId());
            return false;
        }

        if (!is_string($model->getName()) || trim($model->getName()) === '') {
            error_log('[Validation] Invalid Name: ' . $model->getName());
            return false;
        }

        if (!is_string($model->getDescription())) {
            error_log('[Validation] Invalid Description');
            return false;
        }

        if (!is_int($model->getPrice()) || $model->getPrice() < 0) {
            error_log('[Validation] Invalid Price: ' . $model->getPrice());
            return false;
        }

        if (!is_string($model->getBrand())) {
            error_log('[Validation] Invalid Brand');
            return false;
        }

        if (!is_int($model->getCategory()) || $model->getCategory() < 0) {
            error_log('[Validation] Invalid Category: ' . $model->getCategory());
            return false;
        }

        //as there is no image
//        if (!is_string($model->getImage())) {
//            error_log('[Validation] Invalid Image');
//            return false;
//        }

        if (!is_int($model->getAvailableStock()) || $model->getAvailableStock() < 0) {
            error_log('[Validation] Invalid Stock: ' . $model->getAvailableStock());
            return false;
        }

        return true;
    }


    /**
     * @param array $filters
     * @param $sortBy
     * @param $order
     * @return array
     */
    public function getItems(array $filters = [], $sortBy = 'name', $order = 'ASC')
    {
        $entities = $this->getItemDao()->fetch($filters, $sortBy, $order);
        $models = [];

        foreach ($entities as $entity) {
            $models[] = ItemConverter::entityToModel($entity);
        }

        return $models;
    }



    /**
     * @param ItemModel $model
     * @return void
     */
    public function addItem(ItemModel $model)
    {
        $entity = ItemConverter::modelToEntity($model);
        $this->getItemDao()->insert($entity);
    }

    /**
     * @return ItemDao
     */
    protected function getItemDao()
    {
        return $this->dao ?? $this->dao = new ItemDao();
    }

}

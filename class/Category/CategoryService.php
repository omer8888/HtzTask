<?php

require_once 'CategoryDao.php';
require_once 'CategoryConverter.php';

class CategoryService
{
    /**
     * @var CategoryDao
     */
    private $categoryDao;


    /**
     * @return array
     */
    public function getCategoriesForDisplay()
    {
        $entities = $this->getCategoryDao()->fetchAll();
        $models = [];

        foreach ($entities as $entity) {
            $models[] = [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'description' => $entity->getDescription()
            ];
        }

        return [
            'items' => $models
        ];
    }

    /**
     * Validates a CategoryModel instance
     *
     * @param CategoryModel $model
     * @return bool
     */
    public function validateCategoryModel($model)
    {
        if (!is_int($model->getId()) || $model->getId() <= 0) {
            error_log('[CategoryService] Invalid ID: ' . print_r($model->getId(), true));
            return false;
        }

        if (!is_string($model->getName()) || trim($model->getName()) === '') {
            error_log('[CategoryService] Invalid name: ' . print_r($model->getName(), true));
            return false;
        }

        // Description can be null or string, so we only check if it's not a string when set
        if (!is_null($model->getDescription()) && !is_string($model->getDescription())) {
            error_log('[CategoryService] Invalid description: ' . print_r($model->getDescription(), true));
            return false;
        }

        return true;
    }


    /**
     * @return CategoryModel[]
     */
    public function getCategories()
    {
        $entities = $this->getCategoryDao()->fetchAll();
        $models = [];

        foreach ($entities as $entity) {
            $models[] = CategoryConverter::entityToModel($entity);
        }

        return $models;
    }

    /**
     * @param CategoryModel $model
     * @return void
     */
    public function addCategory($model)
    {
        $entity = CategoryConverter::modelToEntity($model);
        $this->getCategoryDao()->insert($entity);
    }

    /**
     * @return CategoryDao
     */
    protected function getCategoryDao()
    {
        return $this->categoryDao ?? $this->categoryDao = new CategoryDao();
    }

}

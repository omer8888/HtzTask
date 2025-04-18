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
    public function getCategories()
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

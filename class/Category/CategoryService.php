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
     * @var HtzoneApi
     */
    private $htzoneApi;


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

    /**
     * @return HtzoneApi
     */
    private function getHtzoneApi()
    {
        return $this->htzoneApi ?? $this->htzoneApi = new HtzoneApi();
    }

}

<?php

require_once 'CategoryDao.php';
require_once 'CategoryConverter.php';

class CategoryService {
    private $dao;

    public function __construct() {
        $this->dao = new CategoryDao();
    }

    /**
     * @return array
     */
    public function getCategories() {
        $entities = $this->dao->fetchAll();
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

    public function addCategory(CategoryModel $model): void {
        $entity = CategoryConverter::modelToEntity($model);
        $this->dao->insert($entity);
    }

//    public function getCategoryById($id): ?CategoryModel {
//        $entity = $this->dao->fetchById($id);
//        return $entity ? CategoryConverter::entityToModel($entity) : null;
//    }
//
//    public function getCategoryItems($category_id): array {
//        $items = $this->dao->fetchCategoryItems($category_id);
//        $models = [];
//
//        foreach ($items as $item) {
//            $models[] = ItemConverter::entityToModel($item);
//        }
//
//        return $models;
//    }
//
//    public function getTopCategories($limit): array {
//        $entities = $this->dao->fetchTopCategories($limit);
//        $models = [];
//
//        foreach ($entities as $entity) {
//            $models[] = CategoryConverter::entityToModel($entity);
//        }
//
//        return $models;
//    }
//

}

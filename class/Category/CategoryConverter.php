<?php

require_once 'CategoryEntity.php';
require_once 'CategoryModel.php';

/**
 * Converting between entity to model and the other way
 */
class CategoryConverter
{
    /**
     * @param CategoryEntity $entity
     * @return CategoryModel
     */
    public static function entityToModel($entity)
    {
        $model = new CategoryModel();
        $model->setId($entity->getId())
            ->setName($entity->getName())
            ->setDescription($entity->getDescription());
        return $model;
    }

    /**
     * @param CategoryModel $model
     * @return CategoryEntity
     */
    public static function modelToEntity($model)
    {
        $entity = new CategoryEntity();
        $entity->setId($model->getId())
            ->setName($model->getName())
            ->setDescription($model->getDescription());
        return $entity;
    }

}

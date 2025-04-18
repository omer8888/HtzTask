<?php

require_once 'ItemEntity.php';
require_once 'ItemModel.php';

class ItemConverter
{
    /**
     * @param $entity
     * @return ItemModel
     */
    public static function entityToModel($entity)
    {
        $model = new ItemModel();
        $model->setId($entity->getId());
        $model->setName($entity->getName());
        $model->setDescription($entity->getDescription());
        $model->setPrice($entity->getPrice());
        $model->setBrand($entity->getBrand());
        $model->setCategory($entity->getCategoryId());
        $model->setImage($entity->getImageUrl());
        $model->setAvailableStock($entity->getStock());
        return $model;
    }

    /**
     * @param ItemModel $model
     * @return ItemEntity
     */
    public static function modelToEntity($model)
    {
        $entity = new ItemEntity();
        $entity->setId($model->getId());
        $entity->setName($model->getName());
        $entity->setDescription($model->getDescription());
        $entity->setPrice($model->getPrice());
        $entity->setBrand($model->getBrand());
        $entity->setCategoryId($model->getCategory());
        $entity->setImageUrl($model->getImage());
        $entity->setStock($model->getAvailableStock());
        return $entity;
    }

}

<?php

require_once 'CategoryEntity.php';


class CategoryDao
{
    /**
     * @var
     */
    private $dataBaseInstance;


    /**
     * @return SQLite3
     */
    protected function getDatabaseInstance()
    {
        return $this->dataBaseInstance ?? $this->dataBaseInstance = Database::getInstance();
    }

    /**
     * @param CategoryEntity $category
     * @return void
     */
    public function insert(CategoryEntity $category): void
    {
        $stmt = $this->getDatabaseInstance()->prepare('
            INSERT OR REPLACE INTO categories (id, name, description)
            VALUES (:id, :name, :description)
        ');

        $stmt->bindValue(':id', $category->getId());
        $stmt->bindValue(':name', $category->getName());
        $stmt->bindValue(':description', $category->getDescription());
        $stmt->execute();
    }

    /**
     * @return CategoryEntity[]
     */
    public function fetchAll(): array
    {
        $sql = 'SELECT * FROM categories';
        $result = $this->getDatabaseInstance()->query($sql);
        $categories = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $entity = new CategoryEntity();
            $entity->setId($row['id'])
                ->setName($row['name'])
                ->setDescription($row['description']);
            $categories[] = $entity;
        }

        return $categories;
    }
}

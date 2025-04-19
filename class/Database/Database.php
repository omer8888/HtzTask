<?php

/**
 * Singleton pattern
 */
class Database
{
    private static ?SQLite3 $instance = null;

    /**
     * @return SQLite3
     * @throws Exception
     */
    public static function getInstance(): SQLite3
    {
        if (self::$instance === null) {
            $dbPath = __DIR__ . '/database.sqlite';

            try {
                self::$instance = new SQLite3($dbPath);
                self::$instance->exec('PRAGMA foreign_keys = ON');
                self::initDatabase();
            } catch (Exception $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    /**
     * @return void
     */
    private static function initDatabase()
    {
        $result = self::$instance->exec('
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY,
            name TEXT NOT NULL,
            description TEXT,
            parent_id INTEGER,
            FOREIGN KEY (parent_id) REFERENCES categories(id)
        )
    ');

        if (!$result) {
            echo "Error creating categories table: " . self::$instance->lastErrorMsg();
        }

        $result = self::$instance->exec('
        CREATE TABLE IF NOT EXISTS items (
            id INTEGER PRIMARY KEY,
            name TEXT NOT NULL,
            description TEXT,
            price REAL NOT NULL,
            brand TEXT,
            category_id INTEGER,
            image_url TEXT,
            stock INTEGER,
            FOREIGN KEY (category_id) REFERENCES categories(id)
        )
    ');

        if (!$result) {
            echo "Error creating items table: " . self::$instance->lastErrorMsg();
        }

    }
}

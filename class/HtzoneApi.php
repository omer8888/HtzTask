<?php

class HtzoneApi {
    private $db;
    private $base_url = 'https://storeapi.htzone.co.il/ext/O2zfcVu2t8gOB6nzSfFBu4joDYPH7s';

    public function __construct() {
        try {
            $this->db = new SQLite3(__DIR__ . '/../database/database.sqlite');
            $this->db->exec('PRAGMA foreign_keys = ON');
        } catch (Exception $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
        $this->initDatabase();
    }

    public function initDatabase() {
        // Create categories table
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                description TEXT,
                parent_id INTEGER,
                FOREIGN KEY (parent_id) REFERENCES categories(id)
            )
        ');

        // Create items table
        $this->db->exec('
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
    }

    public function fetchAndStoreCategories() {
        // Fetch categories from API using CURL and store to local db
       
    }


    public function fetchAndStoreItems() {
          // Fetch items from API using CURL and store to local db
    }
}

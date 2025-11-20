<?php
/**
 * Category Controller
 */

class CategoryController {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($categoryId) {
        $query = "SELECT * FROM categories WHERE id = :id";
        $result = $this->db->query($query, [':id' => $categoryId]);
        return $result ? $result[0] : null;
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        return $this->db->query($query);
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory($categoryId, $sort = 'name') {
        $validSorts = ['name', 'price_asc', 'price_desc'];
        $sortBy = in_array($sort, $validSorts) ? $sort : 'name';

        $orderBy = match($sortBy) {
            'price_asc' => 'price ASC',
            'price_desc' => 'price DESC',
            default => 'name ASC'
        };

        $query = "SELECT * FROM products WHERE category_id = :category_id ORDER BY $orderBy";
        return $this->db->query($query, [':category_id' => $categoryId]);
    }

    /**
     * Create category
     */
    public function createCategory($data) {
        $query = "INSERT INTO categories (name, description) VALUES (:name, :description)";
        return $this->db->execute($query, [
            ':name' => $data['name'],
            ':description' => $data['description'] ?? ''
        ]);
    }

    /**
     * Update category
     */
    public function updateCategory($categoryId, $data) {
        $query = "UPDATE categories SET name = :name, description = :description WHERE id = :id";
        return $this->db->execute($query, [
            ':id' => $categoryId,
            ':name' => $data['name'],
            ':description' => $data['description'] ?? ''
        ]);
    }

    /**
     * Delete category
     */
    public function deleteCategory($categoryId) {
        $query = "DELETE FROM categories WHERE id = :id";
        return $this->db->execute($query, [':id' => $categoryId]);
    }

    /**
     * Count products in category
     */
    public function countProducts($categoryId) {
        $query = "SELECT COUNT(*) as count FROM products WHERE category_id = :category_id";
        $result = $this->db->query($query, [':category_id' => $categoryId]);
        return $result ? $result[0]['count'] : 0;
    }
}

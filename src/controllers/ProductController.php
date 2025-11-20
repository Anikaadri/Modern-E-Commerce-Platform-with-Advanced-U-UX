<?php
/**
 * Product Controller
 */

class ProductController {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts($limit = 8) {
        $query = "SELECT * FROM products LIMIT " . intval($limit);
        return $this->db->query($query);
    }

    /**
     * Get all categories
     */
    public function getCategories() {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        return $this->db->query($query);
    }

    /**
     * Get product by ID
     */
    public function getProductById($productId) {
        $query = "SELECT * FROM products WHERE id = :id";
        $result = $this->db->query($query, [':id' => $productId]);
        return $result ? $result[0] : null;
    }

    /**
     * Get related products
     */
    public function getRelatedProducts($categoryId, $currentProductId, $limit = 4) {
        $query = "SELECT * FROM products WHERE category_id = :category_id AND id != :id LIMIT " . intval($limit);
        return $this->db->query($query, [
            ':category_id' => $categoryId,
            ':id' => $currentProductId
        ]);
    }

    /**
     * Search products
     */
    public function searchProducts($searchQuery) {
        $query = "SELECT * FROM products WHERE name LIKE :search OR description LIKE :search";
        return $this->db->query($query, [':search' => '%' . $searchQuery . '%']);
    }

    /**
     * Get all products with pagination
     */
    public function getAllProducts($page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM products LIMIT " . intval($perPage) . " OFFSET " . intval($offset);
        return $this->db->query($query);
    }

    /**
     * Create product
     */
    public function createProduct($data) {
        $query = "INSERT INTO products (name, description, price, stock, category_id, image) 
                  VALUES (:name, :description, :price, :stock, :category_id, :image)";
        return $this->db->execute($query, [
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':stock' => $data['stock'],
            ':category_id' => $data['category_id'],
            ':image' => $data['image']
        ]);
    }

    /**
     * Update product
     */
    public function updateProduct($productId, $data) {
        $query = "UPDATE products SET name = :name, description = :description, 
                  price = :price, stock = :stock, category_id = :category_id, image = :image 
                  WHERE id = :id";
        return $this->db->execute($query, [
            ':id' => $productId,
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':stock' => $data['stock'],
            ':category_id' => $data['category_id'],
            ':image' => $data['image']
        ]);
    }

    /**
     * Delete product
     */
    public function deleteProduct($productId) {
        $query = "DELETE FROM products WHERE id = :id";
        return $this->db->execute($query, [':id' => $productId]);
    }
}

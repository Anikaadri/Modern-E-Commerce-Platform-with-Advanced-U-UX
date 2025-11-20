<?php
/**
 * Product Service
 */

class ProductService {
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
     * Get trending products
     */
    public function getTrendingProducts($limit = 6) {
        $query = "SELECT p.* FROM products p 
                  LEFT JOIN order_items oi ON p.id = oi.product_id
                  GROUP BY p.id 
                  ORDER BY COUNT(oi.id) DESC 
                  LIMIT " . intval($limit);
        return $this->db->query($query);
    }

    /**
     * Search products with filters
     */
    public function searchProducts($query, $filters = []) {
        $sql = "SELECT * FROM products WHERE (name LIKE :search OR description LIKE :search)";
        $params = [':search' => '%' . $query . '%'];

        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }

        if (!empty($filters['in_stock'])) {
            $sql .= " AND stock > 0";
        }

        return $this->db->query($sql, $params);
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory($categoryId, $limit = null) {
        $sql = "SELECT * FROM products WHERE category_id = :category_id";
        $params = [':category_id' => $categoryId];

        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }

        return $this->db->query($sql, $params);
    }

    /**
     * Get similar products
     */
    public function getSimilarProducts($productId, $limit = 4) {
        $product = $this->getProductById($productId);
        if (!$product) {
            return [];
        }

        $query = "SELECT * FROM products WHERE category_id = :category_id AND id != :id LIMIT " . intval($limit);
        return $this->db->query($query, [
            ':category_id' => $product['category_id'],
            ':id' => $productId
        ]);
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
     * Check product availability
     */
    public function isAvailable($productId, $quantity = 1) {
        $product = $this->getProductById($productId);
        return $product && $product['stock'] >= $quantity;
    }

    /**
     * Get product rating
     */
    public function getProductRating($productId) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE product_id = :id";
        $result = $this->db->query($query, [':id' => $productId]);
        return $result ? $result[0] : ['avg_rating' => 0, 'count' => 0];
    }
}

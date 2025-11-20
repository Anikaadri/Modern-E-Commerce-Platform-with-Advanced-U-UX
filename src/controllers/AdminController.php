<?php
/**
 * Admin Controller
 */

class AdminController {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        $stats = [];

        // Total products
        $result = $this->db->query("SELECT COUNT(*) as count FROM products");
        $stats['total_products'] = $result[0]['count'] ?? 0;

        // Total orders
        $result = $this->db->query("SELECT COUNT(*) as count FROM orders");
        $stats['total_orders'] = $result[0]['count'] ?? 0;

        // Total revenue
        $result = $this->db->query("SELECT SUM(total) as total FROM orders WHERE status = 'completed'");
        $stats['total_revenue'] = $result[0]['total'] ?? 0;

        // Total users
        $result = $this->db->query("SELECT COUNT(*) as count FROM users");
        $stats['total_users'] = $result[0]['count'] ?? 0;

        return $stats;
    }

    /**
     * Get all products
     */
    public function getAllProducts() {
        $query = "SELECT p.*, c.name as category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  ORDER BY p.created_at DESC";
        return $this->db->query($query);
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        return $this->db->query($query);
    }

    /**
     * Get all orders
     */
    public function getAllOrders() {
        $query = "SELECT o.*, u.name as user_name FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  ORDER BY o.created_at DESC";
        return $this->db->query($query);
    }

    /**
     * Get all users
     */
    public function getAllUsers() {
        $query = "SELECT id, name, email, created_at FROM users ORDER BY created_at DESC";
        return $this->db->query($query);
    }

    /**
     * Delete product
     */
    public function deleteProduct($productId) {
        $query = "DELETE FROM products WHERE id = :id";
        return $this->db->execute($query, [':id' => $productId]);
    }

    /**
     * Delete category
     */
    public function deleteCategory($categoryId) {
        $query = "DELETE FROM categories WHERE id = :id";
        return $this->db->execute($query, [':id' => $categoryId]);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($orderId, $status) {
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        return $this->db->execute($query, [':status' => $status, ':id' => $orderId]);
    }

    /**
     * Get order details
     */
    public function getOrderDetails($orderId) {
        $query = "SELECT o.*, u.name, u.email FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  WHERE o.id = :id";
        $result = $this->db->query($query, [':id' => $orderId]);
        return $result ? $result[0] : null;
    }
}

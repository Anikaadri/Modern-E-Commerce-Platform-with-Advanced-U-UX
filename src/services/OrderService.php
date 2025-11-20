<?php
/**
 * Order Service
 */

class OrderService {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    /**
     * Create order from cart
     */
    public function createOrderFromCart($userId, $cartItems, $shippingAddress, $paymentMethod) {
        // Calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Insert order
        $query = "INSERT INTO orders (user_id, total, status, shipping_address, payment_method) 
                  VALUES (:user_id, :total, 'pending', :shipping_address, :payment_method)";
        
        $this->db->execute($query, [
            ':user_id' => $userId,
            ':total' => $total,
            ':shipping_address' => $shippingAddress,
            ':payment_method' => $paymentMethod
        ]);

        $orderId = $this->db->lastInsertId();

        // Insert order items
        foreach ($cartItems as $productId => $item) {
            $query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                      VALUES (:order_id, :product_id, :quantity, :price)";
            
            $this->db->execute($query, [
                ':order_id' => $orderId,
                ':product_id' => $productId,
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);
        }

        return $orderId;
    }

    /**
     * Get order details with items
     */
    public function getOrderDetails($orderId) {
        $query = "SELECT o.*, u.name as user_name, u.email FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  WHERE o.id = :id";
        $result = $this->db->query($query, [':id' => $orderId]);

        if (!$result) {
            return null;
        }

        $order = $result[0];

        // Get order items
        $query = "SELECT oi.*, p.name FROM order_items oi 
                  LEFT JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = :order_id";
        $order['items'] = $this->db->query($query, [':order_id' => $orderId]);

        return $order;
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($orderId, $status) {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $query = "UPDATE orders SET status = :status WHERE id = :id";
        return $this->db->execute($query, [':status' => $status, ':id' => $orderId]);
    }

    /**
     * Get user orders
     */
    public function getUserOrders($userId, $limit = 10, $offset = 0) {
        $query = "SELECT * FROM orders WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        return $this->db->query($query, [':user_id' => $userId]);
    }

    /**
     * Calculate order revenue statistics
     */
    public function getRevenueStats($startDate = null, $endDate = null) {
        $sql = "SELECT COUNT(*) as total_orders, SUM(total) as total_revenue 
                FROM orders WHERE status = 'completed'";
        $params = [];

        if ($startDate) {
            $sql .= " AND created_at >= :start_date";
            $params[':start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND created_at <= :end_date";
            $params[':end_date'] = $endDate;
        }

        $result = $this->db->query($sql, $params);
        return $result ? $result[0] : ['total_orders' => 0, 'total_revenue' => 0];
    }
}

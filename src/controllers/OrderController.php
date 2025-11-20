<?php
/**
 * Order Controller
 */

class OrderController {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    /**
     * Create order
     */
    public function createOrder($data) {
        $total = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }
        }

        $query = "INSERT INTO orders (user_id, total, status, shipping_address, payment_method) 
                  VALUES (:user_id, :total, 'pending', :shipping_address, :payment_method)";
        
        $result = $this->db->execute($query, [
            ':user_id' => $data['user_id'],
            ':total' => $total,
            ':shipping_address' => $data['shipping_address'],
            ':payment_method' => $data['payment_method']
        ]);

        if ($result) {
            $orderId = $this->db->lastInsertId();
            $this->saveOrderItems($orderId, $_SESSION['cart'] ?? []);
            return $orderId;
        }

        return false;
    }

    /**
     * Save order items
     */
    private function saveOrderItems($orderId, $cart) {
        foreach ($cart as $item) {
            $query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                      VALUES (:order_id, :product_id, :quantity, :price)";
            
            $this->db->execute($query, [
                ':order_id' => $orderId,
                ':product_id' => $item['id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);

            // Update product stock
            $this->updateProductStock($item['id'], $item['quantity']);
        }
    }

    /**
     * Update product stock
     */
    private function updateProductStock($productId, $quantity) {
        $query = "UPDATE products SET stock = stock - :quantity WHERE id = :id";
        return $this->db->execute($query, [
            ':quantity' => $quantity,
            ':id' => $productId
        ]);
    }

    /**
     * Process payment
     */
    public function processPayment($orderId, $cardNumber) {
        // Simulate payment processing
        // In production, integrate with payment gateway (Stripe, PayPal, etc.)
        
        $lastFourDigits = substr($cardNumber, -4);
        
        $query = "UPDATE orders SET status = 'completed', payment_method = :payment_method WHERE id = :id";
        return $this->db->execute($query, [
            ':id' => $orderId,
            ':payment_method' => 'Card ending in ' . $lastFourDigits
        ]);
    }

    /**
     * Get order by ID
     */
    public function getOrderById($orderId) {
        $query = "SELECT * FROM orders WHERE id = :id";
        $result = $this->db->query($query, [':id' => $orderId]);
        return $result ? $result[0] : null;
    }

    /**
     * Get order items
     */
    public function getOrderItems($orderId) {
        $query = "SELECT oi.*, p.name FROM order_items oi 
                  LEFT JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = :order_id";
        return $this->db->query($query, [':order_id' => $orderId]);
    }

    /**
     * Cancel order
     */
    public function cancelOrder($orderId) {
        // Check if order can be cancelled (not already shipped, etc.)
        $order = $this->getOrderById($orderId);
        
        if ($order && $order['status'] === 'pending') {
            // Restore product stock
            $items = $this->getOrderItems($orderId);
            foreach ($items as $item) {
                $query = "UPDATE products SET stock = stock + :quantity WHERE id = :id";
                $this->db->execute($query, [
                    ':quantity' => $item['quantity'],
                    ':id' => $item['product_id']
                ]);
            }

            $query = "UPDATE orders SET status = 'cancelled' WHERE id = :id";
            return $this->db->execute($query, [':id' => $orderId]);
        }

        return false;
    }
}

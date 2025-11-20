<?php
/**
 * Cart Service
 */

class CartService {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    /**
     * Validate cart items
     */
    public function validateCart($cartItems) {
        $errors = [];

        foreach ($cartItems as $productId => $item) {
            // Check if product exists
            $query = "SELECT * FROM products WHERE id = :id";
            $result = $this->db->query($query, [':id' => $productId]);

            if (!$result) {
                $errors[] = "Product {$productId} not found";
                continue;
            }

            $product = $result[0];

            // Check stock
            if ($item['quantity'] > $product['stock']) {
                $errors[] = "Not enough stock for {$product['name']}. Available: {$product['stock']}";
            }

            // Check quantity
            if ($item['quantity'] <= 0) {
                $errors[] = "Invalid quantity for {$product['name']}";
            }
        }

        return $errors;
    }

    /**
     * Calculate cart totals
     */
    public function calculateTotals($cartItems) {
        $subtotal = 0;
        $tax = 0;
        $shipping = 0;

        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $tax = $subtotal * 0.1; // 10% tax
        $shipping = $subtotal > 100 ? 0 : 10; // Free shipping over $100

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $subtotal + $tax + $shipping
        ];
    }

    /**
     * Apply discount code
     */
    public function applyDiscount($code, $subtotal) {
        $query = "SELECT * FROM discount_codes WHERE code = :code AND is_active = 1";
        $result = $this->db->query($query, [':code' => $code]);

        if (!$result) {
            return ['success' => false, 'message' => 'Invalid discount code'];
        }

        $discount = $result[0];
        $discountAmount = 0;

        if ($discount['type'] === 'percentage') {
            $discountAmount = $subtotal * ($discount['value'] / 100);
        } else {
            $discountAmount = $discount['value'];
        }

        return [
            'success' => true,
            'discount_amount' => $discountAmount,
            'discount_code' => $code
        ];
    }
}

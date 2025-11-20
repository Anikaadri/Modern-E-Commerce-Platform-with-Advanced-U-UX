<?php
/**
 * Cart Controller
 */

class CartController {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    /**
     * Add product to cart
     */
    public function addToCart($productId, $quantity = 1) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if product exists
        $product = $this->getProduct($productId);
        if (!$product) {
            return false;
        }

        // Add or update cart item
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $productId,
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];
        }

        return true;
    }

    /**
     * Get cart items
     */
    public function getCart() {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }

    /**
     * Remove product from cart
     */
    public function removeFromCart($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            return true;
        }
        return false;
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity($productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($productId);
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
            return true;
        }
        return false;
    }

    /**
     * Calculate cart total
     */
    public function calculateTotal() {
        $total = 0;
        $cart = $this->getCart();
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    /**
     * Clear cart
     */
    public function clearCart() {
        unset($_SESSION['cart']);
        return true;
    }

    /**
     * Get product details
     */
    private function getProduct($productId) {
        $query = "SELECT * FROM products WHERE id = :id";
        $result = $this->db->query($query, [':id' => $productId]);
        return $result ? $result[0] : null;
    }
}

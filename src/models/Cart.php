<?php
/**
 * Cart Model
 */

class Cart {
    public $items = [];

    public function __construct($items = []) {
        $this->items = $items;
    }

    /**
     * Add item to cart
     */
    public function addItem($item) {
        if (isset($this->items[$item['id']])) {
            $this->items[$item['id']]['quantity'] += $item['quantity'];
        } else {
            $this->items[$item['id']] = $item;
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem($itemId) {
        unset($this->items[$itemId]);
    }

    /**
     * Update item quantity
     */
    public function updateQuantity($itemId, $quantity) {
        if ($quantity <= 0) {
            $this->removeItem($itemId);
        } elseif (isset($this->items[$itemId])) {
            $this->items[$itemId]['quantity'] = $quantity;
        }
    }

    /**
     * Get cart total
     */
    public function getTotal() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    /**
     * Get item count
     */
    public function getItemCount() {
        return count($this->items);
    }

    /**
     * Get quantity count
     */
    public function getQuantityCount() {
        $count = 0;
        foreach ($this->items as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    /**
     * Clear cart
     */
    public function clear() {
        $this->items = [];
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty() {
        return empty($this->items);
    }
}

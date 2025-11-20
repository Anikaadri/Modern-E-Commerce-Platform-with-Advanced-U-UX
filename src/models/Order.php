<?php
/**
 * Order Model
 */

class Order {
    public $id;
    public $userId;
    public $total;
    public $status;
    public $shippingAddress;
    public $paymentMethod;
    public $createdAt;
    public $updatedAt;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->userId = $data['user_id'] ?? null;
        $this->total = $data['total'] ?? 0;
        $this->status = $data['status'] ?? 'pending';
        $this->shippingAddress = $data['shipping_address'] ?? '';
        $this->paymentMethod = $data['payment_method'] ?? '';
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }

    /**
     * Validate order data
     */
    public function validate() {
        $errors = [];

        if (empty($this->userId)) {
            $errors[] = "User ID is required";
        }

        if ($this->total <= 0) {
            $errors[] = "Order total must be greater than 0";
        }

        if (empty($this->shippingAddress)) {
            $errors[] = "Shipping address is required";
        }

        if (empty($this->paymentMethod)) {
            $errors[] = "Payment method is required";
        }

        return $errors;
    }

    /**
     * Check if order is completed
     */
    public function isCompleted() {
        return $this->status === 'completed';
    }

    /**
     * Check if order is pending
     */
    public function isPending() {
        return $this->status === 'pending';
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled() {
        return $this->status === 'cancelled';
    }
}

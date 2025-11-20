<?php
/**
 * Review Model
 */

class Review {
    public $id;
    public $productId;
    public $userId;
    public $rating;
    public $comment;
    public $createdAt;
    public $userName; // From join

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->productId = $data['product_id'] ?? null;
        $this->userId = $data['user_id'] ?? null;
        $this->rating = $data['rating'] ?? 0;
        $this->comment = $data['comment'] ?? '';
        $this->createdAt = $data['created_at'] ?? null;
        $this->userName = $data['user_name'] ?? 'Anonymous';
    }

    /**
     * Validate review data
     */
    public function validate() {
        $errors = [];

        if (empty($this->productId)) {
            $errors[] = "Product ID is required";
        }

        if (empty($this->userId)) {
            $errors[] = "User ID is required";
        }

        if ($this->rating < 1 || $this->rating > 5) {
            $errors[] = "Rating must be between 1 and 5";
        }

        if (empty($this->comment)) {
            $errors[] = "Comment is required";
        }

        return $errors;
    }

    /**
     * Get formatted date
     */
    public function getFormattedDate() {
        return date('F j, Y', strtotime($this->createdAt));
    }
}

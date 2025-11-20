<?php
/**
 * Product Model
 */

class Product {
    public $id;
    public $name;
    public $description;
    public $price;
    public $stock;
    public $categoryId;
    public $image;
    public $isFeatured;
    public $createdAt;
    public $updatedAt;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->price = $data['price'] ?? 0;
        $this->stock = $data['stock'] ?? 0;
        $this->categoryId = $data['category_id'] ?? null;
        $this->image = $data['image'] ?? '';
        $this->isFeatured = $data['is_featured'] ?? 0;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }

    /**
     * Check if product is in stock
     */
    public function isInStock() {
        return $this->stock > 0;
    }

    /**
     * Get discounted price
     */
    public function getDiscountedPrice($discountPercentage) {
        return $this->price * (1 - ($discountPercentage / 100));
    }

    /**
     * Validate product data
     */
    public function validate() {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = "Product name is required";
        }

        if ($this->price <= 0) {
            $errors[] = "Price must be greater than 0";
        }

        if ($this->stock < 0) {
            $errors[] = "Stock cannot be negative";
        }

        if (empty($this->categoryId)) {
            $errors[] = "Category is required";
        }

        return $errors;
    }
}

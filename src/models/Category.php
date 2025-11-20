<?php
/**
 * Category Model
 */

class Category {
    public $id;
    public $name;
    public $description;
    public $createdAt;
    public $updatedAt;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }

    /**
     * Validate category data
     */
    public function validate() {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = "Category name is required";
        }

        if (strlen($this->name) > 100) {
            $errors[] = "Category name must be less than 100 characters";
        }

        return $errors;
    }

    /**
     * Get category slug
     */
    public function getSlug() {
        return strtolower(str_replace(' ', '-', $this->name));
    }
}

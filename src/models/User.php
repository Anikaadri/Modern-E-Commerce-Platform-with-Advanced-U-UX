<?php
/**
 * User Model
 */

class User {
    public $id;
    public $name;
    public $email;
    public $password;
    public $isAdmin;
    public $createdAt;
    public $updatedAt;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->isAdmin = $data['is_admin'] ?? 0;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }

    /**
     * Hash password
     */
    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    /**
     * Verify password
     */
    public function verifyPassword($plainPassword) {
        return password_verify($plainPassword, $this->password);
    }

    /**
     * Validate user data
     */
    public function validate($isNew = true) {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = "Name is required";
        }

        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required";
        }

        if ($isNew && (empty($this->password) || strlen($this->password) < 6)) {
            $errors[] = "Password must be at least 6 characters";
        }

        return $errors;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin() {
        return $this->isAdmin == 1;
    }
}

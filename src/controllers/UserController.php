<?php
/**
 * User Controller
 */

class UserController {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    /**
     * Get user by ID
     */
    public function getUserById($userId) {
        $query = "SELECT * FROM users WHERE id = :id";
        $result = $this->db->query($query, [':id' => $userId]);
        return $result ? $result[0] : null;
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        $result = $this->db->query($query, [':email' => $email]);
        return $result ? $result[0] : null;
    }

    /**
     * Get user orders
     */
    public function getUserOrders($userId) {
        $query = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
        return $this->db->query($query, [':user_id' => $userId]);
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        $query = "UPDATE users SET name = :name, email = :email WHERE id = :id";
        return $this->db->execute($query, [
            ':id' => $userId,
            ':name' => $data['name'],
            ':email' => $data['email']
        ]);
    }

    /**
     * Change password
     */
    public function changePassword($userId, $oldPassword, $newPassword) {
        $user = $this->getUserById($userId);
        
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            return false;
        }

        $query = "UPDATE users SET password = :password WHERE id = :id";
        return $this->db->execute($query, [
            ':id' => $userId,
            ':password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);
    }

    /**
     * Get all users (admin only)
     */
    public function getAllUsers() {
        $query = "SELECT id, name, email, created_at FROM users ORDER BY created_at DESC";
        return $this->db->query($query);
    }

    /**
     * Delete user (admin only)
     */
    public function deleteUser($userId) {
        $query = "DELETE FROM users WHERE id = :id";
        return $this->db->execute($query, [':id' => $userId]);
    }
}

<?php
/**
 * User Service
 * Handles user profile data and updates
 */

class UserService {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $query = "SELECT id, name, email, phone, address, city, zip, created_at FROM users WHERE id = :id";
        $result = $this->db->query($query, [':id' => $id]);
        return $result ? $result[0] : null;
    }

    /**
     * Update user profile
     */
    public function updateProfile($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['name'])) {
            $fields[] = "name = :name";
            $params[':name'] = $data['name'];
        }
        if (isset($data['phone'])) {
            $fields[] = "phone = :phone";
            $params[':phone'] = $data['phone'];
        }
        if (isset($data['address'])) {
            $fields[] = "address = :address";
            $params[':address'] = $data['address'];
        }
        if (isset($data['city'])) {
            $fields[] = "city = :city";
            $params[':city'] = $data['city'];
        }
        if (isset($data['zip'])) {
            $fields[] = "zip = :zip";
            $params[':zip'] = $data['zip'];
        }

        if (empty($fields)) {
            return true; // Nothing to update
        }

        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        return $this->db->execute($query, $params);
    }

    /**
     * Change password
     */
    public function changePassword($id, $currentPassword, $newPassword) {
        // Verify current password
        $query = "SELECT password FROM users WHERE id = :id";
        $result = $this->db->query($query, [':id' => $id]);

        if (!$result) {
            return false;
        }

        $user = $result[0];
        
        // Handle password key case sensitivity (copied from AuthService fix)
        $passwordHash = null;
        if (isset($user['password'])) {
            $passwordHash = $user['password'];
        } elseif (isset($user['Password'])) {
            $passwordHash = $user['Password'];
        } elseif (isset($user['PASSWORD'])) {
            $passwordHash = $user['PASSWORD'];
        }

        if ($passwordHash === null || !password_verify($currentPassword, $passwordHash)) {
            return false; // Invalid current password
        }

        // Update with new password
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $query = "UPDATE users SET password = :password WHERE id = :id";
        return $this->db->execute($query, [
            ':password' => $newHash,
            ':id' => $id
        ]);
    }
}

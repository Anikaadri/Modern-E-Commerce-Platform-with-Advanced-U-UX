<?php
/**
 * Auth Service
 */

class AuthService {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    /**
     * Register new user
     */
    public function register($name, $email, $password) {
        // Check if email already exists
        $query = "SELECT * FROM users WHERE email = :email";
        $result = $this->db->query($query, [':email' => $email]);

        if ($result) {
            return false; // Email already exists
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user
        $query = "INSERT INTO users (name, email, password, is_admin) VALUES (:name, :email, :password, 0)";
        return $this->db->execute($query, [
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
    }

    /**
     * Login user
     */
    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email";
        $result = $this->db->query($query, [':email' => $email]);

        if (!$result) {
            return null; // User not found
        }

        $user = $result[0];
        
        // Handle potential case sensitivity or missing key
        $passwordHash = null;
        if (isset($user['password'])) {
            $passwordHash = $user['password'];
        } elseif (isset($user['Password'])) {
            $passwordHash = $user['Password'];
        } elseif (isset($user['PASSWORD'])) {
            $passwordHash = $user['PASSWORD'];
        }
        
        if ($passwordHash === null) {
            // Debug: Log available keys if password is missing
            $keys = implode(", ", array_keys($user));
            error_log("Login Error: 'password' column not found. Available keys: " . $keys);
            throw new Exception("Database Error: Password column missing. Available columns: " . $keys);
        }

        // Verify password
        if (!password_verify($password, $passwordHash)) {
            return null; // Invalid password
        }

        return $user;
    }

    /**
     * Verify email
     */
    public function verifyEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength
     */
    public function validatePasswordStrength($password) {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain uppercase letter";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain lowercase letter";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain number";
        }

        return $errors;
    }

    /**
     * Generate password reset token
     */
    public function generateResetToken($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        $result = $this->db->query($query, [':email' => $email]);

        if (!$result) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $query = "UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email";
        $this->db->execute($query, [
            ':token' => $token,
            ':expiry' => $expiry,
            ':email' => $email
        ]);

        return $token;
    }

    /**
     * Reset password
     */
    public function resetPassword($token, $newPassword) {
        $query = "SELECT * FROM users WHERE reset_token = :token AND reset_token_expiry > NOW()";
        $result = $this->db->query($query, [':token' => $token]);

        if (!$result) {
            return false;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $query = "UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = :token";
        return $this->db->execute($query, [
            ':password' => $hashedPassword,
            ':token' => $token
        ]);
    }
}

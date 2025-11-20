<?php
/**
 * Validator Helper Class
 */

class Validator {
    private $errors = [];

    /**
     * Validate email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate URL
     */
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate integer
     */
    public static function validateInteger($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate float
     */
    public static function validateFloat($value) {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * Validate phone number
     */
    public static function validatePhoneNumber($phone) {
        return preg_match('/^[0-9\-\+\(\)\s]{10,15}$/', $phone);
    }

    /**
     * Validate password strength
     */
    public static function validatePasswordStrength($password) {
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
     * Sanitize string
     */
    public static function sanitizeString($string) {
        return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize email
     */
    public static function sanitizeEmail($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Validate form field
     */
    public function validate($field, $value, $rules) {
        foreach ($rules as $rule => $param) {
            switch ($rule) {
                case 'required':
                    if (empty($value)) {
                        $this->addError($field, ucfirst($field) . " is required");
                    }
                    break;
                case 'email':
                    if (!self::validateEmail($value)) {
                        $this->addError($field, ucfirst($field) . " must be a valid email");
                    }
                    break;
                case 'min_length':
                    if (strlen($value) < $param) {
                        $this->addError($field, ucfirst($field) . " must be at least {$param} characters");
                    }
                    break;
                case 'max_length':
                    if (strlen($value) > $param) {
                        $this->addError($field, ucfirst($field) . " must not exceed {$param} characters");
                    }
                    break;
                case 'numeric':
                    if (!is_numeric($value)) {
                        $this->addError($field, ucfirst($field) . " must be numeric");
                    }
                    break;
                case 'match':
                    if ($value !== $_POST[$param]) {
                        $this->addError($field, ucfirst($field) . " does not match");
                    }
                    break;
            }
        }
    }

    /**
     * Add error
     */
    private function addError($field, $message) {
        $this->errors[$field] = $message;
    }

    /**
     * Get errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Has errors
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
}

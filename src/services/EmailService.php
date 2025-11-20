<?php
/**
 * Email Service
 */

class EmailService {
    private $senderEmail;
    private $senderName;

    public function __construct() {
        $this->senderEmail = SITE_EMAIL ?? 'noreply@onlineshop.local';
        $this->senderName = SITE_NAME ?? 'Online Shop';
    }

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmation($userEmail, $userName, $orderId, $orderTotal) {
        $subject = "Order Confirmation - #{$orderId}";
        
        $body = "
            <h2>Thank you for your order!</h2>
            <p>Dear {$userName},</p>
            <p>Your order has been received and is being processed.</p>
            <p><strong>Order Details:</strong></p>
            <p>Order ID: #{$orderId}</p>
            <p>Total: $" . number_format($orderTotal, 2) . "</p>
            <p>You will receive a shipping confirmation email once your order is dispatched.</p>
            <p>Thank you for shopping with us!</p>
        ";

        return $this->send($userEmail, $subject, $body);
    }

    /**
     * Send shipping notification email
     */
    public function sendShippingNotification($userEmail, $userName, $orderId, $trackingNumber) {
        $subject = "Your order has been shipped - #{$orderId}";
        
        $body = "
            <h2>Your order is on the way!</h2>
            <p>Dear {$userName},</p>
            <p>Your order #{$orderId} has been shipped.</p>
            <p><strong>Tracking Number:</strong> {$trackingNumber}</p>
            <p>Click <a href='https://track.example.com/{$trackingNumber}'>here</a> to track your package.</p>
            <p>Thank you for your patience!</p>
        ";

        return $this->send($userEmail, $subject, $body);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset($userEmail, $resetLink) {
        $subject = "Password Reset Request";
        
        $body = "
            <h2>Password Reset Request</h2>
            <p>Click <a href='{$resetLink}'>here</a> to reset your password.</p>
            <p>This link will expire in 1 hour.</p>
            <p>If you did not request this, please ignore this email.</p>
        ";

        return $this->send($userEmail, $subject, $body);
    }

    /**
     * Send welcome email
     */
    public function sendWelcomeEmail($userEmail, $userName) {
        $subject = "Welcome to " . ($this->senderName);
        
        $body = "
            <h2>Welcome!</h2>
            <p>Hello {$userName},</p>
            <p>Thank you for creating an account with us.</p>
            <p>You can now browse and purchase from our store.</p>
            <p>Happy shopping!</p>
        ";

        return $this->send($userEmail, $subject, $body);
    }

    /**
     * Send email
     */
    private function send($toEmail, $subject, $body) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: " . $this->senderName . " <" . $this->senderEmail . ">" . "\r\n";

        return mail($toEmail, $subject, $body, $headers);
    }

    /**
     * Send admin notification
     */
    public function sendAdminNotification($subject, $message) {
        $adminEmail = ADMIN_EMAIL ?? 'admin@onlineshop.local';
        return $this->send($adminEmail, $subject, $message);
    }
}

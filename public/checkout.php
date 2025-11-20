<?php
/**
 * Handles checkout & payment process
 */
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/controllers/OrderController.php';
require_once __DIR__ . '/../src/services/OrderService.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

$orderController = new OrderController();

// Handle checkout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderData = [
        'user_id' => $_SESSION['user_id'],
        'shipping_address' => $_POST['shipping_address'] ?? '',
        'payment_method' => $_POST['payment_method'] ?? '',
    ];
    
    $orderId = $orderController->createOrder($orderData);
    
    if ($orderId) {
        // Process payment
        $paymentResult = $orderController->processPayment($orderId, $_POST['card_number'] ?? '');
        
        if ($paymentResult) {
            $_SESSION['order_id'] = $orderId;
            header('Location: order-confirmation.php?id=' . $orderId);
            exit;
        } else {
            $error = "Payment processing failed. Please try again.";
        }
    } else {
        $error = "Failed to create order. Please try again.";
    }
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = array_sum(array_map(function($item) { return ($item['price'] ?? 0) * ($item['quantity'] ?? 1); }, $cart));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Online Shop</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Online Shop</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="cart.php">Cart</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <main>
        <h1>Checkout</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="checkout-container">
            <div class="checkout-form">
                <h2>Shipping Information</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="shipping_address">Shipping Address:</label>
                        <textarea name="shipping_address" id="shipping_address" required></textarea>
                    </div>

                    <h2>Payment Information</h2>
                    <div class="form-group">
                        <label for="payment_method">Payment Method:</label>
                        <select name="payment_method" id="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="card_number">Card Number:</label>
                        <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Place Order</button>
                </form>
            </div>

            <div class="order-summary">
                <h2>Order Summary</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
        </div>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>

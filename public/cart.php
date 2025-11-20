<?php
/**
 * User shopping cart
 */
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/controllers/CartController.php';

$cartController = new CartController();

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $cartController->addToCart($productId, $quantity);
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
        exit;
    }
    
    header('Location: cart.php');
    exit;
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $productId = (int)$_GET['remove'];
    $cartController->removeFromCart($productId);
    header('Location: cart.php');
    exit;
}

// Handle update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $cartController->updateQuantity($productId, $quantity);
    header('Location: cart.php');
    exit;
}

$cart = $cartController->getCart();
$total = $cartController->calculateTotal();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Online Shop</title>
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
        <h1>Shopping Cart</h1>
        
        <?php if (empty($cart)): ?>
            <p>Your cart is empty. <a href="index.php">Continue shopping</a></p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name'] ?? 'Product'); ?></td>
                            <td>৳<?php echo number_format($item['price'] ?? 0, 2); ?></td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id'] ?? 0; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity'] ?? 1; ?>" min="1">
                                    <button type="submit" name="update_quantity" class="btn-small">Update</button>
                                </form>
                            </td>
                            <td>৳<?php echo number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2); ?></td>
                            <td><a href="cart.php?remove=<?php echo $item['id'] ?? 0; ?>" class="btn-remove">Remove</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <p>Total: ৳<?php echo number_format($total, 2); ?></p>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </main>

    <script src="assets/js/cart.js"></script>
</body>
</html>

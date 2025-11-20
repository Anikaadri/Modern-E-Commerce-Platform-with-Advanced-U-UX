<?php
/**
 * User dashboard/profile
 */
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/controllers/UserController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=user.php');
    exit;
}

$userController = new UserController();
$user = $userController->getUserById($_SESSION['user_id']);
$orders = $userController->getUserOrders($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Online Shop</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Online Shop</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="cart.php">Cart</a>
            <a href="user.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h1>User Dashboard</h1>
        
        <div class="user-profile">
            <h2>Profile Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name'] ?? 'User'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
            <p><strong>Member Since:</strong> <?php echo htmlspecialchars($user['created_at'] ?? ''); ?></p>
            <a href="edit-profile.php" class="btn">Edit Profile</a>
        </div>

        <div class="user-orders">
            <h2>Order History</h2>
            <?php if (empty($orders)): ?>
                <p>You haven't placed any orders yet. <a href="index.php">Start shopping</a></p>
            <?php else: ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id'] ?? ''; ?></td>
                                <td><?php echo htmlspecialchars($order['created_at'] ?? ''); ?></td>
                                <td>৳<?php echo number_format($order['total'] ?? 0, 2); ?></td>
                                <td><?php echo htmlspecialchars($order['status'] ?? 'Pending'); ?></td>
                                <td><a href="order-details.php?id=<?php echo $order['id'] ?? ''; ?>" class="btn-small">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>

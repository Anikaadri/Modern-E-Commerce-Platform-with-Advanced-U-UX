<?php
/**
 * User Profile Page
 * View and edit profile, change password, view orders
 */
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/services/UserService.php';
require_once __DIR__ . '/../src/services/OrderService.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=profile.php');
    exit;
}

$userService = new UserService();
$orderService = new OrderService();
$userId = $_SESSION['user_id'];
$user = $userService->getUserById($userId);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'update_profile') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address' => $_POST['address'] ?? '',
                'city' => $_POST['city'] ?? '',
                'zip' => $_POST['zip'] ?? ''
            ];
            
            if ($userService->updateProfile($userId, $data)) {
                $_SESSION['user_name'] = $data['name']; // Update session
                echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            } else {
                throw new Exception('Failed to update profile');
            }
        } elseif ($action === 'change_password') {
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            if ($new !== $confirm) {
                throw new Exception('New passwords do not match');
            }
            
            if ($userService->changePassword($userId, $current, $new)) {
                echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
            } else {
                throw new Exception('Incorrect current password');
            }
        } else {
            throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Get orders
$orders = $orderService->getUserOrders($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Online Shop</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/premium-design.css">
    <link rel="stylesheet" href="assets/css/profile-page.css">
    <link rel="stylesheet" href="assets/css/draggable-header.css">
</head>
<body>
    <!-- Header will be included via JS or PHP if component existed, for now manual or include -->
    <?php include 'components/header_stub.php'; // Assuming we might have one, or just copy header code ?>
    <!-- Using the standard header structure for now -->
    <header>
        <div class="header-content">
            <div class="logo">
                <a href="index.php">ONLINE SHOP</a>
            </div>
            <nav>
                <a href="index.php">Home</a>
                <a href="cart.php">Cart</a>
                <a href="profile.php" class="active">Profile</a>
                <a href="login.php?logout=1">Logout</a>
            </nav>
        </div>
    </header>

    <main class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>

        <div class="profile-content">
            <!-- Sidebar Navigation -->
            <aside class="profile-sidebar">
                <button class="tab-btn active" data-tab="info">
                    <span>👤</span> Personal Info
                </button>
                <button class="tab-btn" data-tab="orders">
                    <span>📦</span> My Orders
                </button>
                <button class="tab-btn" data-tab="security">
                    <span>🔒</span> Security
                </button>
            </aside>

            <!-- Content Area -->
            <div class="profile-panels">
                <!-- Personal Info Panel -->
                <div class="tab-panel active" id="info">
                    <h2>Personal Information</h2>
                    <form id="profile-form" class="premium-form">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>ZIP Code</label>
                                <input type="text" name="zip" value="<?php echo htmlspecialchars($user['zip'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>

                <!-- Orders Panel -->
                <div class="tab-panel" id="orders">
                    <h2>Order History</h2>
                    <?php if (empty($orders)): ?>
                        <div class="empty-state">
                            <div class="icon">🛍️</div>
                            <p>You haven't placed any orders yet.</p>
                            <a href="index.php" class="btn btn-secondary">Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="orders-list">
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <span class="order-id">#<?php echo $order['id']; ?></span>
                                        <span class="order-date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                                        <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                    <div class="order-details">
                                        <p>Total: <strong>৳<?php echo number_format($order['total_amount'] ?? 0, 2); ?></strong></p>
                                        <p class="items-count"><?php echo $order['items_count'] ?? 0; ?> items</p>
                                    </div>
                                    <button class="btn btn-sm btn-outline" onclick="viewOrder(<?php echo $order['id']; ?>)">View Details</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Security Panel -->
                <div class="tab-panel" id="security">
                    <h2>Security Settings</h2>
                    <form id="password-form" class="premium-form">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required minlength="8">
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/features.js"></script>
    <script src="assets/js/draggable-header.js"></script>
    <script src="assets/js/profile-page.js"></script>
</body>
</html>

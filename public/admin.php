<?php
/**
 * Admin dashboard (manage all items/categories/orders)
 */
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/controllers/AdminController.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php?redirect=admin.php');
    exit;
}

$adminController = new AdminController();
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

switch ($action) {
    case 'products':
        $products = $adminController->getAllProducts();
        break;
    case 'categories':
        $categories = $adminController->getAllCategories();
        break;
    case 'orders':
        $orders = $adminController->getAllOrders();
        break;
    case 'users':
        $users = $adminController->getAllUsers();
        break;
    default:
        $dashboard = $adminController->getDashboardStats();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Online Shop</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="index.php">View Store</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <aside class="admin-sidebar">
            <ul>
                <li><a href="admin.php?action=dashboard" class="<?php echo $action === 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="admin.php?action=products" class="<?php echo $action === 'products' ? 'active' : ''; ?>">Products</a></li>
                <li><a href="admin.php?action=categories" class="<?php echo $action === 'categories' ? 'active' : ''; ?>">Categories</a></li>
                <li><a href="admin.php?action=orders" class="<?php echo $action === 'orders' ? 'active' : ''; ?>">Orders</a></li>
                <li><a href="admin.php?action=users" class="<?php echo $action === 'users' ? 'active' : ''; ?>">Users</a></li>
            </ul>
        </aside>

        <section class="admin-content">
            <?php if ($action === 'dashboard'): ?>
                <h2>Dashboard Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Products</h3>
                        <p><?php echo $dashboard['total_products'] ?? 0; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Orders</h3>
                        <p><?php echo $dashboard['total_orders'] ?? 0; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Revenue</h3>
                        <p>$<?php echo number_format($dashboard['total_revenue'] ?? 0, 2); ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Users</h3>
                        <p><?php echo $dashboard['total_users'] ?? 0; ?></p>
                    </div>
                </div>

            <?php elseif ($action === 'products'): ?>
                <h2>Products Management</h2>
                <a href="admin-product-form.php" class="btn">Add New Product</a>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products ?? [] as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td>
                                    <a href="admin-product-form.php?id=<?php echo $product['id']; ?>" class="btn-small">Edit</a>
                                    <a href="admin.php?action=delete_product&id=<?php echo $product['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif ($action === 'categories'): ?>
                <h2>Categories Management</h2>
                <a href="admin-category-form.php" class="btn">Add New Category</a>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories ?? [] as $category): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars($category['description']); ?></td>
                                <td>
                                    <a href="admin-category-form.php?id=<?php echo $category['id']; ?>" class="btn-small">Edit</a>
                                    <a href="admin.php?action=delete_category&id=<?php echo $category['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif ($action === 'orders'): ?>
                <h2>Orders Management</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders ?? [] as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td>$<?php echo number_format($order['total'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                                <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                                <td>
                                    <a href="admin-order-details.php?id=<?php echo $order['id']; ?>" class="btn-small">View</a>
                                    <a href="admin.php?action=update_order_status&id=<?php echo $order['id']; ?>" class="btn-small">Update Status</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif ($action === 'users'): ?>
                <h2>Users Management</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users ?? [] as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                <td>
                                    <a href="admin-user-details.php?id=<?php echo $user['id']; ?>" class="btn-small">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>
    <script src="assets/js/admin.js"></script>
</body>
</html>

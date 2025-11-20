<?php
/**
 * User login/register
 */
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/services/AuthService.php';

$authService = new AuthService();
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'login';
$error = '';
$success = '';

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    $response = ['success' => false, 'message' => ''];
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($mode === 'register') {
        $name = $_POST['name'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
            if ($isAjax) {
                echo json_encode(['success' => false, 'message' => $error]);
                exit;
            }
        } else {
            $result = $authService->register($name, $email, $password);
            if ($result) {
                $success = "Registration successful! Please log in.";
                $mode = 'login';
                if ($isAjax) {
                    echo json_encode(['success' => true, 'message' => $success, 'redirect' => 'login.php']);
                    exit;
                }
            } else {
                $error = "Email already exists.";
                if ($isAjax) {
                    echo json_encode(['success' => false, 'message' => $error]);
                    exit;
                }
            }
        }
    } else {
        try {
            $user = $authService->login($email, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                
                if ($isAjax) {
                    echo json_encode(['success' => true, 'message' => 'Login successful!', 'redirect' => $redirect]);
                    exit;
                }
                
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = "Invalid email or password.";
                if ($isAjax) {
                    echo json_encode(['success' => false, 'message' => $error]);
                    exit;
                }
            }
        } catch (Exception $e) {
            $error = "Login Error: " . $e->getMessage();
            if ($isAjax) {
                echo json_encode(['success' => false, 'message' => $error]);
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $mode === 'login' ? 'Login' : 'Register'; ?> - Online Shop</title>
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
        <div class="auth-container">
            <h1><?php echo $mode === 'login' ? 'Login' : 'Register'; ?></h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST">
                <?php if ($mode === 'register'): ?>
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <?php if ($mode === 'register'): ?>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary"><?php echo $mode === 'login' ? 'Login' : 'Register'; ?></button>
            </form>

            <p>
                <?php if ($mode === 'login'): ?>
                    Don't have an account? <a href="login.php?mode=register">Register here</a>
                <?php else: ?>
                    Already have an account? <a href="login.php?mode=login">Login here</a>
                <?php endif; ?>
            </p>
        </div>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>

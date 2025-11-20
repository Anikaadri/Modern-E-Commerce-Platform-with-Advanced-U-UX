<?php
/**
 * Header Component
 */
?>
<header>
    <div class="container">
        <div class="header-top">
            <h1><a href="index.php">Online Shop</a></h1>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#categories">Categories</a></li>
                    <li><a href="#deals">Deals</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>

            <div class="header-right">
                <div class="search-box">
                    <form method="GET" action="index.php">
                        <input type="text" name="search" placeholder="Search products...">
                        <button type="submit">Search</button>
                    </form>
                </div>

                <div class="header-icons">
                    <a href="cart.php" class="cart-icon">
                        <span>Cart</span>
                        <span class="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                    </a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="user.php" class="user-icon">Account</a>
                        <a href="logout.php" class="logout-icon">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="login-icon">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>

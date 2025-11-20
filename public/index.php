<?php
/**
 * Homepage: featured products, categories, search
 */
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/config/settings.php';
require_once __DIR__ . '/../src/controllers/ProductController.php';

$productController = new ProductController();
$featuredProducts = $productController->getFeaturedProducts() ?? [];
$categories = $productController->getCategories() ?? [];
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

if ($searchQuery) {
    $products = $productController->searchProducts($searchQuery) ?? [];
} else {
    $products = $featuredProducts;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Shop - Premium Shopping Experience</title>
    <meta name="description" content="Discover premium products with our modern online shop featuring advanced search, wishlist, and smooth animations">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/premium-design.css">
    <link rel="stylesheet" href="assets/css/draggable-header.css">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #ec4899;
            --accent: #f59e0b;
            --dark: #0f172a;
            --light: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-400: #cbd5e1;
            --gray-600: #475569;
            --gray-700: #334155;
            --success: #10b981;
        }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6; 
            color: var(--gray-700);
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            min-height: 100vh;
            overflow-x: hidden;
            padding-top: 180px; /* Space for header */
        }
        
        /* Header & Navigation */
        header { 
            background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%);
            color: white; 
            padding: 1.5rem 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 100;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        header h1 { 
            margin-bottom: 1.2rem;
            font-size: 2.2rem;
            font-weight: 900;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #fff 0%, #e0e7ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-transform: uppercase;
            tracking: 2px;
        }
        
        header nav { 
            display: flex; 
            gap: 3rem; 
            margin: 1.5rem 0;
            align-items: center;
        }
        
        header a { 
            color: #e2e8f0;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            text-transform: capitalize;
            letter-spacing: 0.5px;
        }
        
        header a:hover { 
            color: var(--secondary);
            text-shadow: 0 0 20px rgba(236, 72, 153, 0.5);
        }
        
        header a::before {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--secondary) 0%, var(--accent) 100%);
            transition: width 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border-radius: 2px;
        }
        
        header a:hover::before {
            width: 100%;
        }
        
        header form { 
            display: flex; 
            gap: 0.75rem;
            margin: 1.5rem 0;
            background: rgba(255, 255, 255, 0.08);
            padding: 0.75rem;
            border-radius: 16px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        header input { 
            padding: 0.9rem 1.2rem;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.95);
            color: var(--dark);
            font-size: 0.95rem;
            flex: 1;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        header input::placeholder {
            color: var(--gray-600);
            font-weight: 500;
        }
        
        header input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.3), 0 4px 15px rgba(99, 102, 241, 0.2);
            transform: scale(1.02);
        }
        
        header button { 
            padding: 0.9rem 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        
        header button::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
            transition: all 0.5s ease;
        }
        
        header button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(99, 102, 241, 0.5);
        }
        
        header button:active {
            transform: translateY(-1px);
        }
        
        /* Main Content */
        main { 
            max-width: 1400px;
            margin: 4rem auto;
            padding: 0 2rem;
            animation: slideUp 0.6s ease-out;
        }
        
        /* Slideshow/Carousel */
        .slideshow-container {
            position: relative;
            width: 100%;
            height: 500px;
            margin-bottom: 5rem;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(99, 102, 241, 0.3);
        }
        
        .slides-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        
        .slide {
            display: none;
            width: 100%;
            height: 100%;
            animation: fadeSlide 0.8s ease-in-out;
        }
        
        .slide.active {
            display: flex;
            align-items: center;
        }
        
        .slide-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            height: 100%;
            padding: 3rem;
            gap: 3rem;
        }
        
        .slide-image {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: slideInLeft 0.6s ease-out;
        }
        
        .placeholder-image {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8rem;
            font-weight: 900;
            color: white;
            box-shadow: 0 20px 60px rgba(99, 102, 241, 0.4);
        }
        
        .slide-text {
            flex: 1;
            color: white;
            animation: slideInRight 0.6s ease-out;
        }
        
        .slide-text h2 {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .slide-text p {
            font-size: 1.15rem;
            margin-bottom: 2rem;
            color: #cbd5e1;
            line-height: 1.6;
            max-width: 500px;
        }
        
        .slide-price {
            font-size: 2.5rem;
            font-weight: 900;
            color: #f59e0b;
            margin-bottom: 2rem;
        }
        
        .slide-btn {
            display: inline-block;
            padding: 1.2rem 3rem;
            background: linear-gradient(135deg, #f59e0b 0%, #ec4899 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
        }
        
        .slide-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(245, 158, 11, 0.5);
        }
        
        /* Slide Navigation */
        .slide-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 1rem 1.2rem;
            font-size: 1.5rem;
            cursor: pointer;
            border-radius: 50%;
            transition: all 0.3s ease;
            z-index: 10;
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }
        
        .slide-nav:hover {
            background: rgba(255, 255, 255, 0.4);
            border-color: rgba(255, 255, 255, 0.6);
            transform: translateY(-50%) scale(1.1);
        }
        
        .slide-nav.prev {
            left: 2rem;
        }
        
        .slide-nav.next {
            right: 2rem;
        }
        
        /* Slide Indicators */
        .slide-indicators {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.8rem;
            z-index: 10;
        }
        
        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .indicator.active {
            background: #f59e0b;
            border-color: #fbbf24;
            width: 30px;
            border-radius: 10px;
            transform: scale(1.1);
        }
        
        .indicator:hover {
            background: rgba(255, 255, 255, 0.6);
        
        
        /* Categories Section */
        .categories { 
            margin-bottom: 5rem;
        }
        
        .categories h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 2rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 1rem;
        }
        
        .categories h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 5px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 3px;
        }
        
        .categories ul { 
            list-style: none; 
            display: flex; 
            gap: 1.5rem; 
            flex-wrap: wrap;
        }
        
        .categories li {
            display: inline-block;
        }
        
        .categories a { 
            padding: 0.9rem 2rem;
            background: white;
            border-radius: 30px;
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 2px solid var(--gray-200);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            text-transform: capitalize;
            letter-spacing: 0.3px;
        }
        
        .categories a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            transition: left 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: -1;
        }
        
        .categories a:hover { 
            color: white;
            border-color: transparent;
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.4);
            left: 0;
        }
        
        .categories a:hover::before {
            left: 0;
        }
        
        /* Products Section */
        .products { 
            margin-top: 5rem;
        }
        
        .products h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 2.5rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 1rem;
        }
        
        .products h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 5px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 3px;
        }
        
        .product-grid { 
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 3rem;
            padding: 1rem 0;
        }
        
        .product-card { 
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid var(--gray-100);
            animation: fadeInScale 0.6s ease-out;
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 0%, rgba(99, 102, 241, 0.15) 50%, transparent 100%);
            transition: left 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 0;
        }
        
        .product-card:hover::before {
            left: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-12px) rotateY(2deg);
            box-shadow: 0 25px 50px rgba(99, 102, 241, 0.25);
            border-color: var(--primary);
        }
        
        .product-card > * {
            position: relative;
            z-index: 1;
        }
        
        .product-card img { 
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .product-card h3 { 
            margin: 1.2rem 0 0.8rem 0;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.3;
        }
        
        .product-card p { 
            color: var(--gray-600);
            font-size: 0.9rem;
            margin-bottom: 1.2rem;
            line-height: 1.6;
        }
        
        .product-card .price { 
            color: #f59e0b;
            font-weight: 900;
            font-size: 1.8rem;
            margin: 1.5rem 0;
            display: flex;
            align-items: baseline;
            gap: 0.3rem;
        }
        
        .product-card .price::before {
            content: '৳';
            font-size: 1.3rem;
        }
        
        .product-card .btn { 
            display: inline-block;
            margin-top: 1.5rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.35);
            width: 100%;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        
        .product-card .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }
        
        .product-card .btn:hover::after {
            width: 300px;
            height: 300px;
        }
        
        .product-card .btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(99, 102, 241, 0.45);
        }
        
        .product-card .btn:active {
            transform: translateY(-2px);
        }
        
        /* No Products Message */
        .products > p {
            text-align: center;
            padding: 4rem;
            color: var(--gray-600);
            font-size: 1.15rem;
            font-weight: 500;
        }
        
        /* Footer */
        footer { 
            background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            margin-top: 6rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        footer p {
            color: #cbd5e1;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        
        /* Animations */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes fadeSlide {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .slide-content {
                flex-direction: column;
                padding: 2rem;
                gap: 2rem;
            }
            
            .slide-text h2 {
                font-size: 2rem;
            }
            
            .placeholder-image {
                width: 200px;
                height: 200px;
                font-size: 5rem;
            }
        }
        
        @media (max-width: 768px) {
            header {
                padding: 1.2rem 1rem;
            }
            
            header h1 {
                font-size: 1.8rem;
                margin-bottom: 1rem;
            }
            
            header nav {
                gap: 1.5rem;
                margin-bottom: 1.2rem;
                font-size: 0.9rem;
            }
            
            header form {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            main {
                padding: 0 1rem;
                margin: 2.5rem auto;
            }
            
            .slideshow-container {
                height: 400px;
                margin-bottom: 3rem;
            }
            
            .slide-content {
                flex-direction: column;
                padding: 1.5rem;
                gap: 1.5rem;
            }
            
            .slide-text h2 {
                font-size: 1.5rem;
            }
            
            .slide-text p {
                font-size: 0.9rem;
            }
            
            .slide-price {
                font-size: 1.8rem;
            }
            
            .slide-btn {
                padding: 0.8rem 1.5rem;
                font-size: 0.85rem;
            }
            
            .slide-nav {
                width: 45px;
                height: 45px;
                padding: 0.75rem;
                font-size: 1.2rem;
            }
            
            .slide-nav.prev {
                left: 1rem;
            }
            
            .slide-nav.next {
                right: 1rem;
            }
            
            .placeholder-image {
                width: 150px;
                height: 150px;
                font-size: 3rem;
            }
            
            .categories h2,
            .products h2 {
                font-size: 1.6rem;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 2rem;
            }
            
            .categories ul {
                gap: 0.8rem;
            }
            
            .product-card {
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .slideshow-container {
                height: 300px;
                margin-bottom: 2rem;
                border-radius: 16px;
            }
            
            .slide-content {
                padding: 1rem;
                gap: 1rem;
            }
            
            .slide-text h2 {
                font-size: 1.2rem;
            }
            
            .slide-text p {
                display: none;
            }
            
            .slide-price {
                font-size: 1.5rem;
            }
            
            .slide-btn {
                padding: 0.7rem 1.2rem;
                font-size: 0.75rem;
                letter-spacing: 0.3px;
            }
            
            .slide-nav {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .placeholder-image {
                width: 100px;
                height: 100px;
                font-size: 2rem;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Online Shop</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="cart.php">Cart</a>
            <a href="cart.php" style="position: relative;">
                Wishlist
                <span class="wishlist-counter badge-counter" style="display: none;">0</span>
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">Profile</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
            <a href="admin.php">Admin</a>
        </nav>
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>
    </header>

    <main>
        <!-- Product Slideshow/Carousel -->
        <section class="slideshow-container">
            <div class="slides-wrapper">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $index => $product): ?>
                        <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="slide-content">
                                <div class="slide-image">
                                    <div class="placeholder-image">
                                        <span><?php echo htmlspecialchars(substr($product['name'] ?? 'Product', 0, 1)); ?></span>
                                    </div>
                                </div>
                                <div class="slide-text">
                                    <h2><?php echo htmlspecialchars($product['name'] ?? 'Unknown Product'); ?></h2>
                                    <p><?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                                    <div class="slide-price">৳<?php echo number_format(intval($product['price'] ?? 0)); ?></div>
                                    <a href="product.php?id=<?php echo $product['id'] ?? '#'; ?>" class="slide-btn">Shop Now</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Slide Controls -->
            <button class="slide-nav prev" onclick="changeSlide(-1)">❮</button>
            <button class="slide-nav next" onclick="changeSlide(1)">❯</button>

            <!-- Slide Indicators -->
            <div class="slide-indicators">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $index => $product): ?>
                        <span class="indicator <?php echo $index === 0 ? 'active' : ''; ?>" onclick="currentSlide(<?php echo $index; ?>)"></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="categories">
            <h2>Categories</h2>
            <ul>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <li><a href="category.php?id=<?php echo $category['id'] ?? '#'; ?>"><?php echo htmlspecialchars($category['name'] ?? 'Unknown'); ?></a></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No categories available</li>
                <?php endif; ?>
            </ul>
        </section>

        <section class="products">
            <h2>Featured Products</h2>
            <div class="product-grid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" data-product-id="<?php echo $product['id'] ?? ''; ?>">
                            <div style="position: relative;">
                                <!-- Wishlist Button -->
                                <button class="wishlist-btn" 
                                        data-product-id="<?php echo $product['id'] ?? ''; ?>"
                                        onclick="toggleWishlist('<?php echo $product['id'] ?? ''; ?>', '<?php echo htmlspecialchars($product['name'] ?? 'Product'); ?>')"
                                        style="position: absolute; top: 1rem; right: 1rem; background: white; border: none; width: 44px; height: 44px; border-radius: 50%; cursor: pointer; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.15); transition: all 0.3s; z-index: 10; display: flex; align-items: center; justify-content: center;"
                                        onmouseover="this.style.transform='scale(1.1)'"
                                        onmouseout="this.style.transform='scale(1)'">
                                    🤍
                                </button>
                                
                                <!-- Product Image Placeholder -->
                                <div style="width: 100%; height: 250px; background: linear-gradient(135deg, var(--primary-500) 0%, var(--secondary-500) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 4rem; font-weight: 900; color: white; margin-bottom: 1.5rem; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
                                    <?php echo htmlspecialchars(substr($product['name'] ?? 'P', 0, 1)); ?>
                                </div>
                            </div>
                            
                            <h3><?php echo htmlspecialchars($product['name'] ?? 'Unknown Product'); ?></h3>
                            <p><?php echo substr(htmlspecialchars($product['description'] ?? ''), 0, 100); ?>...</p>
                            <p class="price">৳<?php echo number_format(intval($product['price'] ?? 0)); ?></p>
                            
                            <!-- Action Buttons -->
                            <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem; flex-wrap: wrap;">
                                <button class="btn btn-primary" 
                                        onclick="addToCart('<?php echo $product['id'] ?? ''; ?>')"
                                        style="flex: 1; padding: 0.75rem; font-size: 0.9rem;">
                                    Add to Cart
                                </button>
                                <button class="btn btn-buy-now" 
                                        onclick="buyNow('<?php echo $product['id'] ?? ''; ?>')"
                                        style="flex: 1; padding: 0.75rem; font-size: 0.9rem;">
                                    Buy Now
                                </button>
                                <a href="product.php?id=<?php echo $product['id'] ?? '#'; ?>" 
                                   class="btn btn-outline"
                                   style="width: 100%; padding: 0.75rem; font-size: 0.9rem; text-align: center; margin-top: 0.5rem;">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products available</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Online Shop. All rights reserved.</p>
    </footer>
    
    <!-- Floating Action Buttons -->
    <button class="fab fab-scroll-top" 
            onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
            style="display: none;"
            title="Scroll to top">
        ↑
    </button>
    
    <!-- Wishlist FAB -->
    <a href="cart.php" 
       class="fab" 
       style="bottom: 140px; background: linear-gradient(135deg, var(--secondary-500) 0%, var(--accent-500) 100%);"
       title="View Wishlist">
        <span style="position: relative;">
            ❤️
            <span class="wishlist-counter badge-counter" style="display: none;">0</span>
        </span>
    </a>
    
    <!-- Include Premium JavaScript -->
    <script src="assets/js/features.js"></script>
    <script src="assets/js/animations.js"></script>
    <script src="assets/js/mobile-menu.js"></script>
    <script src="assets/js/draggable-header.js"></script>
    
    <script>
        let slideIndex = 0;
        let slideTimer;
        
        function changeSlide(n) {
            clearTimeout(slideTimer);
            showSlide(slideIndex += n);
            autoSlide();
        }
        
        function currentSlide(n) {
            clearTimeout(slideTimer);
            showSlide(slideIndex = n);
            autoSlide();
        }
        
        function showSlide(n) {
            const slides = document.querySelectorAll('.slide');
            const indicators = document.querySelectorAll('.indicator');
            
            if (slides.length === 0) return;
            
            if (n >= slides.length) {
                slideIndex = 0;
            }
            if (n < 0) {
                slideIndex = slides.length - 1;
            }
            
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(ind => ind.classList.remove('active'));
            
            slides[slideIndex].classList.add('active');
            if (indicators[slideIndex]) {
                indicators[slideIndex].classList.add('active');
            }
        }
        
        function autoSlide() {
            slideTimer = setTimeout(() => {
                slideIndex++;
                showSlide(slideIndex);
                autoSlide();
            }, 5000);
        }
        
        // Initialize slideshow
        document.addEventListener('DOMContentLoaded', function() {
            showSlide(slideIndex);
            autoSlide();
        });
    </script>
</body>
</html>

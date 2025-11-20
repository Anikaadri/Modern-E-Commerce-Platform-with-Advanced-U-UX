<?php
/**
 * Single product detail, dynamic content
 */
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/controllers/ProductController.php';
require_once __DIR__ . '/../src/controllers/ReviewController.php';

$productController = new ProductController();
$reviewController = new ReviewController();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    header('Location: index.php');
    exit;
}

$product = $productController->getProductById($productId);

if (!$product) {
    header('HTTP/1.1 404 Not Found');
    echo "Product not found";
    exit;
}

// Handle Review Submission
$reviewError = '';
$reviewSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) {
        $reviewError = "You must be logged in to leave a review.";
    } else {
        $reviewData = [
            'product_id' => $productId,
            'user_id' => $_SESSION['user_id'],
            'rating' => (int)$_POST['rating'],
            'comment' => trim($_POST['comment'])
        ];
        
        $result = $reviewController->addReview($reviewData);
        
        if ($result['success']) {
            $reviewSuccess = "Review submitted successfully!";
        } else {
            $reviewError = implode("<br>", $result['errors']);
        }
    }
}

$relatedProducts = $productController->getRelatedProducts($product['category_id'], $productId);
$reviews = $reviewController->getReviewsByProductId($productId);
$avgRatingData = $reviewController->getAverageRating($productId);
$avgRating = $avgRatingData['rating'];
$reviewCount = $avgRatingData['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Online Shop</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Review Section Styles */
        .review-section {
            margin-top: 40px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .review-summary {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stars {
            color: #f39c12;
            font-size: 1.2em;
        }
        .review-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .review-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .reviewer-name {
            font-weight: bold;
        }
        .review-date {
            color: #777;
            font-size: 0.9em;
        }
        .review-form {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-top: 30px;
        }
        .rating-input {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .rating-input label {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Online Shop</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="cart.php">Cart</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="login.php?logout=true">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <div class="product-detail">
            <div class="product-image">
                <img src="assets/images/<?php echo htmlspecialchars($product['image'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['name'] ?? 'Product'); ?>">
            </div>
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name'] ?? 'Product'); ?></h1>
                
                <div class="review-summary">
                    <div class="stars">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= round($avgRating) ? '★' : '☆';
                        }
                        ?>
                    </div>
                    <span><?php echo $avgRating; ?> / 5 (<?php echo $reviewCount; ?> reviews)</span>
                </div>

                <p class="price">৳<?php echo number_format($product['price'] ?? 0, 2); ?></p>
                <p class="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                <p class="stock">In Stock: <?php echo $product['stock'] ?? 0; ?> units</p>
                
                <form method="POST" action="cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock'] ?? 10; ?>">
                    <button type="submit" class="btn btn-add-cart">Add to Cart</button>
                </form>
            </div>
        </div>

        <section class="review-section">
            <h2>Customer Reviews</h2>
            
            <?php if ($reviewSuccess): ?>
                <div class="alert alert-success"><?php echo $reviewSuccess; ?></div>
            <?php endif; ?>
            
            <?php if ($reviewError): ?>
                <div class="alert alert-error"><?php echo $reviewError; ?></div>
            <?php endif; ?>

            <div class="review-list">
                <?php if (empty($reviews)): ?>
                    <p>No reviews yet. Be the first to review this product!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <span class="reviewer-name"><?php echo htmlspecialchars($review->userName); ?></span>
                                <span class="review-date"><?php echo $review->getFormattedDate(); ?></span>
                            </div>
                            <div class="stars">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $review->rating ? '★' : '☆';
                                }
                                ?>
                            </div>
                            <p class="review-comment"><?php echo htmlspecialchars($review->comment); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="review-form">
                    <h3>Write a Review</h3>
                    <form method="POST">
                        <input type="hidden" name="submit_review" value="1">
                        <div class="form-group">
                            <label>Rating:</label>
                            <div class="rating-input">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label>
                                        <input type="radio" name="rating" value="<?php echo $i; ?>" required> <?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Comment:</label>
                            <textarea name="comment" id="comment" rows="4" required style="width: 100%; padding: 10px;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            <?php else: ?>
                <p style="margin-top: 20px;">Please <a href="login.php?redirect=product.php?id=<?php echo $productId; ?>">login</a> to leave a review.</p>
            <?php endif; ?>
        </section>

        <section class="related-products">
            <h2>Related Products</h2>
            <div class="product-grid">
                <?php foreach ($relatedProducts as $relProduct): ?>
                    <div class="product-card">
                        <img src="assets/images/<?php echo htmlspecialchars($relProduct['image'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($relProduct['name'] ?? 'Product'); ?>">
                        <h3><?php echo htmlspecialchars($relProduct['name'] ?? 'Product'); ?></h3>
                        <p class="price">৳<?php echo number_format($relProduct['price'] ?? 0, 2); ?></p>
                        <a href="product.php?id=<?php echo $relProduct['id'] ?? 0; ?>" class="btn">View</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>
    <script src="assets/js/product.js"></script>
</body>
</html>

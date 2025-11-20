<?php
/**
 * Sidebar Component
 */
?>
<aside class="sidebar">
    <div class="sidebar-section">
        <h3>Categories</h3>
        <ul class="category-list">
            <?php 
            $controller = new \CategoryController();
            $categories = $controller->getAllCategories();
            foreach ($categories as $category): 
            ?>
                <li>
                    <a href="category.php?id=<?php echo $category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="sidebar-section">
        <h3>Price Filter</h3>
        <form method="GET" action="index.php" class="price-filter">
            <div class="filter-group">
                <label for="min-price">Min Price:</label>
                <input type="number" id="min-price" name="min_price" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>" min="0">
            </div>
            
            <div class="filter-group">
                <label for="max-price">Max Price:</label>
                <input type="number" id="max-price" name="max_price" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>" min="0">
            </div>
            
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <div class="sidebar-section">
        <h3>Special Offers</h3>
        <div class="offers-list">
            <div class="offer-item">
                <p>Free Shipping on orders over $100</p>
            </div>
            <div class="offer-item">
                <p>New customers get 10% off</p>
            </div>
        </div>
    </div>
</aside>

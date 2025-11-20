<?php
/**
 * Product Card Component
 */
?>
<div class="product-card">
    <div class="product-image">
        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php if ($product['is_featured']): ?>
            <span class="featured-badge">Featured</span>
        <?php endif; ?>
    </div>
    
    <div class="product-info">
        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
        <p class="description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
        
        <div class="product-footer">
            <div class="price">
                <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
            </div>
            
            <div class="stock-status">
                <?php if ($product['stock'] > 0): ?>
                    <span class="in-stock">In Stock</span>
                <?php else: ?>
                    <span class="out-of-stock">Out of Stock</span>
                <?php endif; ?>
            </div>
        </div>
        
        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
    </div>
</div>

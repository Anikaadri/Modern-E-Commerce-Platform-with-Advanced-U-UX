<?php
/**
 * Cart Item Component
 */
?>
<tr class="cart-item" data-product-id="<?php echo $item['id']; ?>">
    <td class="product-name">
        <a href="product.php?id=<?php echo $item['id']; ?>">
            <?php echo htmlspecialchars($item['name']); ?>
        </a>
    </td>
    
    <td class="product-price">
        $<?php echo number_format($item['price'], 2); ?>
    </td>
    
    <td class="product-quantity">
        <form method="POST" class="quantity-form">
            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
            <button type="submit" name="update_quantity" class="btn-small">Update</button>
        </form>
    </td>
    
    <td class="product-subtotal">
        $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
    </td>
    
    <td class="product-action">
        <a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn-remove" onclick="return confirm('Remove this item?');">Remove</a>
    </td>
</tr>

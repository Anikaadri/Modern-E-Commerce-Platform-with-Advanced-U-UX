<?php
require_once __DIR__ . '/../src/config/database.php';

try {
    global $database;

    // 1. Add Categories
    $categories = [
        'Men' => 'Fashion for men',
        'Women' => 'Fashion for women',
        'Electronics' => 'Gadgets and devices',
        'Accessories' => 'Bags, jewelry, and more',
        'Home & Living' => 'Decor and essentials'
    ];

    $categoryIds = [];

    foreach ($categories as $name => $desc) {
        // Check if exists
        $result = $database->query("SELECT id FROM categories WHERE name = ?", [$name]);
        $id = !empty($result) ? $result[0]['id'] : null;

        if (!$id) {
            $database->execute("INSERT INTO categories (name, description) VALUES (?, ?)", [$name, $desc]);
            $id = $database->lastInsertId();
            echo "Created category: $name\n";
        } else {
            echo "Category exists: $name\n";
        }
        $categoryIds[$name] = $id;
    }

    // 2. Add Products
    $products = [
        [
            'name' => 'Classic Men\'s T-Shirt',
            'description' => 'Premium cotton t-shirt for everyday comfort. Available in multiple sizes.',
            'price' => 1200,
            'stock' => 50,
            'category' => 'Men',
            'image' => 'men-tshirt.jpg' 
        ],
        [
            'name' => 'Slim Fit Jeans',
            'description' => 'Stylish slim fit jeans with durable denim fabric.',
            'price' => 2500,
            'stock' => 30,
            'category' => 'Men',
            'image' => 'men-jeans.jpg'
        ],
        [
            'name' => 'Leather Jacket',
            'description' => 'Genuine leather jacket for a bold look.',
            'price' => 8500,
            'stock' => 10,
            'category' => 'Men',
            'image' => 'men-jacket.jpg'
        ],
        [
            'name' => 'Floral Summer Dress',
            'description' => 'Lightweight and breezy dress perfect for summer.',
            'price' => 3500,
            'stock' => 25,
            'category' => 'Women',
            'image' => 'women-dress.jpg'
        ],
        [
            'name' => 'Designer Handbag',
            'description' => 'Elegant handbag to complement any outfit.',
            'price' => 5500,
            'stock' => 15,
            'category' => 'Women',
            'image' => 'women-bag.jpg'
        ],
        [
            'name' => 'Wireless Headphones',
            'description' => 'Noise-cancelling over-ear headphones with high fidelity sound.',
            'price' => 4500,
            'stock' => 40,
            'category' => 'Electronics',
            'image' => 'headphones.jpg'
        ],
        [
            'name' => 'Smart Watch Series 5',
            'description' => 'Track your fitness and stay connected on the go.',
            'price' => 12000,
            'stock' => 20,
            'category' => 'Electronics',
            'image' => 'smartwatch.jpg'
        ],
        [
            'name' => '4K Action Camera',
            'description' => 'Capture your adventures in stunning 4K resolution.',
            'price' => 15000,
            'stock' => 10,
            'category' => 'Electronics',
            'image' => 'camera.jpg'
        ],
        [
            'name' => 'Aviator Sunglasses',
            'description' => 'Classic aviator style with UV protection.',
            'price' => 1500,
            'stock' => 60,
            'category' => 'Accessories',
            'image' => 'sunglasses.jpg'
        ],
        [
            'name' => 'Minimalist Wallet',
            'description' => 'Slim leather wallet with RFID protection.',
            'price' => 950,
            'stock' => 100,
            'category' => 'Accessories',
            'image' => 'wallet.jpg'
        ],
        [
            'name' => 'Modern Desk Lamp',
            'description' => 'Adjustable LED desk lamp with wireless charging base.',
            'price' => 2800,
            'stock' => 35,
            'category' => 'Home & Living',
            'image' => 'lamp.jpg'
        ]
    ];

    foreach ($products as $p) {
        // Check if exists
        $result = $database->query("SELECT id FROM products WHERE name = ?", [$p['name']]);
        $id = !empty($result) ? $result[0]['id'] : null;

        if (!$id) {
            $catId = $categoryIds[$p['category']] ?? null;
            if ($catId) {
                $database->execute("INSERT INTO products (name, description, price, stock, category_id) VALUES (?, ?, ?, ?, ?)", 
                    [$p['name'], $p['description'], $p['price'], $p['stock'], $catId]);
                echo "Created product: {$p['name']}\n";
            }
        } else {
            echo "Product exists: {$p['name']}\n";
        }
    }

    echo "Seeding completed successfully!";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

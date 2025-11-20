<?php
require_once __DIR__ . '/../src/config/database.php';

global $database;

echo "<h1>Updating Categories...</h1>";

// Update existing categories with proper names
$updates = [
    1 => ['name' => 'Electronics', 'description' => 'Gadgets and devices'],
    2 => ['name' => 'Computers', 'description' => 'Laptops, desktops and accessories'],
    3 => ['name' => 'Software', 'description' => 'Digital products and applications'],
    25 => ['name' => 'Men', 'description' => 'Fashion for men'],
    26 => ['name' => 'Women', 'description' => 'Fashion for women'],
    27 => ['name' => 'Accessories', 'description' => 'Bags, jewelry, and more']
];

foreach ($updates as $id => $data) {
    try {
        $result = $database->execute(
            "UPDATE categories SET name = ?, description = ? WHERE id = ?",
            [$data['name'], $data['description'], $id]
        );
        echo "✓ Updated category ID $id to: {$data['name']}<br>";
    } catch (Exception $e) {
        echo "✗ Failed to update category ID $id: " . $e->getMessage() . "<br>";
    }
}

// Also add Home & Living if it doesn't exist
try {
    $result = $database->query("SELECT id FROM categories WHERE name = 'Home & Living'");
    if (empty($result)) {
        $database->execute(
            "INSERT INTO categories (name, description) VALUES (?, ?)",
            ['Home & Living', 'Decor and essentials']
        );
        echo "✓ Created new category: Home & Living<br>";
    }
} catch (Exception $e) {
    echo "Note: " . $e->getMessage() . "<br>";
}

echo "<br><h2>Current Categories:</h2>";
$categories = $database->query("SELECT * FROM categories ORDER BY id");
echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Description</th></tr>";
foreach ($categories as $cat) {
    echo "<tr><td>{$cat['id']}</td><td>{$cat['name']}</td><td>{$cat['description']}</td></tr>";
}
echo "</table>";

echo "<br><a href='index.php'>← Back to Homepage</a>";

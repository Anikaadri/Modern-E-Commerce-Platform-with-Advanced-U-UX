<?php
require_once __DIR__ . '/../src/config/database.php';

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->query("SELECT id, name, category_id FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>Product List</h1>";
echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Category ID</th></tr>";
foreach ($products as $p) {
    echo "<tr><td>{$p['id']}</td><td>" . htmlspecialchars($p['name'] ?? 'NULL') . "</td><td>{$p['category_id']}</td></tr>";
}
echo "</table>";

$stmt = $conn->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<h1>Category List</h1>";
echo "<table border='1'><tr><th>ID</th><th>Name</th></tr>";
foreach ($categories as $c) {
    echo "<tr><td>{$c['id']}</td><td>" . htmlspecialchars($c['name']) . "</td></tr>";
}
echo "</table>";

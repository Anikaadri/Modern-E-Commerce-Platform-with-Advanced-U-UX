<?php
require_once __DIR__ . '/../src/config/database.php';

global $database;

echo "<h1>Categories in Database:</h1>";
$categories = $database->query("SELECT * FROM categories ORDER BY id");

if (empty($categories)) {
    echo "<p>No categories found!</p>";
} else {
    echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Description</th></tr>";
    foreach ($categories as $cat) {
        echo "<tr>";
        echo "<td>" . ($cat['id'] ?? 'NULL') . "</td>";
        echo "<td>" . ($cat['name'] ?? 'NULL') . "</td>";
        echo "<td>" . ($cat['description'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

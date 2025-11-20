<?php
/**
 * Database Initialization Script
 * Run this once to create tables and seed initial data
 */

require_once __DIR__ . '/../src/config/database.php';

try {
    echo "<h1>🚀 Database Initialization</h1>";
    echo "<pre>";
    echo "Creating database tables...\n";

    // Create categories table
    $database->execute("
        CREATE TABLE IF NOT EXISTS categories (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✓ Categories table created\n";

    // Create products table
    $database->execute("
        CREATE TABLE IF NOT EXISTS products (
            id INT PRIMARY KEY AUTO_INCREMENT,
            category_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            stock INT DEFAULT 0,
            image VARCHAR(255),
            is_featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id)
        )
    ");
    echo "✓ Products table created\n";

    // Create users table
    $database->execute("
        CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            address TEXT,
            is_admin BOOLEAN DEFAULT FALSE,
            reset_token VARCHAR(255),
            reset_token_expiry DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "✓ Users table created\n";

    // Create orders table
    $database->execute("
        CREATE TABLE IF NOT EXISTS orders (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            total DECIMAL(10, 2) NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            shipping_address TEXT,
            payment_method VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    echo "✓ Orders table created\n";

    // Create order_items table
    $database->execute("
        CREATE TABLE IF NOT EXISTS order_items (
            id INT PRIMARY KEY AUTO_INCREMENT,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        )
    ");
    echo "✓ Order Items table created\n";

    // Create cart table
    $database->execute("
        CREATE TABLE IF NOT EXISTS cart (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_cart_item (user_id, product_id),
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        )
    ");
    echo "✓ Cart table created\n";

    // Create reviews table
    $database->execute("
        CREATE TABLE IF NOT EXISTS reviews (
            id INT PRIMARY KEY AUTO_INCREMENT,
            product_id INT NOT NULL,
            user_id INT NOT NULL,
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    echo "✓ Reviews table created\n";

    // Create discount_codes table
    $database->execute("
        CREATE TABLE IF NOT EXISTS discount_codes (
            id INT PRIMARY KEY AUTO_INCREMENT,
            code VARCHAR(50) NOT NULL UNIQUE,
            type VARCHAR(20) NOT NULL,
            value DECIMAL(10, 2) NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            expires_at DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✓ Discount Codes table created\n";

    // Seed initial categories
    echo "\nSeeding initial data...\n";
    
    $database->execute(
        "INSERT IGNORE INTO categories (name, description) VALUES (:name, :description)",
        [':name' => 'Electronics', ':description' => 'Computers, laptops, and electronic devices']
    );
    
    $database->execute(
        "INSERT IGNORE INTO categories (name, description) VALUES (:name, :description)",
        [':name' => 'Accessories', ':description' => 'Computer accessories and peripherals']
    );
    
    $database->execute(
        "INSERT IGNORE INTO categories (name, description) VALUES (:name, :description)",
        [':name' => 'Software', ':description' => 'Software and digital products']
    );
    
    echo "✓ Categories seeded\n";

    // Seed initial products
    $database->execute(
        "INSERT IGNORE INTO products (category_id, name, description, price, stock) 
         VALUES (:cat, :name, :desc, :price, :stock)",
        [
            ':cat' => 1,
            ':name' => 'Laptop Pro',
            ':desc' => 'High-performance laptop for professionals',
            ':price' => 168999,
            ':stock' => 15
        ]
    );

    $database->execute(
        "INSERT IGNORE INTO products (category_id, name, description, price, stock) 
         VALUES (:cat, :name, :desc, :price, :stock)",
        [
            ':cat' => 2,
            ':name' => 'Wireless Mouse',
            ':desc' => 'Ergonomic wireless mouse with long battery life',
            ':price' => 3899,
            ':stock' => 50
        ]
    );

    $database->execute(
        "INSERT IGNORE INTO products (category_id, name, description, price, stock) 
         VALUES (:cat, :name, :desc, :price, :stock)",
        [
            ':cat' => 2,
            ':name' => 'Mechanical Keyboard',
            ':desc' => 'Premium mechanical keyboard with RGB lighting',
            ':price' => 19499,
            ':stock' => 30
        ]
    );

    $database->execute(
        "INSERT IGNORE INTO products (category_id, name, description, price, stock) 
         VALUES (:cat, :name, :desc, :price, :stock)",
        [
            ':cat' => 1,
            ':name' => '4K Monitor',
            ':desc' => '27-inch 4K ultra HD monitor',
            ':price' => 51999,
            ':stock' => 20
        ]
    );

    echo "✓ Products seeded\n";

    // Create admin user
    $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $database->execute(
        "INSERT IGNORE INTO users (name, email, password, is_admin) 
         VALUES (:name, :email, :password, :admin)",
        [
            ':name' => 'Admin User',
            ':email' => 'admin@onlineshop.local',
            ':password' => $adminPassword,
            ':admin' => 1
        ]
    );
    echo "✓ Admin user created\n";

    echo "\n✅ Database initialization completed successfully!\n";
    echo "</pre>";

} catch (Exception $e) {
    echo "<h1>❌ Error</h1>";
    echo "<pre>";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "</pre>";
}
?>

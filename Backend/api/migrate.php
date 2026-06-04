<?php
require_once __DIR__ . '/../config/db.php';

echo "<h2>Starting Database Migration...</h2>";

// 1. Create Site Settings Table
$sql1 = "CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql1)) {
    echo "✅ Table 'site_settings' created or already exists.<br>";
} else {
    echo "❌ Error creating 'site_settings': " . $conn->error . "<br>";
}

// 2. Insert Default Settings
$defaults = [
    ['hero_title', 'Purely Organic. <br>Truly Powerful.'],
    ['hero_subtitle', 'The Ritual of Nature'],
    ['hero_description', 'Discover the healing power of nature with our hand-crafted, herbal care products made from 100% organic ingredients.'],
    ['hero_image', 'hero-bg.jpg']
];

foreach ($defaults as $row) {
    $stmt = $conn->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
    $stmt->bind_param("ss", $row[0], $row[1]);
    $stmt->execute();
}
echo "✅ Default site settings initialized.<br>";

// 3. Add Stock Column to Products
$sql2 = "SHOW COLUMNS FROM products LIKE 'stock'";
$res = $conn->query($sql2);
if ($res->num_rows === 0) {
    if ($conn->query("ALTER TABLE products ADD COLUMN stock INT DEFAULT 50")) {
        echo "✅ Column 'stock' added to 'products'.<br>";
    } else {
        echo "❌ Error adding 'stock': " . $conn->error . "<br>";
    }
} else {
    echo " ✅ Column 'stock' already exists.<br>";
}

// 4. Create Wishlist Table
$sql_wishlist = "CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, product_id)
)";
if ($conn->query($sql_wishlist)) {
    echo "✅ Table 'wishlist' initialized.<br>";
}

// 5. Create Blog Table
$sql_blog = "CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    author VARCHAR(100) DEFAULT 'Shaheen Wellness Team',
    status ENUM('Draft', 'Published') DEFAULT 'Published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql_blog)) {
    echo "✅ Table 'blog_posts' initialized.<br>";
}

echo "<br><strong>Migration Complete!</strong> <a href='../../Frontend/index.php'>Return to Site</a>";
?>
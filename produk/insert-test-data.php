<?php
/**
 * INSERT TEST DATA - Quick data seeder
 * Dijalankan dari test-api.php
 */

header('Content-Type: text/html; charset=utf-8');

require_once '../MobileNest/config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Insert Test Data</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; padding: 15px; border: 1px solid; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: 15px; border: 1px solid; border-radius: 5px; margin: 10px 0; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class='container'>
    <h1>📑 Insert Test Data</h1>
";

// Test data
$test_products = [
    [
        'nama_produk' => 'Samsung Galaxy S24 Ultra',
        'merek' => 'Samsung',
        'deskripsi' => 'Flagship smartphone with advanced AI features',
        'harga' => 15999000,
        'stok' => 25,
        'kategori' => 'Flagship',
        'status_produk' => 'Tersedia',
        'terjual' => 120,
        'rating' => 4.8
    ],
    [
        'nama_produk' => 'iPhone 15 Pro Max',
        'merek' => 'Apple',
        'deskripsi' => 'Premium Apple smartphone with A17 Pro chip',
        'harga' => 16999000,
        'stok' => 30,
        'kategori' => 'Flagship',
        'status_produk' => 'Tersedia',
        'terjual' => 150,
        'rating' => 4.9
    ],
    [
        'nama_produk' => 'Xiaomi 14 Ultra',
        'merek' => 'Xiaomi',
        'deskripsi' => 'Powerful mid-range phone with great camera',
        'harga' => 6999000,
        'stok' => 40,
        'kategori' => 'Mid-Range',
        'status_produk' => 'Tersedia',
        'terjual' => 200,
        'rating' => 4.5
    ],
    [
        'nama_produk' => 'Samsung Galaxy A55',
        'merek' => 'Samsung',
        'deskripsi' => 'Affordable Samsung with good performance',
        'harga' => 4499000,
        'stok' => 50,
        'kategori' => 'Budget',
        'status_produk' => 'Tersedia',
        'terjual' => 180,
        'rating' => 4.3
    ],
    [
        'nama_produk' => 'OPPO Reno 11',
        'merek' => 'OPPO',
        'deskripsi' => 'Stylish phone with AMOLED display',
        'harga' => 5499000,
        'stok' => 35,
        'kategori' => 'Mid-Range',
        'status_produk' => 'Tersedia',
        'terjual' => 140,
        'rating' => 4.4
    ],
    [
        'nama_produk' => 'Vivo V29',
        'merek' => 'Vivo',
        'deskripsi' => 'Camera-focused phone with night mode',
        'harga' => 5999000,
        'stok' => 28,
        'kategori' => 'Mid-Range',
        'status_produk' => 'Tersedia',
        'terjual' => 160,
        'rating' => 4.6
    ],
    [
        'nama_produk' => 'Realme 12 Pro',
        'merek' => 'Realme',
        'deskripsi' => 'Budget flagship with fast charging',
        'harga' => 4999000,
        'stok' => 45,
        'kategori' => 'Budget',
        'status_produk' => 'Tersedia',
        'terjual' => 190,
        'rating' => 4.2
    ],
    [
        'nama_produk' => 'iPhone 15',
        'merek' => 'Apple',
        'deskripsi' => 'Standard iPhone with A17 chip',
        'harga' => 12999000,
        'stok' => 35,
        'kategori' => 'Flagship',
        'status_produk' => 'Tersedia',
        'terjual' => 200,
        'rating' => 4.7
    ],
    [
        'nama_produk' => 'Xiaomi 13T',
        'merek' => 'Xiaomi',
        'deskripsi' => 'Value flagship with 144W charging',
        'harga' => 8999000,
        'stok' => 32,
        'kategori' => 'Mid-Range',
        'status_produk' => 'Tersedia',
        'terjual' => 170,
        'rating' => 4.4
    ],
    [
        'nama_produk' => 'Samsung Galaxy S23',
        'merek' => 'Samsung',
        'deskripsi' => 'Previous gen flagship at discount',
        'harga' => 9999000,
        'stok' => 20,
        'kategori' => 'Mid-Range',
        'status_produk' => 'Tersedia',
        'terjual' => 250,
        'rating' => 4.5
    ]
];

// Insert products
$inserted = 0;
$failed = 0;

echo "<p>Inserting " . count($test_products) . " test products...</p>";

foreach ($test_products as $product) {
    $sql = "INSERT INTO produk (nama_produk, merek, deskripsi, harga, stok, kategori, status_produk, terjual, rating, tanggal_ditambahkan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $failed++;
        echo "<div class='error'>✗ Error preparing query: " . $conn->error . "</div>";
        continue;
    }
    
    $stmt->bind_param(
        'sssiiisid',
        $product['nama_produk'],
        $product['merek'],
        $product['deskripsi'],
        $product['harga'],
        $product['stok'],
        $product['kategori'],
        $product['status_produk'],
        $product['terjual'],
        $product['rating']
    );
    
    if ($stmt->execute()) {
        $inserted++;
        echo "<div class='success'>✓ " . htmlspecialchars($product['nama_produk']) . "</div>";
    } else {
        $failed++;
        echo "<div class='error'>✗ " . htmlspecialchars($product['nama_produk']) . " - " . $stmt->error . "</div>";
    }
    
    $stmt->close();
}

echo "<hr>";
echo "<h2>🌟 Results</h2>";
echo "<div class='success'>✓ Successfully inserted: <strong>" . $inserted . "</strong> products</div>";

if ($failed > 0) {
    echo "<div class='error'>✗ Failed: <strong>" . $failed . "</strong> products</div>";
}

// Verify
$count = $conn->query("SELECT COUNT(*) as total FROM produk")->fetch_assoc()['total'];
echo "<p>Total products in database: <strong>" . $count . "</strong></p>";

echo "<hr>";
echo "<h2>🔍 Next Steps</h2>";
echo "<ol>
    <li>Go to <a href='../MobileNest/produk/list-produk.php' target='_blank'>Filter Page</a>
    <li>Test the filter - products should now appear
    <li>Try different filters (brand, price, search)
    <li>Check sorting options
</ol>";

echo "<hr>";
echo "<p><a href='test-api.php'>← Back to API Test</a></p>";

echo "</div>
</body>
</html>";

$conn->close();
?>

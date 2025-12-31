<?php
/**
 * TEST API ENDPOINT - Diagnostic Tool
 * Buka di browser: http://localhost/MobileNestV1/produk/test-api.php
 * Untuk melihat apakah API berfungsi dan data ada
 */

// Set header
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>API Test - MobileNest</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        code { background: #f4f4f4; padding: 10px; display: block; border-radius: 4px; margin: 10px 0; font-family: monospace; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .button { display: inline-block; padding: 10px 20px; margin: 5px 0; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .button:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class='container'>
    <h1>🔍 MobileNest API Test</h1>
    <p>Periksa apakah database dan API berfungsi dengan baik</p>
";

// Test 1: Check config
echo "<div class='test-section info'>
    <h2>Test 1: Database Connection</h2>";

require_once '../MobileNest/config.php';

if ($conn->connect_errno) {
    echo "<div class='error'>✗ Database connection failed: " . $conn->connect_error . "</div>";
} else {
    echo "<div class='success'>✓ Database connected successfully!</div>";
    echo "<p>Database: <strong>" . htmlspecialchars($db_name) . "</strong></p>";
}
echo "</div>";

// Test 2: Check if table exists
echo "<div class='test-section info'>
    <h2>Test 2: Produk Table Status</h2>";

$table_check = $conn->query("SHOW TABLES LIKE 'produk'");

if ($table_check->num_rows == 0) {
    echo "<div class='error'>✗ Table 'produk' does not exist!</div>";
    echo "<p>You need to create the table first. Check database structure.</p>";
} else {
    echo "<div class='success'>✓ Table 'produk' exists!</div>";
}
echo "</div>";

// Test 3: Check product count
echo "<div class='test-section info'>
    <h2>Test 3: Product Count</h2>";

$count_result = $conn->query("SELECT COUNT(*) as total FROM produk");

if ($count_result) {
    $count_row = $count_result->fetch_assoc();
    $product_count = $count_row['total'];
    
    if ($product_count == 0) {
        echo "<div class='error'>⚠️ No products found in database!</div>";
        echo "<p>Total products: <strong>0</strong></p>";
        echo "<p><button class='button' onclick='addTestData()'>Add Test Data</button></p>";
    } else {
        echo "<div class='success'>✓ Products found!</div>";
        echo "<p>Total products: <strong>" . $product_count . "</strong></p>";
    }
} else {
    echo "<div class='error'>✗ Query failed: " . $conn->error . "</div>";
}
echo "</div>";

// Test 4: Test API endpoint
echo "<div class='test-section info'>
    <h2>Test 4: API Endpoint Test</h2>
    <p>Testing: <code>produk/get-produk.php</code></p>";

// Make request to API
$api_url = 'http://localhost/MobileNestV1/produk/get-produk.php';
$response = @file_get_contents($api_url);

if ($response === false) {
    echo "<div class='error'>✗ Cannot access API endpoint</div>";
} else {
    $data = json_decode($response, true);
    
    if (isset($data['error'])) {
        echo "<div class='error'>✗ API Error: " . htmlspecialchars($data['error']) . "</div>";
    } else if (is_array($data)) {
        if (count($data) == 0) {
            echo "<div class='error'>⚠️ API returns empty array</div>";
        } else {
            echo "<div class='success'>✓ API working! Returns " . count($data) . " products</div>";
            echo "<p>Sample product:</p>";
            echo "<pre>" . json_encode($data[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
        }
    } else {
        echo "<div class='error'>✗ Invalid API response</div>";
    }
}
echo "</div>";

// Test 5: Check brands
echo "<div class='test-section info'>
    <h2>Test 5: Available Brands</h2>";

$brand_result = $conn->query("SELECT DISTINCT merek FROM produk WHERE merek IS NOT NULL AND merek != '' ORDER BY merek");

if ($brand_result && $brand_result->num_rows > 0) {
    echo "<div class='success'>✓ Brands found:</div>";
    echo "<ul>";
    while ($row = $brand_result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['merek']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<div class='error'>⚠️ No brands found</div>";
}
echo "</div>";

// Test 6: Raw query test
echo "<div class='test-section info'>
    <h2>Test 6: Direct Query Test</h2>
    <p>Running: <code>SELECT * FROM produk LIMIT 1</code></p>";

$raw_query = $conn->query("SELECT * FROM produk LIMIT 1");

if ($raw_query) {
    if ($raw_query->num_rows > 0) {
        $row = $raw_query->fetch_assoc();
        echo "<div class='success'>✓ Query successful!</div>";
        echo "<pre>" . json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
    } else {
        echo "<div class='error'>✗ Query returned no rows</div>";
    }
} else {
    echo "<div class='error'>✗ Query failed: " . $conn->error . "</div>";
}
echo "</div>";

echo "<div class='test-section'>
    <h2>📋 Summary</h2>
    <h3>What to check:</h3>
    <ul>
        <li>✓ Database connection: OK
        <li>✓ Table exists: Check above
        <li>✓ Has data: Check product count above
        <li>✓ API works: Check Test 4 above
    </ul>
    
    <h3>If products are empty:</h3>
    <ul>
        <li>1️⃣ Click 'Add Test Data' button above
        <li>2️⃣ Refresh browser
        <li>3️⃣ Go to filter page: <a href='../MobileNest/produk/list-produk.php' target='_blank'>list-produk.php</a>
        <li>4️⃣ Products should now show
    </ul>
    
    <h3>Still not working?</h3>
    <ul>
        <li>1️⃣ Check phpMyAdmin for produk table
        <li>2️⃣ Verify table structure has required columns
        <li>3️⃣ Check browser console (F12) for JS errors
        <li>4️⃣ Check Network tab for API response
    </ul>
</div>

<script>
function addTestData() {
    if (confirm('Add 10 test products to database?')) {
        window.location.href = 'insert-test-data.php';
    }
}
</script>

</div>
</body>
</html>
";

$conn->close();
?>

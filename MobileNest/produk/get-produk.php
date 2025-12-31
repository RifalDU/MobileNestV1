<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
require_once '../config.php';

// Get filter parameters
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 999999999;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';

// Build WHERE clause
$where_conditions = ["status_produk = 'Tersedia'"];

if (!empty($brand)) {
    $brands_array = explode(',', $brand);
    $brands_safe = array_map(function($b) { 
        global $conn;
        return "'" . $conn->real_escape_string(trim($b)) . "'";
    }, $brands_array);
    $where_conditions[] = "merek IN (" . implode(',', $brands_safe) . ")";
}

if ($min_price > 0 || $max_price < 999999999) {
    $where_conditions[] = "harga >= $min_price AND harga <= $max_price";
}

if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $where_conditions[] = "(nama_produk LIKE '%$search_safe%' OR merek LIKE '%$search_safe%' OR deskripsi LIKE '%$search_safe%')";
}

$where_clause = implode(' AND ', $where_conditions);

// Build ORDER BY clause
$order_by = "tanggal_ditambahkan DESC"; // Default

switch($sort) {
    case 'harga_rendah':
        $order_by = "harga ASC";
        break;
    case 'harga_tinggi':
        $order_by = "harga DESC";
        break;
    case 'populer':
        $order_by = "terjual DESC, id_produk DESC";
        break;
    case 'terbaru':
    default:
        $order_by = "tanggal_ditambahkan DESC";
}

// Get products
$sql = "SELECT id_produk, nama_produk, merek, deskripsi, harga, stok, kategori, status_produk, tanggal_ditambahkan, gambar, terjual, rating 
        FROM produk 
        WHERE $where_clause 
        ORDER BY $order_by";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Convert to proper types
    $row['id_produk'] = (int)$row['id_produk'];
    $row['harga'] = (int)$row['harga'];
    $row['stok'] = (int)$row['stok'];
    $row['terjual'] = (int)$row['terjual'];
    $row['rating'] = (float)$row['rating'];
    $products[] = $row;
}

echo json_encode($products);
?>

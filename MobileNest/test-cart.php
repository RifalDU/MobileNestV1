<?php
session_start();

echo "<h1>SESSION DEBUG</h1>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . session_status() . "<br>";
echo "<hr>";

echo "<h2>Current Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Test Add Item</h2>";
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$_SESSION['cart']['1'] = 5;
$_SESSION['cart']['2'] = 3;

echo "After add:<br>";
echo "<pre>";
print_r($_SESSION['cart']);
echo "</pre>";

echo "<h2>Session File Info</h2>";
echo "Session save path: " . ini_get('session.save_path') . "<br>";
echo "Session name: " . session_name() . "<br>";

// Try to find session file
$session_file = session_save_path() . '/sess_' . session_id();
if (file_exists($session_file)) {
    echo "Session file found: " . $session_file . "<br>";
    echo "File size: " . filesize($session_file) . " bytes<br>";
    echo "File contents:<br>";
    echo "<pre>";
    echo htmlspecialchars(file_get_contents($session_file));
    echo "</pre>";
} else {
    echo "Session file NOT found at: " . $session_file . "<br>";
}
?>

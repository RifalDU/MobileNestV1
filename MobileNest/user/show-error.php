<?php
// FORCE DISPLAY ALL ERRORS
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Testing PHP Configuration</h1>";
echo "<hr>";

echo "<h2>1. PHP Version</h2>";
echo "PHP Version: " . phpversion();
echo "<br>";

echo "<hr>";
echo "<h2>2. Loading config.php</h2>";

try {
    require_once '../config.php';
    echo "<p style='color:green'><strong>SUCCESS:</strong> config.php loaded</p>";
} catch (Exception $e) {
    echo "<p style='color:red'><strong>ERROR:</strong> " . $e->getMessage() . "</p>";
    die();
}

echo "<hr>";
echo "<h2>3. Test Simple HTML Page</h2>";
echo "<p>If you see this, basic PHP is working.</p>";

echo "<hr>";
echo "<h2>4. Now test actual login.php</h2>";
echo "<p><a href='login.php'>Click here to test login.php</a></p>";
echo "<p><a href='login-new.php'>Click here to test login-new.php</a></p>";
echo "<p><a href='login-debug.php'>Click here to test login-debug.php</a></p>";

echo "<hr>";
echo "<h2>5. Check Error Log Location</h2>";
echo "<p>Error log: " . ini_get('error_log') . "</p>";
echo "<p>Check this file for actual error messages!</p>";

?>

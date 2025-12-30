<?php
// TURN ON ERROR DISPLAY
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>DEBUG MODE - login.php</h2>";
echo "<hr>";

// TEST 1: Check if config.php can be loaded
echo "<h3>Test 1: Loading config.php</h3>";
try {
    require_once '../config.php';
    echo "<p style='color:green'>✅ Config.php loaded successfully</p>";
    echo "<p>SITE_URL: " . (defined('SITE_URL') ? SITE_URL : 'NOT DEFINED') . "</p>";
    echo "<p>Database connection: " . (isset($conn) ? 'EXISTS' : 'NOT EXISTS') . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error loading config.php: " . $e->getMessage() . "</p>";
    die();
}

echo "<hr>";

// TEST 2: Check if includes folder exists
echo "<h3>Test 2: Check includes folder</h3>";
$header_path = '../includes/header.php';
$footer_path = '../includes/footer.php';

if (file_exists($header_path)) {
    echo "<p style='color:green'>✅ header.php exists</p>";
} else {
    echo "<p style='color:red'>❌ header.php NOT FOUND at: $header_path</p>";
}

if (file_exists($footer_path)) {
    echo "<p style='color:green'>✅ footer.php exists</p>";
} else {
    echo "<p style='color:red'>❌ footer.php NOT FOUND at: $footer_path</p>";
}

echo "<hr>";

// TEST 3: Check logo
echo "<h3>Test 3: Check logo file</h3>";
$logo_path = "../assets/images/logo.jpg";

if (file_exists($logo_path)) {
    echo "<p style='color:green'>✅ Logo exists at: $logo_path</p>";
    echo "<img src='$logo_path' height='50' alt='Logo'> <-- Logo preview";
} else {
    echo "<p style='color:red'>❌ Logo NOT FOUND at: $logo_path</p>";
    echo "<p>Try these paths:</p>";
    echo "<ul>";
    echo "<li>../assets/images/logo.jpg</li>";
    echo "<li>../assets/images/logo.png</li>";
    echo "<li>../img/logo.jpg</li>";
    echo "</ul>";
}

echo "<hr>";

// TEST 4: Test header include
echo "<h3>Test 4: Include header.php</h3>";

$page_title = "Login DEBUG";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/script.js";
$logo_path = "../assets/images/logo.jpg";
$home_url = "../index.php";
$produk_url = "../produk/list-produk.php";
$login_url = "login.php";
$register_url = "register.php";
$keranjang_url = "../transaksi/keranjang.php";

try {
    ob_start();
    include '../includes/header.php';
    $header_output = ob_get_clean();
    echo "<p style='color:green'>✅ header.php included successfully</p>";
    echo "<details><summary>View header output</summary><pre>" . htmlspecialchars($header_output) . "</pre></details>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error including header.php: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// TEST 5: Test database query
echo "<h3>Test 5: Test database query</h3>";

if (isset($conn)) {
    $sql = "SELECT COUNT(*) as user_count FROM users";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p style='color:green'>✅ Database query successful</p>";
        echo "<p>Total users in database: " . $row['user_count'] . "</p>";
    } else {
        echo "<p style='color:red'>❌ Database query failed: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color:red'>❌ No database connection</p>";
}

echo "<hr>";

// TEST 6: Session check
echo "<h3>Test 6: Session status</h3>";
echo "<p>Session status: " . session_status() . "</p>";
echo "<ul>";
echo "<li>PHP_SESSION_DISABLED (0): " . (session_status() === PHP_SESSION_DISABLED ? 'YES' : 'NO') . "</li>";
echo "<li>PHP_SESSION_NONE (1): " . (session_status() === PHP_SESSION_NONE ? 'YES' : 'NO') . "</li>";
echo "<li>PHP_SESSION_ACTIVE (2): " . (session_status() === PHP_SESSION_ACTIVE ? 'YES' : 'NO') . "</li>";
echo "</ul>";

if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color:green'>✅ Session is active</p>";
    echo "<p>Session data:</p>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
} else {
    echo "<p style='color:orange'>⚠️ Session not started yet</p>";
}

echo "<hr>";

// TEST 7: Simulate login process
echo "<h3>Test 7: Test login form processing</h3>";
echo "<p>Try to login using the form below:</p>";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    echo "<p>POST data received:</p>";
    echo "<ul>";
    echo "<li>Username: " . htmlspecialchars($username) . "</li>";
    echo "<li>Password: " . (empty($password) ? 'EMPTY' : '***hidden***') . "</li>";
    echo "</ul>";
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong!';
    } else {
        // Query user
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            $error = 'Database error: ' . $conn->error;
        } else {
            $stmt->bind_param('ss', $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();
                
                echo "<p style='color:green'>✅ User found in database:</p>";
                echo "<ul>";
                echo "<li>ID: " . $user_data['id_user'] . "</li>";
                echo "<li>Username: " . $user_data['username'] . "</li>";
                echo "<li>Email: " . $user_data['email'] . "</li>";
                echo "<li>Nama: " . $user_data['nama_lengkap'] . "</li>";
                echo "</ul>";
                
                if (password_verify($password, $user_data['password'])) {
                    echo "<p style='color:green'>✅ Password CORRECT!</p>";
                    
                    // Set session
                    $_SESSION['user'] = $user_data['id_user'];
                    $_SESSION['user_name'] = $user_data['nama_lengkap'];
                    $_SESSION['username'] = $user_data['username'];
                    
                    $success = 'Login berhasil! Session set.';
                    
                    echo "<p>Session after login:</p>";
                    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
                    
                    echo "<p><strong>Would redirect to: ../index.php</strong></p>";
                    echo "<p><a href='../index.php' class='btn btn-primary'>Go to Homepage</a></p>";
                } else {
                    $error = 'Password salah!';
                }
            } else {
                $error = 'Username atau email tidak ditemukan!';
            }
            
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Debug</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        hr { margin: 30px 0; border-top: 2px solid #333; }
        h2 { color: #d9534f; }
        h3 { color: #5bc0de; }
        .test-form { max-width: 500px; margin: 20px 0; padding: 20px; border: 2px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="test-form">
    <h4>Test Login Form</h4>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Username or Email</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" name="test_login" class="btn btn-primary">Test Login</button>
    </form>
</div>

<hr>

<h3>Conclusion</h3>
<p>If all tests pass (green checkmarks), then login.php should work.</p>
<p>If any test fails (red X), fix that issue first.</p>

<hr>

<p><strong>Links for testing:</strong></p>
<ul>
    <li><a href="login.php">Go to actual login.php</a></li>
    <li><a href="../index.php">Go to homepage</a></li>
    <li><a href="register.php">Go to register page</a></li>
</ul>

</body>
</html>

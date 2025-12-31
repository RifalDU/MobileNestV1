<?php
/**
 * Login Process Handler
 * Authenticates user against admin and users tables
 * 
 * Flow:
 * 1. Check admin table first
 * 2. If not found, check users table
 * 3. Verify password (password_verify)
 * 4. Set session variables
 * 5. Redirect to appropriate dashboard
 */

session_start();
require_once '../config.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../login.php');
    exit();
}

// Get input
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validate input
if (empty($username) || empty($password)) {
    $_SESSION['error'] = '❌ Username dan password harus diisi!';
    header('Location: ../../login.php');
    exit();
}

// STRATEGY 1: Check ADMIN table first
$stmt = $conn->prepare("SELECT id_admin, username, password, nama_lengkap, email FROM admin WHERE username = ?");
if (!$stmt) {
    $_SESSION['error'] = '❌ Database error: ' . $conn->error;
    header('Location: ../../login.php');
    exit();
}

$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    
    // For now, compare passwords directly (in production, use password_verify)
    // Current DB passwords appear to be plain text or SHA2 hashed
    // TODO: Update passwords to bcrypt hash
    
    // Try direct comparison first (if passwords are plain text)
    if ($admin['password'] === $password) {
        // Admin login successful - PLAIN TEXT MATCH
        $_SESSION['authenticated'] = true;
        $_SESSION['role'] = 'admin';
        $_SESSION['id'] = $admin['id_admin'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['nama_lengkap'] = $admin['nama_lengkap'];
        $_SESSION['email'] = $admin['email'];
        $_SESSION['login_time'] = time();
        
        $stmt->close();
        header('Location: ../../admin/dashboard.php');
        exit();
    }
    
    // Try password_verify (if passwords are hashed)
    if (password_verify($password, $admin['password'])) {
        // Admin login successful - HASHED MATCH
        $_SESSION['authenticated'] = true;
        $_SESSION['role'] = 'admin';
        $_SESSION['id'] = $admin['id_admin'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['nama_lengkap'] = $admin['nama_lengkap'];
        $_SESSION['email'] = $admin['email'];
        $_SESSION['login_time'] = time();
        
        $stmt->close();
        header('Location: ../../admin/dashboard.php');
        exit();
    }
    
    // Password doesn't match
    $_SESSION['error'] = '❌ Username atau password salah!';
    $stmt->close();
    header('Location: ../../login.php');
    exit();
}

$stmt->close();

// STRATEGY 2: Check USERS table if admin not found
$stmt = $conn->prepare("SELECT id_user, username, password, nama_lengkap, email, status_akun FROM users WHERE username = ?");
if (!$stmt) {
    $_SESSION['error'] = '❌ Database error: ' . $conn->error;
    header('Location: ../../login.php');
    exit();
}

$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Check if account is active
    if ($user['status_akun'] !== 'Aktif') {
        $_SESSION['error'] = '❌ Akun Anda tidak aktif. Hubungi admin untuk mengaktifkan!';
        $stmt->close();
        header('Location: ../../login.php');
        exit();
    }
    
    // Try direct comparison first (if passwords are plain text)
    if ($user['password'] === $password) {
        // User login successful - PLAIN TEXT MATCH
        $_SESSION['authenticated'] = true;
        $_SESSION['role'] = 'user';
        $_SESSION['id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['login_time'] = time();
        
        $stmt->close();
        header('Location: ../../user/dashboard.php');
        exit();
    }
    
    // Try password_verify (if passwords are hashed)
    if (password_verify($password, $user['password'])) {
        // User login successful - HASHED MATCH
        $_SESSION['authenticated'] = true;
        $_SESSION['role'] = 'user';
        $_SESSION['id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['login_time'] = time();
        
        $stmt->close();
        header('Location: ../../user/dashboard.php');
        exit();
    }
    
    // Password doesn't match
    $_SESSION['error'] = '❌ Username atau password salah!';
    $stmt->close();
    header('Location: ../../login.php');
    exit();
}

$stmt->close();

// Username not found in both tables
$_SESSION['error'] = '❌ Username tidak ditemukan!';
header('Location: ../../login.php');
exit();

?>

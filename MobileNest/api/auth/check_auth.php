<?php
/**
 * Authentication Middleware
 * Check if user is logged in and has valid session
 * Include this file at the top of protected pages
 * 
 * Usage:
 * require_once 'api/auth/check_auth.php';
 * 
 * Variables available after include:
 * - $user_role: 'admin' or 'user'
 * - $user_id: ID of logged-in user
 * - $username: Username of logged-in user
 * - $nama_lengkap: Full name of logged-in user
 */

session_start();

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Not logged in - redirect to login
    header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/MobileNest/login.php');
    exit();
}

// Get user information from session
$user_role = $_SESSION['role'] ?? null;           // 'admin' or 'user'
$user_id = $_SESSION['id'] ?? null;               // user ID (either id_admin or id_user)
$username = $_SESSION['username'] ?? 'Unknown';   // username
$nama_lengkap = $_SESSION['nama_lengkap'] ?? 'Unknown'; // full name

// Validate session variables
if ($user_role === null || $user_id === null) {
    // Invalid session - destroy and redirect
    session_destroy();
    header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/MobileNest/login.php');
    exit();
}

// Session is valid
// User can now access this page

?>

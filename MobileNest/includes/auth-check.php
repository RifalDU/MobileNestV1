<?php
/**
 * AUTH CHECK
 * File untuk validasi authentication dan authorization
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * Redirect to login if not authenticated
 */
function require_user_login() {
    if (!isset($_SESSION['user'])) {
        // Save current URL untuk redirect setelah login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        
        // Redirect ke login page
        header('Location: ' . SITE_URL . '/user/login.php');
        exit;
    }
}

/**
 * Check if admin is logged in
 * Redirect to admin login if not authenticated
 */
function require_admin_login() {
    if (!isset($_SESSION['admin'])) {
        // Redirect ke admin login
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Check if user is guest (not logged in)
 * Redirect to home if already authenticated
 */
function require_guest() {
    if (isset($_SESSION['user'])) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
    if (isset($_SESSION['admin'])) {
        header('Location: ' . SITE_URL . '/admin/dashboard.php');
        exit;
    }
}

/**
 * Get current logged in user ID
 * @return int|null User ID or null if not logged in
 */
function get_current_user_id() {
    return isset($_SESSION['user']) ? (int)$_SESSION['user'] : null;
}

/**
 * Get current logged in admin ID
 * @return int|null Admin ID or null if not logged in
 */
function get_current_admin_id() {
    return isset($_SESSION['admin']) ? (int)$_SESSION['admin'] : null;
}

/**
 * Check if current user is logged in
 * @return bool
 */
function is_user_logged_in() {
    return isset($_SESSION['user']);
}

/**
 * Check if current user is admin
 * @return bool
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin']);
}

/**
 * Logout user
 */
function logout_user() {
    // Clear all session variables
    $_SESSION = [];
    
    // Destroy session
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    
    // Redirect to home
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

/**
 * Get user display name
 * @return string
 */
function get_user_display_name() {
    if (isset($_SESSION['user_name'])) {
        return $_SESSION['user_name'];
    }
    if (isset($_SESSION['username'])) {
        return $_SESSION['username'];
    }
    if (isset($_SESSION['admin_name'])) {
        return $_SESSION['admin_name'];
    }
    if (isset($_SESSION['admin_username'])) {
        return $_SESSION['admin_username'];
    }
    return 'User';
}

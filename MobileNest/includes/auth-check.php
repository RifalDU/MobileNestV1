<?php
/**
 * AUTH-CHECK.PHP - Simplified & Stable Version
 * Menangani authentication & authorization untuk User dan Admin
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Require user login
 * Redirect to login if not authenticated
 */
function require_user_login() {
    if (!isset($_SESSION['user'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . SITE_URL . '/user/login.php');
        exit;
    }
}

/**
 * Require admin login
 * Redirect to admin login if not authenticated
 */
function require_admin_login() {
    if (!isset($_SESSION['admin'])) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Require guest (not logged in)
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
 * Check if user is admin via database
 */
function is_user_admin($user_id, $conn) {
    $sql = "SELECT id_admin FROM admin WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    return $result->num_rows > 0;
}

/**
 * Get current user ID
 */
function get_current_user_id() {
    return isset($_SESSION['user']) ? (int)$_SESSION['user'] : null;
}

/**
 * Get current admin ID
 */
function get_current_admin_id() {
    return isset($_SESSION['admin']) ? (int)$_SESSION['admin'] : null;
}

/**
 * Get user ID (user or admin)
 */
function get_user_id() {
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    } elseif (isset($_SESSION['admin'])) {
        return $_SESSION['admin'];
    }
    return null;
}

/**
 * Get user name
 */
function get_user_name() {
    if (isset($_SESSION['user_name'])) {
        return $_SESSION['user_name'];
    } elseif (isset($_SESSION['admin_name'])) {
        return $_SESSION['admin_name'];
    }
    return 'User';
}

/**
 * Check if user is logged in
 */
function is_user_logged_in() {
    return isset($_SESSION['user']);
}

/**
 * Check if admin is logged in
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin']);
}

/**
 * Check if anyone is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user']) || isset($_SESSION['admin']);
}

/**
 * Get user role
 */
function get_user_role() {
    if (isset($_SESSION['admin'])) {
        return 'admin';
    } elseif (isset($_SESSION['user'])) {
        return 'user';
    }
    return 'guest';
}

/**
 * Logout user
 */
function user_logout() {
    unset($_SESSION['user']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
}

/**
 * Logout admin
 */
function admin_logout() {
    unset($_SESSION['admin']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
}

/**
 * Logout all
 */
function logout_all() {
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Hash password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Verify password
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Get base URL
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
    if ($basePath === '\\' || $basePath === '/') {
        $basePath = '';
    }
    return $protocol . '://' . $host . $basePath;
}
?>

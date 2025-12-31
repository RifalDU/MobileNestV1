<?php
/**
 * Helper Functions
 * Contains utility functions used across the application
 * 
 * Functions:
 * - is_logged_in()  - Check if user is authenticated
 * - get_user_name() - Get current logged in user name
 * - get_user_role() - Get current user role (admin/user)
 * - redirect_to()   - Redirect with full URL path
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is currently logged in
 * 
 * @return bool True if user has valid session
 */
function is_logged_in() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

/**
 * Get current logged in user's name/username
 * 
 * @return string User name or 'Guest' if not logged in
 */
function get_user_name() {
    if (is_logged_in()) {
        // Try different session keys for name
        if (isset($_SESSION['nama_lengkap'])) {
            return htmlspecialchars($_SESSION['nama_lengkap']);
        } elseif (isset($_SESSION['username'])) {
            return htmlspecialchars($_SESSION['username']);
        }
    }
    return 'Guest';
}

/**
 * Get current logged in user's role
 * 
 * @return string|null Role (admin/user) or null if not logged in
 */
function get_user_role() {
    if (is_logged_in() && isset($_SESSION['role'])) {
        return $_SESSION['role'];
    }
    return null;
}

/**
 * Get current logged in user's ID
 * 
 * @return int|null User ID or null if not logged in
 */
function get_user_id() {
    if (is_logged_in() && isset($_SESSION['id'])) {
        return $_SESSION['id'];
    }
    return null;
}

/**
 * Check if current user is admin
 * 
 * @return bool True if user is admin
 */
function is_admin() {
    return get_user_role() === 'admin';
}

/**
 * Check if current user is regular user
 * 
 * @return bool True if user is regular user
 */
function is_user() {
    return get_user_role() === 'user';
}

/**
 * Get SITE_URL constant
 * Used in navbar and other templates
 * 
 * @return string Site URL without trailing slash
 */
function get_site_url() {
    return defined('SITE_URL') ? SITE_URL : '/MobileNest';
}

/**
 * Generate logout link
 * 
 * @return string HTML logout link
 */
function get_logout_link() {
    $url = get_site_url() . '/includes/logout.php';
    return sprintf(
        '<a href="%s" class="text-danger">%s Logout</a>',
        htmlspecialchars($url),
        '🚪'
    );
}

?>

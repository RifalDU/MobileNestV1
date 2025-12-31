<?php
/**
 * Logout Handler
 * Destroys session and redirects to login page
 * 
 * Location: includes/ (not api/) - This is a helper, not an API endpoint
 * 
 * Usage: Click logout link that points to this file
 * <a href="includes/logout.php">Logout</a>
 */

session_start();

// Destroy all session variables
session_destroy();

// Clear session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Redirect to login page
header('Location: ../login.php?logged_out=true');
exit();

?>

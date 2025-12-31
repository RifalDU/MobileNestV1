<?php
/**
 * Login Page (User Area)
 * Location: /MobileNest/user/login.php
 * Form Action: /MobileNest/includes/process_login.php
 * 
 * Redirect Logic:
 * - Admin → /MobileNest/admin/dashboard.php
 * - User → /MobileNest/index.php
 * 
 * Test Credentials:
 * Admin: username=admin, password=password123
 * User: username=user1, password=pass1
 */

session_start();
require_once '../config.php';

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ' . SITE_URL . '/admin/dashboard.php');
    } else {
        header('Location: ' . SITE_URL . '/index.php');
    }
    exit();
}

// Get error/success messages
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error']);
unset($_SESSION['success']);

$logged_out = isset($_GET['logged_out']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MobileNest</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 450px;
            width: 100%;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-section img {
            height: 60px;
            margin-bottom: 15px;
        }
        
        .logo-section h2 {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .logo-section p {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-label i {
            color: #667eea;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            color: white;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #dee2e6;
        }
        
        .divider::before {
            left: 0;
        }
        
        .divider::after {
            right: 0;
        }
        
        .divider span {
            background: white;
            padding: 0 10px;
            color: #6c757d;
            font-size: 14px;
        }
        
        .register-link {
            text-align: center;
        }
        
        .register-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }
        
        .forgot-password a {
            color: #6c757d;
            font-size: 14px;
            text-decoration: none;
        }
        
        .forgot-password a:hover {
            color: #667eea;
        }
        
        .alert {
            border-radius: 8px;
        }
        
        .demo-info {
            background: #f0f7ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 13px;
        }
        
        .demo-info strong {
            color: #667eea;
        }
        
        .demo-item {
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px solid rgba(102,126,234,0.2);
        }
        
        .demo-item:last-child {
            border-bottom: none;
        }
        
        code {
            background: white;
            padding: 2px 6px;
            border-radius: 3px;
            color: #c33;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <!-- Logo & Title -->
        <div class="logo-section">
            <img src="<?php echo SITE_URL; ?>/assets/images/logo.jpg" alt="MobileNest Logo">
            <h2>📱 MobileNest</h2>
            <p>Silakan login ke akun Anda</p>
        </div>

        <!-- Logout Success Alert -->
        <?php if ($logged_out): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                ✓ Anda berhasil logout
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Error Alert -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Success Alert -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="<?php echo SITE_URL; ?>/includes/process_login.php" id="loginForm">
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-person-fill"></i> Username
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="username" 
                    name="username" 
                    placeholder="Masukkan username" 
                    required
                    autofocus
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="bi bi-lock-fill"></i> Password
                </label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan password" 
                    required
                >
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3">
                <i class="bi bi-box-arrow-in-right"></i> Masuk
            </button>
        </form>

        <!-- Divider -->
        <div class="divider">
            <span>atau</span>
        </div>

        <!-- Register Link -->
        <div class="register-link">
            <p class="mb-0">Belum punya akun? 
                <a href="register.php">퉰dPendaftaran</a>
            </p>
        </div>

        <!-- Forgot Password -->
        <div class="forgot-password">
            <a href="#">
                <i class="bi bi-question-circle"></i> Lupa password?
            </a>
        </div>
        
        <!-- Back to Home -->
        <div class="text-center mt-3">
            <a href="<?php echo SITE_URL; ?>/index.php" class="text-muted" style="font-size: 14px;">
                <i class="bi bi-house-door"></i> Kembali ke Beranda
            </a>
        </div>
        
        <!-- Demo Credentials -->
        <div class="demo-info">
            <strong>🧪 Test Credentials:</strong>
            <div class="demo-item">
                <span class="badge bg-primary">ADMIN</span>
                <code>admin</code> / <code>password123</code>
            </div>
            <div class="demo-item">
                <span class="badge bg-secondary">USER</span>
                <code>user1</code> / <code>pass1</code>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        
        if (!username || !password) {
            e.preventDefault();
            alert('Username dan password tidak boleh kosong!');
        }
    });
</script>

</body>
</html>

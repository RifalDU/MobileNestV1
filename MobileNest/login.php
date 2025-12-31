<?php
/**
 * MobileNest Login Page
 * Unified login for both Admin and Users
 * 
 * Test Credentials:
 * Admin: username=admin, password=password123
 * User: username=user1, password=pass1
 */

session_start();

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .login-header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }
        
        .alert.error {
            background-color: #fee;
            color: #c33;
            border: 1px solid #fcc;
            display: block;
        }
        
        .alert.success {
            background-color: #efe;
            color: #3c3;
            border: 1px solid #cfc;
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: inherit;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 13px;
        }
        
        .demo-credentials {
            background: #f5f5f5;
            border-left: 3px solid #667eea;
            padding: 15px;
            border-radius: 6px;
            margin-top: 25px;
            font-size: 13px;
            color: #555;
        }
        
        .demo-credentials h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 13px;
        }
        
        .demo-item {
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .demo-item:last-child {
            border-bottom: none;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            background: #667eea;
            color: white;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 5px;
        }
        
        .badge.user {
            background: #764ba2;
        }
        
        code {
            background: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #c33;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="logo">📱</div>
            <h1>MobileNest</h1>
            <p>E-Commerce Platform Login</p>
        </div>
        
        <!-- Messages -->
        <?php if ($logged_out): ?>
            <div class="alert success">✓ Anda berhasil logout</div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <form method="POST" action="api/auth/process_login.php" id="loginForm">
            <div class="form-group">
                <label for="username">👤 Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Masukkan username Anda" 
                    required
                    autofocus
                >
            </div>
            
            <div class="form-group">
                <label for="password">🔐 Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan password Anda" 
                    required
                >
            </div>
            
            <button type="submit" class="btn-login">🔓 Login</button>
        </form>
        
        <!-- Footer -->
        <div class="login-footer">
            <p>Sistem akan otomatis mengarahkan ke dashboard yang tepat</p>
        </div>
        
        <!-- Demo Credentials -->
        <div class="demo-credentials">
            <h4>🧪 Test Credentials:</h4>
            
            <div class="demo-item">
                <span class="badge">ADMIN</span>
                <strong>Admin Account</strong><br>
                Username: <code>admin</code><br>
                Password: <code>password123</code>
            </div>
            
            <div class="demo-item">
                <span class="badge user">USER</span>
                <strong>Customer Account</strong><br>
                Username: <code>user1</code><br>
                Password: <code>pass1</code>
            </div>
        </div>
    </div>
    
    <script>
        // Simple form validation
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

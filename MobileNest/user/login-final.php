<?php
require_once '../config.php';

// Redirect jika sudah login
if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
    if (isset($_SESSION['admin'])) {
        header('Location: ' . SITE_URL . '/admin/dashboard.php');
    } else {
        header('Location: ' . SITE_URL . '/index.php');
    }
    exit;
}

$error = '';
$success = '';

// Check for success message from registration
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// PROSES LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong!';
    } else {
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param('ss', $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();
                
                if (password_verify($password, $user_data['password'])) {
                    // Check admin
                    $is_admin = false;
                    $table_check = $conn->query("SHOW TABLES LIKE 'admin'");
                    if ($table_check && $table_check->num_rows > 0) {
                        $admin_sql = "SELECT id_admin FROM admin WHERE id_user = ?";
                        $admin_stmt = $conn->prepare($admin_sql);
                        if ($admin_stmt) {
                            $admin_stmt->bind_param('i', $user_data['id_user']);
                            $admin_stmt->execute();
                            $admin_result = $admin_stmt->get_result();
                            if ($admin_result->num_rows > 0) {
                                $is_admin = true;
                            }
                            $admin_stmt->close();
                        }
                    }
                    
                    if ($is_admin) {
                        $_SESSION['admin'] = $user_data['id_user'];
                        $_SESSION['admin_name'] = $user_data['nama_lengkap'] ?? $user_data['username'];
                        $_SESSION['admin_email'] = $user_data['email'];
                        header('Location: ' . SITE_URL . '/admin/dashboard.php');
                        exit;
                    } else {
                        $_SESSION['user'] = $user_data['id_user'];
                        $_SESSION['user_name'] = $user_data['nama_lengkap'] ?? $user_data['username'];
                        $_SESSION['user_email'] = $user_data['email'];
                        $_SESSION['username'] = $user_data['username'];
                        $_SESSION['role'] = 'user';
                        
                        header('Location: ' . SITE_URL . '/index.php');
                        exit;
                    }
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
    <title>Login - MobileNest</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated Background Circles */
        body::before,
        body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 15s infinite ease-in-out;
        }
        
        body::before {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }
        
        body::after {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            animation-delay: 2s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-30px) scale(1.1);
            }
        }
        
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            padding: 45px 40px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
        }
        
        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 35px;
            animation: fadeIn 0.8s ease-out 0.2s both;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .logo-section img {
            height: 60px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }
        
        .logo-section img:hover {
            transform: scale(1.05) rotate(5deg);
        }
        
        .logo-section h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 8px;
        }
        
        .logo-section p {
            color: #6c757d;
            font-size: 15px;
            font-weight: 400;
        }
        
        /* Form Styles */
        .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-label i {
            color: #667eea;
            font-size: 16px;
        }
        
        .form-control {
            padding: 13px 18px;
            border-radius: 12px;
            border: 2px solid #e9ecef;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
            background: white;
            transform: translateY(-2px);
        }
        
        .form-control:hover:not(:focus) {
            border-color: #d1d5db;
        }
        
        /* Checkbox */
        .form-check-input {
            border-radius: 6px;
            border: 2px solid #dee2e6;
            width: 20px;
            height: 20px;
            transition: all 0.3s ease;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .form-check-label {
            font-size: 14px;
            color: #6c757d;
            margin-left: 8px;
        }
        
        /* Button */
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            border-radius: 12px;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.5s ease;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 14px 18px;
            font-size: 14px;
            animation: slideDown 0.4s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #51cf66 0%, #40c057 100%);
            color: white;
        }
        
        /* Divider */
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
            width: 42%;
            height: 1px;
            background: linear-gradient(to right, transparent, #dee2e6, transparent);
        }
        
        .divider::before {
            left: 0;
        }
        
        .divider::after {
            right: 0;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            color: #9ca3af;
            font-size: 13px;
            font-weight: 500;
        }
        
        /* Links */
        .register-link,
        .forgot-password,
        .home-link {
            text-align: center;
            margin-top: 15px;
        }
        
        .register-link a,
        .forgot-password a,
        .home-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .register-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }
        
        .register-link a:hover::after {
            width: 100%;
        }
        
        .register-link a:hover,
        .forgot-password a:hover,
        .home-link a:hover {
            color: #764ba2;
        }
        
        .forgot-password a,
        .home-link a {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }
        
        .forgot-password a:hover,
        .home-link a:hover {
            color: #667eea;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-card {
                padding: 35px 25px;
            }
            
            .logo-section h3 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <!-- Logo & Title -->
        <div class="logo-section">
            <img src="../assets/images/logo.jpg" alt="MobileNest Logo" onerror="this.style.display='none'">
            <h3>MobileNest</h3>
            <p>Silakan login ke akun Anda</p>
        </div>

        <!-- Error Alert -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Success Alert -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-person-fill"></i> Username atau Email
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="username" 
                    name="username" 
                    placeholder="Masukkan username atau email" 
                    required
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
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

            <button type="submit" name="login" class="btn btn-login w-100 mb-3">
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
                <a href="register.php">Daftar di sini</a>
            </p>
        </div>

        <!-- Forgot Password -->
        <div class="forgot-password">
            <a href="#">
                <i class="bi bi-question-circle"></i> Lupa password?
            </a>
        </div>
        
        <!-- Back to Home -->
        <div class="home-link">
            <a href="../index.php">
                <i class="bi bi-house-door"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

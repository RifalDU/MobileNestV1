<?php
// CRITICAL: Config must be loaded first (handles session_start)
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

/**
 * PROSES LOGIN
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // VALIDASI INPUT
    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong!';
    } else {
        // QUERY TABEL USERS (Support username OR email)
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
                
                // VERIFIKASI PASSWORD
                if (password_verify($password, $user_data['password'])) {
                    
                    // CEK TABEL ADMIN (Optional)
                    $is_admin = false;
                    
                    $table_check = $conn->query("SHOW TABLES LIKE 'admin'");
                    if ($table_check && $table_check->num_rows > 0) {
                        $admin_check_sql = "SELECT id_admin FROM admin WHERE id_user = ?";
                        $admin_stmt = $conn->prepare($admin_check_sql);
                        
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
                        // LOGIN AS ADMIN
                        $_SESSION['admin'] = $user_data['id_user'];
                        $_SESSION['admin_name'] = $user_data['nama_lengkap'] ?? $user_data['username'];
                        $_SESSION['admin_email'] = $user_data['email'];
                        $_SESSION['admin_username'] = $user_data['username'];
                        
                        header('Location: ' . SITE_URL . '/admin/dashboard.php');
                        exit;
                    } else {
                        // LOGIN AS USER
                        $_SESSION['user'] = $user_data['id_user'];
                        $_SESSION['user_name'] = $user_data['nama_lengkap'] ?? $user_data['username'];
                        $_SESSION['user_email'] = $user_data['email'];
                        $_SESSION['username'] = $user_data['username'];
                        $_SESSION['role'] = 'user';
                        
                        // REDIRECT KE INDEX (Homepage)
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
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
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
        
        .badge-new {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 11px;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <!-- Logo & Title -->
        <div class="logo-section">
            <img src="../assets/images/logo.jpg" alt="MobileNest Logo" onerror="this.style.display='none'">
            <h2>MobileNest <span class="badge-new">NEW</span></h2>
            <p>Silakan login ke akun Anda</p>
        </div>

        <!-- Error Alert -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Success Alert -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                    autocomplete="username"
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
                    autocomplete="current-password"
                >
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">
                    Ingat saya
                </label>
            </div>

            <button type="submit" name="login" class="btn btn-primary btn-login w-100 mb-3">
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
        <div class="text-center mt-3">
            <a href="../index.php" class="text-muted">
                <i class="bi bi-house-door"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

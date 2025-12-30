<?php
// ❌ REMOVED: session_start() - Sudah di-handle oleh config.php
// Config.php sudah melakukan session_start(), jadi tidak perlu lagi di sini

require_once '../config.php';  // ✅ FIXED: Correct path to config.php

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

/**
 * 🔐 PROSES LOGIN
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // ✅ VALIDASI INPUT
    if (empty($username) || empty($password)) {
        $error = '❌ Username dan password tidak boleh kosong!';
    } else {
        // 1️⃣ QUERY TABEL USERS
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
                
                // 2️⃣ VERIFIKASI PASSWORD
                if (password_verify($password, $user_data['password'])) {
                    
                    // 3️⃣ CEK TABEL ADMIN (Optional - skip jika tidak ada tabel admin)
                    $is_admin = false;
                    
                    // Check if admin table exists
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
                        // ✅ LOGIN AS ADMIN
                        $_SESSION['admin'] = $user_data['id_user'];
                        $_SESSION['admin_name'] = $user_data['nama_lengkap'] ?? $user_data['username'];
                        $_SESSION['admin_email'] = $user_data['email'];
                        $_SESSION['admin_username'] = $user_data['username'];
                        
                        header('Location: ' . SITE_URL . '/admin/dashboard.php');
                        exit;
                    } else {
                        // ✅ LOGIN AS USER
                        $_SESSION['user'] = $user_data['id_user'];
                        $_SESSION['user_name'] = $user_data['nama_lengkap'] ?? $user_data['username'];
                        $_SESSION['user_email'] = $user_data['email'];
                        $_SESSION['username'] = $user_data['username'];
                        $_SESSION['role'] = 'user';
                        
                        // ✅ REDIRECT KE INDEX (Homepage), bukan pesanan
                        header('Location: ' . SITE_URL . '/index.php');
                        exit;
                    }
                } else {
                    $error = '❌ Password salah!';
                }
            } else {
                $error = '❌ Username tidak ditemukan!';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 32px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background-color: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
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
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }

        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .btn-home {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #666;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .btn-home:hover {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>
                <i class="fas fa-mobile-alt"></i>
                MobileNest
            </h1>
            <p>Silakan Login ke Akun Anda</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="login-form">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i>
                    Username atau Email
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Masukkan username atau email" 
                    required
                    autocomplete="username"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                >
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i>
                    Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan password" 
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" name="login" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Masuk
            </button>
        </form>

        <div class="login-footer">
            Belum punya akun? <a href="register.php">Daftar sekarang</a>
        </div>
        
        <a href="<?php echo SITE_URL; ?>/index.php" class="btn-home">
            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>
</body>
</html>

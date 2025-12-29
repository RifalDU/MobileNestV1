<?php
session_start();

// ✅ PERBAIKAN PATH CONFIG (Mundur satu folder ke root)
// Ini solusi untuk error "Failed to open stream"
require_once dirname(__DIR__) . '/config.php';

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
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            $error = 'Database error: ' . $conn->error;
        } else {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();
                
                // 2️⃣ VERIFIKASI PASSWORD
                if (password_verify($password, $user_data['password'])) {
                    
                    // 3️⃣ CEK TABEL ADMIN
                    $admin_check_sql = "SELECT id_admin FROM admin WHERE id_user = ?";
                    $admin_stmt = $conn->prepare($admin_check_sql);
                    
                    if (!$admin_stmt) {
                        $error = 'Database error: ' . $conn->error;
                    } else {
                        $admin_stmt->bind_param('i', $user_data['id_user']);
                        $admin_stmt->execute();
                        $admin_result = $admin_stmt->get_result();
                        
                        if ($admin_result->num_rows > 0) {
                            // ✅ LOGIN AS ADMIN
                            $_SESSION['admin'] = $user_data['id_user'];
                            $_SESSION['admin_name'] = $user_data['username'];
                            $_SESSION['admin_email'] = $user_data['email'];
                            
                            // Log activity (Opsional)
                            // ... kode log activity ...
                            
                            header('Location: ' . SITE_URL . '/admin/dashboard.php');
                            exit;
                        } else {
                            // ✅ LOGIN AS USER
                            $_SESSION['user'] = $user_data['id_user'];
                            $_SESSION['user_name'] = $user_data['username'];
                            $_SESSION['user_email'] = $user_data['email'];
                            
                            // Log activity (Opsional)
                            // ... kode log activity ...
                            
                            header('Location: ' . SITE_URL . '/user/pesanan.php');
                            exit;
                        }
                        
                        $admin_stmt->close();
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
            /* ✅ UBAH BACKGROUND JADI BIRU (Sesuai Request) */
            /* Menggunakan warna Bootstrap Primary Blue (#0d6efd) dengan sedikit gradient */
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #0d6efd; /* Ubah warna teks jadi biru juga */
            font-size: 28px;
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
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #0d6efd; /* Fokus warna biru */
            box-shadow: 0 0 5px rgba(13, 110, 253, 0.2);
        }

        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            /* Tombol Biru Solid */
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-login:hover {
            background: #0b5ed7; /* Biru lebih gelap saat hover */
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .login-footer a {
            color: #0d6efd;
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
        }
        .btn-home:hover {
            color: #0d6efd;
            text-decoration: underline;
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
                    Username
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Masukkan username" 
                    required
                    autocomplete="username"
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
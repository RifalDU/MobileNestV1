<?php
require_once '../config.php';

$page_title = "Login";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/script.js";
$logo_path = "../assets/images/logo.jpg";
$home_url = "../index.php";
$produk_url = "../produk/list-produk.php";
$login_url = "login.php";
$register_url = "register.php";
$keranjang_url = "../transaksi/keranjang.php";

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
        $error = 'Username dan password tidak boleh kosong!';
    } else {
        // 1️⃣ QUERY TABEL USERS (Support username OR email)
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
                    
                    // 3️⃣ CEK TABEL ADMIN (Optional)
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
                        
                        // ✅ REDIRECT KE INDEX (Homepage)
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

include '../includes/header.php';
?>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                <div class="card shadow border-0 rounded-lg">
                    <div class="card-body p-4 p-sm-5">
                        <!-- Logo & Title -->
                        <div class="text-center mb-4">
                            <img src="<?php echo $logo_path; ?>" alt="MobileNest Logo" height="50" class="mb-3">
                            <h3 class="fw-bold text-primary">MobileNest</h3>
                            <p class="text-muted">Silakan login ke akun Anda</p>
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
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-bold">
                                    <i class="bi bi-person-fill text-primary"></i> Username atau Email
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
                                <label for="password" class="form-label fw-bold">
                                    <i class="bi bi-lock-fill text-primary"></i> Password
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

                            <button type="submit" name="login" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="my-4 text-center">
                            <small class="text-muted">atau</small>
                        </div>

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="mb-0">Belum punya akun? 
                                <a href="register.php" class="text-decoration-none fw-bold">Daftar di sini</a>
                            </p>
                        </div>

                        <!-- Forgot Password -->
                        <div class="text-center mt-3">
                            <a href="#" class="text-muted text-decoration-none small">
                                <i class="bi bi-question-circle"></i> Lupa password?
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>

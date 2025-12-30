<?php
// Load config FIRST
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php';
require_once '../includes/auth-check.php';
require_user_login();

$user_id = $_SESSION['user'];
$user_data = [];
$errors = [];
$message = '';

// Get user data
$sql = "SELECT id_user, nama_lengkap, email, no_telepon, alamat FROM users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) { die("Prepare failed: " . $conn->error); }
$stmt->bind_param('i', $user_id);
if (!$stmt->execute()) { die("Execute failed: " . $stmt->error); }
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
}
$stmt->close();

// Process form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'edit_profil') {
        $nama = trim($_POST['nama_lengkap'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telepon = trim($_POST['no_telepon'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        
        if (empty($nama)) { $errors[] = 'Nama lengkap tidak boleh kosong'; }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Email tidak valid'; }
        
        // Check email duplicate
        $email_check_sql = "SELECT id_user FROM users WHERE email = ? AND id_user != ?";
        $email_check = $conn->prepare($email_check_sql);
        $email_check->bind_param('si', $email, $user_id);
        $email_check->execute();
        if ($email_check->get_result()->num_rows > 0) {
            $errors[] = 'Email sudah digunakan oleh user lain';
        }
        $email_check->close();
        
        if (empty($errors)) {
            $update_sql = "UPDATE users SET nama_lengkap = ?, email = ?, no_telepon = ?, alamat = ? WHERE id_user = ?";
            $update_stmt = $conn->prepare($update_sql);
            if ($update_stmt) {
                $update_stmt->bind_param('ssssi', $nama, $email, $telepon, $alamat, $user_id);
                if ($update_stmt->execute()) {
                    $message = 'Profil berhasil diperbarui!';
                    $user_data['nama_lengkap'] = $nama;
                    $user_data['email'] = $email;
                    $user_data['no_telepon'] = $telepon;
                    $user_data['alamat'] = $alamat;
                    $_SESSION['user_name'] = $nama;
                } else {
                    $errors[] = 'Error: ' . $update_stmt->error;
                }
                $update_stmt->close();
            }
        }
    } elseif ($action === 'ubah_password') {
        $password_lama = $_POST['password_lama'] ?? '';
        $password_baru = $_POST['password_baru'] ?? '';
        $password_konfirm = $_POST['password_konfirm'] ?? '';
        
        if (empty($password_lama)) { $errors[] = 'Password lama tidak boleh kosong'; }
        if (empty($password_baru) || strlen($password_baru) < 6) { $errors[] = 'Password baru minimal 6 karakter'; }
        if ($password_baru !== $password_konfirm) { $errors[] = 'Password baru tidak sama dengan konfirmasi'; }
        
        if (empty($errors)) {
            $check_pwd_sql = "SELECT password FROM users WHERE id_user = ?";
            $check_pwd = $conn->prepare($check_pwd_sql);
            $check_pwd->bind_param('i', $user_id);
            $check_pwd->execute();
            $pwd_result = $check_pwd->get_result();
            $user = $pwd_result->fetch_assoc();
            $check_pwd->close();
            
            if (password_verify($password_lama, $user['password'])) {
                $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                $pwd_update_sql = "UPDATE users SET password = ? WHERE id_user = ?";
                $pwd_update = $conn->prepare($pwd_update_sql);
                $pwd_update->bind_param('si', $password_hash, $user_id);
                if ($pwd_update->execute()) {
                    $message = 'Password berhasil diubah!';
                } else {
                    $errors[] = 'Error: ' . $pwd_update->error;
                }
                $pwd_update->close();
            } else {
                $errors[] = 'Password lama tidak sesuai';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - MobileNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            
            <!-- Back Button -->
            <div class="mb-3">
                <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>

            <!-- Profile Header Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-person-circle" style="font-size: 80px; color: #0d6efd;"></i>
                    </div>
                    <h3 class="fw-bold"><?php echo htmlspecialchars($user_data['nama_lengkap'] ?? 'User'); ?></h3>
                    <p class="text-muted mb-1"><?php echo htmlspecialchars($user_data['email'] ?? ''); ?></p>
                    <span class="badge bg-secondary">ID: #<?php echo $user_id; ?></span>
                </div>
            </div>

            <!-- Alerts -->
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <div><i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="data-tab" data-bs-toggle="tab" data-bs-target="#data-pribadi" type="button" role="tab">
                        <i class="bi bi-person"></i> Data Pribadi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#ubah-password" type="button" role="tab">
                        <i class="bi bi-lock"></i> Keamanan
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                
                <!-- Tab 1: Data Pribadi -->
                <div class="tab-pane fade show active" id="data-pribadi" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-pencil-square"></i> Edit Informasi Pribadi
                            </h5>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Pastikan semua data yang Anda masukkan akurat dan terbaru.
                            </div>

                            <form method="POST">
                                <input type="hidden" name="action" value="edit_profil">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-person"></i> Nama Lengkap
                                    </label>
                                    <input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($user_data['nama_lengkap'] ?? ''); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-envelope"></i> Email
                                    </label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-phone"></i> No. Telepon
                                    </label>
                                    <input type="text" class="form-control" name="no_telepon" value="<?php echo htmlspecialchars($user_data['no_telepon'] ?? ''); ?>" placeholder="081234567890">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-geo-alt"></i> Alamat Lengkap
                                    </label>
                                    <textarea class="form-control" name="alamat" rows="3" placeholder="Jln. Contoh No. 123, Kota, Provinsi"><?php echo htmlspecialchars($user_data['alamat'] ?? ''); ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Ubah Password -->
                <div class="tab-pane fade" id="ubah-password" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-shield-lock"></i> Ubah Password
                            </h5>

                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> Gunakan password yang kuat dengan kombinasi huruf, angka, dan simbol.
                            </div>

                            <form method="POST">
                                <input type="hidden" name="action" value="ubah_password">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-key"></i> Password Lama
                                    </label>
                                    <input type="password" class="form-control" name="password_lama" placeholder="Masukkan password saat ini" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-lock"></i> Password Baru
                                    </label>
                                    <input type="password" class="form-control" name="password_baru" placeholder="Minimal 6 karakter" required>
                                    <small class="text-muted">Password harus minimal 6 karakter</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-check-circle"></i> Konfirmasi Password
                                    </label>
                                    <input type="password" class="form-control" name="password_konfirm" placeholder="Ulangi password baru" required>
                                </div>

                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-key"></i> Ubah Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

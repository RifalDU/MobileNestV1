<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config.php';
require_once '../includes/auth-check.php';
require_user_login();

$user_id = $_SESSION['user'];
$errors = [];
$message = '';

$sql = "SELECT * FROM users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_profil'])) {
        $nama = trim($_POST['nama_lengkap']);
        $email = trim($_POST['email']);
        $telepon = trim($_POST['no_telepon']);
        $alamat = trim($_POST['alamat']);
        
        if (empty($nama)) $errors[] = 'Nama tidak boleh kosong';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid';
        
        if (empty($errors)) {
            $update = $conn->prepare("UPDATE users SET nama_lengkap=?, email=?, no_telepon=?, alamat=? WHERE id_user=?");
            $update->bind_param('ssssi', $nama, $email, $telepon, $alamat, $user_id);
            if ($update->execute()) {
                $message = 'Profil berhasil diperbarui!';
                $user_data = ['nama_lengkap'=>$nama, 'email'=>$email, 'no_telepon'=>$telepon, 'alamat'=>$alamat];
            }
            $update->close();
        }
    }
    
    if (isset($_POST['ubah_password'])) {
        $old = $_POST['password_lama'];
        $new = $_POST['password_baru'];
        $confirm = $_POST['password_konfirm'];
        
        if (empty($old)) $errors[] = 'Password lama kosong';
        if (strlen($new) < 6) $errors[] = 'Password baru minimal 6 karakter';
        if ($new !== $confirm) $errors[] = 'Password tidak sama';
        
        if (empty($errors)) {
            $check = $conn->prepare("SELECT password FROM users WHERE id_user=?");
            $check->bind_param('i', $user_id);
            $check->execute();
            $pwd = $check->get_result()->fetch_assoc();
            $check->close();
            
            if (password_verify($old, $pwd['password'])) {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET password=? WHERE id_user=?");
                $update->bind_param('si', $hash, $user_id);
                if ($update->execute()) $message = 'Password berhasil diubah!';
                $update->close();
            } else {
                $errors[] = 'Password lama salah';
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: system-ui, -apple-system, sans-serif;
}

.profile-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    margin-bottom: 25px;
}

.profile-header {
    text-align: center;
    padding: 40px 20px;
    border-bottom: 1px solid #f0f0f0;
}

.avatar {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 50px;
    color: white;
    margin-bottom: 15px;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}

.profile-header h4 {
    margin: 10px 0 5px;
    color: #2c3e50;
    font-weight: 600;
}

.profile-header p {
    color: #6c757d;
    margin: 0;
}

.nav-tabs {
    border: none;
    background: white;
    border-radius: 15px;
    padding: 10px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 600;
    padding: 12px 25px;
    border-radius: 10px;
    transition: all 0.3s;
    margin: 0 5px;
}

.nav-tabs .nav-link:hover {
    background: #f8f9fa;
    color: #667eea;
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-label i {
    color: #667eea;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 12px 15px;
    transition: all 0.3s;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}

.btn-danger {
    background: linear-gradient(135deg, #f093fb, #f5576c);
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(245, 87, 108, 0.4);
}

.btn-back {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-back:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

.alert {
    border: none;
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 20px;
}
</style>
</head>
<body>
<div class="container py-4">
<div class="row justify-content-center">
<div class="col-md-9 col-lg-8">

<a href="<?php echo SITE_URL; ?>/index.php" class="btn-back mb-3">
<i class="bi bi-arrow-left"></i> Kembali ke Beranda
</a>

<div class="profile-card profile-header">
<div class="avatar"><i class="bi bi-person-circle"></i></div>
<h4><?php echo htmlspecialchars($user_data['nama_lengkap']); ?></h4>
<p><?php echo htmlspecialchars($user_data['email']); ?></p>
</div>

<?php if($message): ?>
<div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?php echo $message; ?></div>
<?php endif; ?>

<?php if($errors): ?>
<div class="alert alert-danger">
<?php foreach($errors as $e) echo "<div><i class='bi bi-exclamation-triangle-fill'></i> $e</div>"; ?>
</div>
<?php endif; ?>

<ul class="nav nav-tabs">
<li class="nav-item">
<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#data">
<i class="bi bi-person"></i> Data Pribadi
</button>
</li>
<li class="nav-item">
<button class="nav-link" data-bs-toggle="tab" data-bs-target="#password">
<i class="bi bi-shield-lock"></i> Keamanan
</button>
</li>
</ul>

<div class="tab-content">
<div class="tab-pane fade show active" id="data">
<div class="profile-card" style="padding:30px">
<h5 style="margin-bottom:25px;color:#2c3e50"><i class="bi bi-pencil-square"></i> Edit Profil</h5>
<form method="POST">
<div class="mb-3">
<label class="form-label"><i class="bi bi-person"></i> Nama Lengkap</label>
<input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($user_data['nama_lengkap']); ?>" required>
</div>
<div class="mb-3">
<label class="form-label"><i class="bi bi-envelope"></i> Email</label>
<input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
</div>
<div class="mb-3">
<label class="form-label"><i class="bi bi-phone"></i> No. Telepon</label>
<input type="text" class="form-control" name="no_telepon" value="<?php echo htmlspecialchars($user_data['no_telepon']); ?>" placeholder="08xxxxxxxxxx">
</div>
<div class="mb-3">
<label class="form-label"><i class="bi bi-geo-alt"></i> Alamat Lengkap</label>
<textarea class="form-control" name="alamat" rows="3" placeholder="Jln. Contoh No. 123, Kota"><?php echo htmlspecialchars($user_data['alamat']); ?></textarea>
</div>
<button type="submit" name="edit_profil" class="btn btn-primary w-100">
<i class="bi bi-save"></i> Simpan Perubahan
</button>
</form>
</div>
</div>

<div class="tab-pane fade" id="password">
<div class="profile-card" style="padding:30px">
<h5 style="margin-bottom:25px;color:#2c3e50"><i class="bi bi-key"></i> Ubah Password</h5>
<form method="POST">
<div class="mb-3">
<label class="form-label"><i class="bi bi-lock"></i> Password Lama</label>
<input type="password" class="form-control" name="password_lama" placeholder="Masukkan password saat ini" required>
</div>
<div class="mb-3">
<label class="form-label"><i class="bi bi-lock-fill"></i> Password Baru</label>
<input type="password" class="form-control" name="password_baru" placeholder="Minimal 6 karakter" required>
<small class="text-muted"><i class="bi bi-info-circle"></i> Gunakan kombinasi huruf, angka, dan simbol</small>
</div>
<div class="mb-3">
<label class="form-label"><i class="bi bi-check-circle"></i> Konfirmasi Password</label>
<input type="password" class="form-control" name="password_konfirm" placeholder="Ulangi password baru" required>
</div>
<button type="submit" name="ubah_password" class="btn btn-danger w-100">
<i class="bi bi-shield-check"></i> Ubah Password
</button>
</form>
</div>
</div>
</div>

</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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
<title>Profil - MobileNest</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
<div class="row justify-content-center">
<div class="col-md-8">

<a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-sm btn-outline-primary mb-3">← Kembali</a>

<div class="card mb-3">
<div class="card-body text-center">
<h4><?php echo htmlspecialchars($user_data['nama_lengkap']); ?></h4>
<p class="text-muted mb-0"><?php echo htmlspecialchars($user_data['email']); ?></p>
</div>
</div>

<?php if($message): ?>
<div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if($errors): ?>
<div class="alert alert-danger">
<?php foreach($errors as $e) echo "<div>$e</div>"; ?>
</div>
<?php endif; ?>

<ul class="nav nav-tabs mb-3">
<li class="nav-item">
<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#data">Data Pribadi</button>
</li>
<li class="nav-item">
<button class="nav-link" data-bs-toggle="tab" data-bs-target="#password">Password</button>
</li>
</ul>

<div class="tab-content">
<div class="tab-pane fade show active" id="data">
<div class="card">
<div class="card-body">
<form method="POST">
<div class="mb-3">
<label class="form-label">Nama Lengkap</label>
<input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($user_data['nama_lengkap']); ?>" required>
</div>
<div class="mb-3">
<label class="form-label">Email</label>
<input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
</div>
<div class="mb-3">
<label class="form-label">No. Telepon</label>
<input type="text" class="form-control" name="no_telepon" value="<?php echo htmlspecialchars($user_data['no_telepon']); ?>">
</div>
<div class="mb-3">
<label class="form-label">Alamat</label>
<textarea class="form-control" name="alamat" rows="2"><?php echo htmlspecialchars($user_data['alamat']); ?></textarea>
</div>
<button type="submit" name="edit_profil" class="btn btn-primary w-100">Simpan</button>
</form>
</div>
</div>
</div>

<div class="tab-pane fade" id="password">
<div class="card">
<div class="card-body">
<form method="POST">
<div class="mb-3">
<label class="form-label">Password Lama</label>
<input type="password" class="form-control" name="password_lama" required>
</div>
<div class="mb-3">
<label class="form-label">Password Baru (min 6 karakter)</label>
<input type="password" class="form-control" name="password_baru" required>
</div>
<div class="mb-3">
<label class="form-label">Konfirmasi Password</label>
<input type="password" class="form-control" name="password_konfirm" required>
</div>
<button type="submit" name="ubah_password" class="btn btn-danger w-100">Ubah Password</button>
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

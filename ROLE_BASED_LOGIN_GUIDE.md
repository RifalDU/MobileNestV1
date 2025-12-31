# 🔐 Role-Based Login System Guide - MobileNest

**Membedakan Admin dan Users dengan Database Existing**

---

## 📊 CURRENT STRUCTURE

### Database Design (SUDAH ADA!)

Database Anda sudah memiliki 2 table terpisah:

```
┌──────────────────────────────────────┐
│          admin TABLE                 │
├──────────────────────────────────────┤
│ id_admin (PK)                        │
│ username (UNIQUE)                    │
│ password (hashed)                    │
│ nama_lengkap                         │
│ email                                │
│ no_telepon                           │
│ tanggal_dibuat                       │
└──────────────────────────────────────┘

┌──────────────────────────────────────┐
│         users TABLE                  │
├──────────────────────────────────────┤
│ id_user (PK)                         │
│ username (UNIQUE)                    │
│ password (hashed)                    │
│ nama_lengkap                         │
│ email                                │
│ no_telepon                           │
│ alamat                               │
│ tanggal_daftar                       │
│ status_akun (enum: Aktif/Nonaktif)   │
└──────────────────────────────────────┘
```

**KEUNTUNGAN Design Ini:**
- ✅ Terpisah = Admin & User punya struktur berbeda
- ✅ Admin-only columns tidak perlu di users table
- ✅ Lebih aman & clean
- ✅ Easy to scale

---

## 🔑 LOGIN FLOW

### STEP 1: User Submit Login Form

```
┌─────────────────────────────────┐
│   Login Form (login.php)        │
│                                 │
│  • Username input               │
│  • Password input               │
│  • Login button                 │
└─────────────────────────────────┘
           ↓
      (Submit POST)
           ↓
┌─────────────────────────────────┐
│   process_login.php             │
│   (Handle authentication)       │
└─────────────────────────────────┘
```

### STEP 2: Check Both Tables

```php
<?php
// process_login.php

$username = $_POST['username'];
$password = $_POST['password'];

// STRATEGY 1: Check ADMIN first
$admin_query = "SELECT * FROM admin WHERE username = ?"
if ($admin_result) {
    // Admin login
    $_SESSION['role'] = 'admin';
    $_SESSION['id'] = $admin_id;
    redirect('/admin/dashboard');
}

// STRATEGY 2: Check USERS if admin not found
$user_query = "SELECT * FROM users WHERE username = ?"
if ($user_result) {
    // User login
    $_SESSION['role'] = 'user';
    $_SESSION['id'] = $user_id;
    redirect('/user/dashboard');
}

// Neither found
echo "Username atau password salah!";
?>
```

### STEP 3: Set Session & Redirect

```php
// $_SESSION struktur
$_SESSION = [
    'authenticated' => true,
    'role' => 'admin'|'user',      ← KUNCI untuk membedakan!
    'id' => 1|2|3...,              ← Admin ID atau User ID
    'username' => 'admin123',
    'nama_lengkap' => 'John Doe'
];
```

---

## 📁 FILE STRUCTURE

### Untuk Auth System:

```
MobileNest/
├── login.php                  ← Login form (sama untuk semua)
├── api/
│   └── auth/
│       ├── process_login.php  ← Validasi username+password
│       ├── logout.php         ← Clear session
│       └── check_auth.php     ← Middleware check role
├── admin/                      ← Admin only
│   ├── dashboard.php
│   ├── manage_products.php
│   ├── manage_users.php
│   └── ...
└── user/                       ← User only
    ├── profile.php
    ├── order_history.php
    ├── wishlist.php
    └── ...
```

---

## 🛠️ IMPLEMENTATION - Step by Step

### 1️⃣ CREATE: Middleware (check_auth.php)

```php
<?php
// MobileNest/api/auth/check_auth.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['authenticated'])) {
    header('Location: /MobileNest/login.php');
    exit();
}

// Get user role
$user_role = $_SESSION['role'];  // 'admin' or 'user'
$user_id = $_SESSION['id'];
$username = $_SESSION['username'];

// Usage in pages:
// require_once 'api/auth/check_auth.php';
// if ($user_role !== 'admin') {
//     die('Access Denied! Only admins allowed.');
// }

?>
```

### 2️⃣ CREATE: process_login.php

```php
<?php
// MobileNest/api/auth/process_login.php

session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password harus diisi!';
        header('Location: ../../login.php');
        exit();
    }
    
    // STRATEGY: Check admin table first
    $stmt = $conn->prepare("SELECT id_admin, username, password, nama_lengkap, email FROM admin WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // Verify password (assuming passwords are hashed with password_hash())
        if (password_verify($password, $admin['password'])) {
            // Admin login successful
            $_SESSION['authenticated'] = true;
            $_SESSION['role'] = 'admin';          ← ROLE!
            $_SESSION['id'] = $admin['id_admin'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['nama_lengkap'] = $admin['nama_lengkap'];
            
            header('Location: ../../admin/dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = 'Password salah!';
            header('Location: ../../login.php');
            exit();
        }
    }
    
    // Check users table if admin not found
    $stmt = $conn->prepare("SELECT id_user, username, password, nama_lengkap, email, status_akun FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check status akun
        if ($user['status_akun'] !== 'Aktif') {
            $_SESSION['error'] = 'Akun Anda tidak aktif. Hubungi admin!';
            header('Location: ../../login.php');
            exit();
        }
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // User login successful
            $_SESSION['authenticated'] = true;
            $_SESSION['role'] = 'user';           ← ROLE!
            $_SESSION['id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            
            header('Location: ../../user/dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = 'Password salah!';
            header('Location: ../../login.php');
            exit();
        }
    }
    
    // Username not found in both tables
    $_SESSION['error'] = 'Username tidak ditemukan!';
    header('Location: ../../login.php');
    exit();
}

?>
```

### 3️⃣ CREATE: logout.php

```php
<?php
// MobileNest/api/auth/logout.php

session_start();

// Destroy session
session_destroy();

// Redirect to login
header('Location: ../../login.php');
exit();

?>
```

### 4️⃣ UPDATE: login.php (Form)

```php
<?php
session_start();

// Jika sudah login, redirect
if (isset($_SESSION['authenticated'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - MobileNest</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .login-container { max-width: 400px; margin: 100px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #0056b3; }
        .error { color: red; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🔐 MobileNest Login</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="api/auth/process_login.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px; color: #666;">
            Login sebagai Admin atau User - sistem akan otomatis mengarahkan ke dashboard yang tepat!
        </p>
    </div>
</body>
</html>
```

### 5️⃣ CREATE: admin/dashboard.php

```php
<?php
require_once '../api/auth/check_auth.php';

// Cek role
if ($user_role !== 'admin') {
    die('❌ Access Denied! Only admins allowed.');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - MobileNest</title>
</head>
<body>
    <div style="padding: 20px; background: #e8f4f8; border-radius: 8px;">
        <h1>👨‍💼 Admin Dashboard</h1>
        <p>Selamat datang, <strong><?= $username ?></strong>!</p>
        
        <h3>Menu Admin:</h3>
        <ul>
            <li><a href="manage_products.php">📦 Kelola Produk</a></li>
            <li><a href="manage_users.php">👥 Kelola Pengguna</a></li>
            <li><a href="manage_orders.php">📋 Kelola Pesanan</a></li>
            <li><a href="reports.php">📊 Laporan & Analitik</a></li>
            <li><a href="../api/auth/logout.php" style="color: red;">🚪 Logout</a></li>
        </ul>
    </div>
</body>
</html>
```

### 6️⃣ CREATE: user/dashboard.php

```php
<?php
require_once '../api/auth/check_auth.php';

// Cek role
if ($user_role !== 'user') {
    die('❌ Access Denied! Only users allowed.');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - MobileNest</title>
</head>
<body>
    <div style="padding: 20px; background: #f0f9ff; border-radius: 8px;">
        <h1>👤 User Dashboard</h1>
        <p>Selamat datang, <strong><?= $username ?></strong>!</p>
        
        <h3>Menu Pengguna:</h3>
        <ul>
            <li><a href="profile.php">👤 Profil Saya</a></li>
            <li><a href="order_history.php">📦 Riwayat Pesanan</a></li>
            <li><a href="wishlist.php">❤️ Wishlist</a></li>
            <li><a href="settings.php">⚙️ Pengaturan</a></li>
            <li><a href="../api/auth/logout.php" style="color: red;">🚪 Logout</a></li>
        </ul>
    </div>
</body>
</html>
```

---

## 🧪 TEST LOGIN CREDENTIALS

### Admin (dari tabel admin):
```
Username: admin
Password: password123  (atau sesuai database)
→ Akan redirect ke: /admin/dashboard.php
```

### User (dari tabel users):
```
Username: user1
Password: pass1  (atau sesuai database)
→ Akan redirect ke: /user/dashboard.php
```

---

## 🔒 SECURITY BEST PRACTICES

### 1. Password Hashing (SUDAH DI DATABASE!)

Passwords di database HARUS di-hash:

```php
// Saat register/create user
$hashed_password = password_hash($raw_password, PASSWORD_BCRYPT);
// INSERT INTO users (username, password) VALUES (?, ?);

// Saat login
if (password_verify($input_password, $db_password)) {
    // Login success
}
```

**Current DB passwords:** Pastikan sudah di-hash dengan SHA2 atau bcrypt!

### 2. Session Security

```php
// Tambah di awal session
session_start();

// Regenerate session ID (prevent session fixation)
session_regenerate_id(true);

// Set session timeout (30 menit)
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > 1800) {
        session_destroy();
        header('Location: login.php');
        exit();
    }
}
$_SESSION['last_activity'] = time();
```

### 3. Prepared Statements (SUDAH DI CODE ABOVE!)

```php
// ✅ AMAN - Prepared statement
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();

// ❌ TIDAK AMAN - String concatenation
$query = "SELECT * FROM users WHERE username = '$username'";
// Vulnerable to SQL injection!
```

### 4. Role-Based Access Control (RBAC)

```php
// Middleware check role
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('❌ Access Denied!');
}
```

---

## 🚀 IMPLEMENTATION CHECKLIST

- [ ] Create `api/auth/check_auth.php` - Middleware
- [ ] Create `api/auth/process_login.php` - Handle login
- [ ] Create `api/auth/logout.php` - Handle logout
- [ ] Update `login.php` - Login form
- [ ] Create `admin/dashboard.php` - Admin area
- [ ] Create `user/dashboard.php` - User area
- [ ] Test with admin credentials
- [ ] Test with user credentials
- [ ] Verify database passwords are hashed
- [ ] Add session timeout
- [ ] Add CSRF token protection

---

## 📊 DECISION MATRIX

| Scenario | Admin Table | Users Table | Result |
|----------|-------------|-------------|--------|
| Login username found in admin + correct password | ✅ Match | (skip) | → Admin Dashboard |
| Login username found in users + correct password | (skip) | ✅ Match | → User Dashboard |
| Username not found anywhere | ❌ | ❌ | → Error: Username not found |
| Password incorrect (admin) | ✅ Found but ❌ pwd | (skip) | → Error: Wrong password |
| User account inactive | (skip) | ✅ But status='Nonaktif' | → Error: Account inactive |

---

## 🎯 NEXT FEATURES

1. **Admin Features:**
   - Add/edit/delete products
   - Manage users (activate/deactivate)
   - View orders & transactions
   - Reports & analytics

2. **User Features:**
   - View order history
   - Save to wishlist
   - Change password
   - Update profile

3. **Security:**
   - Two-factor authentication (2FA)
   - Email verification
   - Password reset
   - Activity logging

---

## ✅ SUMMARY

**Database Structure:** ✅ SUDAH ADA!
- `admin` table = untuk admin accounts
- `users` table = untuk customer accounts

**Login Logic:** Cek admin dulu, kalo tidak ada cek users

**Session Management:** Set `$_SESSION['role']` = 'admin' atau 'user'

**Access Control:** Check role di setiap protected page

**Result:** Admin & Users see different dashboards! 🎉

---

**Created:** 2025-12-31
**For:** MobileNest E-Commerce Platform
**Status:** Ready for Implementation ✅

# 🔐 MobileNest - Login System Documentation

**Updated: December 31, 2025**

---

## 📊 Database Structure

### Current Tables

#### 1. **USERS TABLE** - User credentials & profile
```sql
CREATE TABLE users (
  id_user INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,        -- hashed with password_hash()
  no_hp VARCHAR(20),
  alamat TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Key Points:**
- `password` hashed dengan **bcrypt** (`password_hash()` & `password_verify()`)
- Supports login via `username` OR `email`
- `nama_lengkap` = User's full name

---

#### 2. **ADMIN TABLE** - Admin users mapping
```sql
CREATE TABLE admin (
  id_admin INT PRIMARY KEY AUTO_INCREMENT,
  id_user INT NOT NULL UNIQUE,
  role VARCHAR(50) DEFAULT 'admin',      -- 'admin', 'manager', 'moderator'
  permissions JSON,                       -- future: detailed permissions
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
);
```

**Purpose:**
- Maps users to admin roles
- Determines if user is **Admin** or **Regular User**
- If user exists in `users` table BUT NOT in `admin` table → **Regular User**
- If user exists in BOTH tables → **Admin**

---

#### 3. **PRODUCTS TABLE** - Smartphone products
```sql
CREATE TABLE products (
  id_produk INT PRIMARY KEY AUTO_INCREMENT,
  nama_produk VARCHAR(100) NOT NULL,
  brand VARCHAR(50) NOT NULL,
  harga DECIMAL(10, 2) NOT NULL,
  spesifikasi TEXT,
  foto VARCHAR(255),
  stok INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

#### 4. **TRANSAKSI TABLE** - Orders
```sql
CREATE TABLE transaksi (
  id_transaksi INT PRIMARY KEY AUTO_INCREMENT,
  id_user INT NOT NULL,
  tanggal_transaksi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  total_harga DECIMAL(10, 2) NOT NULL,
  status VARCHAR(50) DEFAULT 'pending',  -- 'pending', 'completed', 'cancelled'
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
);
```

---

#### 5. **DETAIL_TRANSAKSI TABLE** - Order items
```sql
CREATE TABLE detail_transaksi (
  id_detail INT PRIMARY KEY AUTO_INCREMENT,
  id_transaksi INT NOT NULL,
  id_produk INT NOT NULL,
  jumlah INT NOT NULL,
  harga DECIMAL(10, 2) NOT NULL,
  subtotal DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi) ON DELETE CASCADE,
  FOREIGN KEY (id_produk) REFERENCES products(id_produk) ON DELETE CASCADE
);
```

---

## 🔑 How Admin vs User Login Works

### Login Flow

```
┌─────────────────────────────────────────┐
│   User Input: username/email + password │
└────────────┬────────────────────────────┘
             ↓
┌─────────────────────────────────────────┐
│  Search in USERS table by username/email│
└────────────┬────────────────────────────┘
             ↓
        ┌────┴────┐
        ↓         ↓
     FOUND    NOT FOUND
      ↓         ↓
      │      Error: "Username/email tidak ditemukan"
      │      Redirect to login page
      ↓
┌─────────────────────────────────────────┐
│  Verify password with password_verify() │
└────────────┬────────────────────────────┘
             ↓
        ┌────┴────┐
        ↓         ↓
   CORRECT    WRONG
      ↓         ↓
      │      Error: "Password salah"
      │      Redirect to login page
      ↓
┌─────────────────────────────────────────┐
│ Check if user exists in ADMIN table     │
└────────────┬────────────────────────────┘
             ↓
        ┌────┴────────────┐
        ↓                 ↓
      YES               NO
       ↓                 ↓
  SET SESSION:      SET SESSION:
  $_SESSION['admin'] $_SESSION['user']
  Redirect to:      Redirect to:
  /admin/            /index.php
  dashboard.php      (or previous page)
```

---

## 📝 Session Variables

### When User Login as **REGULAR USER**
```php
$_SESSION['user'] = id_user;              // User ID
$_SESSION['user_name'] = nama_lengkap;    // Full name
$_SESSION['user_email'] = email;          // Email address
$_SESSION['username'] = username;         // Username
$_SESSION['role'] = 'user';               // Role identifier
```

### When User Login as **ADMIN**
```php
$_SESSION['admin'] = id_user;              // Admin ID (same as user ID)
$_SESSION['admin_name'] = nama_lengkap;    // Full name
$_SESSION['admin_email'] = email;          // Email address
$_SESSION['admin_username'] = username;    // Username
// No role session (admin is determined by presence of $_SESSION['admin'])
```

---

## ✅ Authentication Helpers (in config.php)

### Check if User Logged In
```php
if (is_logged_in()) {
    // User is either admin or regular user
    echo "User logged in!";
}
```

### Check if User is Admin
```php
if (is_admin()) {
    // User is admin
    echo "Admin access granted!";
}
```

### Get Current User Info
```php
$user_info = get_user_info();
// Returns:
// Array(
//   'id' => user_id,
//   'role' => 'admin' or 'user',
//   'username' => username
// )
```

### Require Login (for pages that need authentication)
```php
require_login();              // Require any login
require_login(true);          // Require admin login
```

---

## 🔒 Security Implementation

### Password Hashing
- Using **bcrypt** via `password_hash()`
- Hash length: 60 characters
- Format: `$2y$10$...` (bcrypt identifier)

### SQL Injection Prevention
- **Prepared Statements** used for all queries
- User input bound with `bind_param()`
- Example:
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
$stmt->bind_param('ss', $username_or_email, $username_or_email);
$stmt->execute();
```

### CSRF Protection (available in config.php)
```php
$csrf_token = generate_csrf_token();
// In form: <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

// On form submit:
if (!verify_csrf_token($_POST['csrf_token'])) {
    die('CSRF token invalid');
}
```

---

## 📂 Files Related to Login System

### User Authentication
- `MobileNest/user/login.php` - Login form UI
- `MobileNest/user/proses-login.php` - Login processing logic
- `MobileNest/user/register.php` - Registration form
- `MobileNest/user/proses-register.php` - Registration processing
- `MobileNest/user/logout.php` - Logout handler

### User Pages
- `MobileNest/user/profil.php` - User profile page
- `MobileNest/user/pesanan.php` - User orders/transactions page

### Admin Pages
- `MobileNest/admin/dashboard.php` - Admin dashboard
- `MobileNest/admin/...` - Other admin pages

### Configuration
- `MobileNest/config.php` - Database connection + helper functions
- `MobileNest/check-users.php` - Debug tool to check users in database
- `MobileNest/debug-login.php` - Debug tool for login issues

---

## 🧪 Testing Login System

### Step 1: Check Users in Database
Go to: `http://localhost/MobileNest/check-users.php`

This will show:
- All users in database
- Password encryption status
- Which users are admins

### Step 2: Test Login
Go to: `http://localhost/MobileNest/user/login.php`

Try login with:
- **Username** or **Email**
- **Password** (if using test data: `password123`)

### Step 3: Check Session
After login, check F12 → Storage → Cookies
- Look for `PHPSESSID` cookie
- Indicates active session

---

## 🚀 How to Identify Admin vs User

### Method 1: Check Admin Table
```php
$user_id = $_SESSION['user'] ?? null;

if ($user_id) {
    $admin_check = fetch_single(
        "SELECT id_admin FROM admin WHERE id_user = $user_id"
    );
    
    if ($admin_check) {
        echo "This is an ADMIN";
    } else {
        echo "This is a REGULAR USER";
    }
}
```

### Method 2: Check Session (Faster)
```php
if (is_admin()) {
    echo "User is ADMIN";
} else {
    echo "User is REGULAR USER";
}
```

---

## 📋 Quick Reference

| Action | Code | Result |
|--------|------|--------|
| **Check logged in** | `is_logged_in()` | `true` if admin or user |
| **Check admin** | `is_admin()` | `true` if admin |
| **Get user info** | `get_user_info()` | Array with id, role, username |
| **Get admin ID** | `$_SESSION['admin']` | Admin's user ID |
| **Get user ID** | `$_SESSION['user']` | Regular user's ID |
| **Get username** | `$_SESSION['admin_username']` or `$_SESSION['username']` | Username |
| **Logout** | `logout()` | Destroys session |

---

## ⚙️ Future Improvements

- [ ] Role-based permissions (manager, moderator, etc.)
- [ ] Two-factor authentication (2FA)
- [ ] Email verification on signup
- [ ] Password reset functionality
- [ ] Activity logging
- [ ] IP whitelist for admin
- [ ] Session expiry with auto-logout

---

**Questions? Check:**
- Debug tool: `check-users.php`
- Debug login: `debug-login.php`
- Config functions: `config.php`


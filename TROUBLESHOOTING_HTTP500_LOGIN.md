# Troubleshooting Guide: HTTP 500 Error saat Login User

**Problem:** Saat login menggunakan akun user, muncul error "HTTP ERROR 500"

**Status:** In Progress  
**Date:** December 31, 2025

---

## Quick Diagnosis Steps

### Step 1: Run Debug Script 🔧

**Paling Penting - Lakukan ini dulu!**

```
Buka di browser: http://localhost/MobileNest/debug-login.php
```

Script ini akan:
- ✓ Check PHP configuration
- ✓ Test database connection
- ✓ Verify users table exists
- ✓ Show sample users
- ✓ Test login credentials
- ✓ Display recent errors

---

## Common Causes & Solutions

### 🔴 Cause 1: Database Connection Error

**Symptoms:**
```
HTTP 500
PHP Fatal error: Connection refused
```

**Check:**
1. XAMPP Control Panel → MySQL service **RUNNING**?
2. Database name = `mobilenest_db`?
3. Username = `root`, Password = (kosong)?

**Solution:**
```bash
# 1. Start MySQL di XAMPP
# Click "Start" pada MySQL service

# 2. Verify database exists
# Buka: http://localhost/phpmyadmin
# Check if "mobilenest_db" ada di list

# 3. If not exists, create it:
CREATE DATABASE mobilenest_db;

# 4. Import schema:
# phpMyAdmin > mobilenest_db > Import > mobilenest_schema.sql
```

---

### 🔴 Cause 2: Users Table Tidak Ada

**Symptoms:**
```
Table 'mobilenest_db.users' doesn't exist
```

**Check:**
```sql
SHOW TABLES LIKE 'users';
```

**Solution:**
```bash
# Import database schema
1. Download: mobilenest_schema.sql dari repository
2. phpMyAdmin > mobilenest_db > Import
3. Upload file > Go
```

---

### 🔴 Cause 3: Password Hash Tidak Valid

**Symptoms:**
- Login gagal terus meskipun password benar
- Error saat verify password

**Problem:**
Password di database mungkin tidak di-hash dengan benar (MD5/SHA1 instead of password_hash)

**Check:**
```sql
-- Check password format
SELECT username, SUBSTRING(password, 1, 10) as pwd_preview, LENGTH(password) as pwd_length 
FROM users;

-- password_hash() menghasilkan:
-- Length: 60 characters
-- Format: $2y$10$...

-- MD5 menghasilkan:
-- Length: 32 characters
-- Format: 5f4dcc3b5aa765d61d8327deb882cf99
```

**Solution - Update Password:**
```sql
-- Update password untuk user tertentu
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'testuser';

-- Password di atas = 'password' (untuk testing)
```

**Generate New Hash:**
```php
<?php
// Buat file generate-hash.php
echo password_hash('password123', PASSWORD_DEFAULT);
// Copy hasil hash ke UPDATE query
?>
```

---

### 🔴 Cause 4: Session Error

**Symptoms:**
```
Warning: session_start(): Failed to read session data
```

**Solution:**
```php
// In config.php, check session settings:
session_save_path(__DIR__ . '/sessions'); // Create this folder

// Create sessions folder:
mkdir C:\xampp\htdocs\MobileNest\sessions
// Set permissions: writable
```

---

### 🔴 Cause 5: Headers Already Sent

**Symptoms:**
```
Warning: Cannot modify header information - headers already sent
```

**Causes:**
- Whitespace sebelum `<?php`
- `echo` sebelum `header()`
- BOM (Byte Order Mark) di file

**Solution:**
```php
// Check proses-login.php:
// 1. No whitespace before <?php
// 2. No output before header()
// 3. Use exit after header()

header('Location: ../index.php');
exit; // IMPORTANT!
```

---

### 🔴 Cause 6: Redirect Loop

**Symptoms:**
- Page keeps redirecting
- Error 500 after multiple redirects

**Check:**
```php
// In proses-login.php:
// Make sure redirect goes to correct path
header('Location: ../index.php'); // Correct
// NOT:
header('Location: index.php'); // Wrong (infinite loop)
```

---

## Step-by-Step Debugging

### Step 1: Enable Error Display

**Edit `config.php`:**
```php
// Add at the very top (after <?php):
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
```

### Step 2: Check PHP Error Log

**Location:**
```
C:\xampp\htdocs\MobileNest\error.log
```

**Or:**
```
C:\xampp\php\logs\php_error_log
```

**Check for:**
- Fatal errors
- Database connection errors
- Undefined variables
- Parse errors

### Step 3: Test Database Connection

```php
<?php
// Create test-db-simple.php
require_once 'config.php';

if ($conn) {
    echo "Connected to: " . $conn->query("SELECT DATABASE()")->fetch_row()[0];
    
    // Check users table
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $count = $result->fetch_assoc()['count'];
    echo "<br>Total users: $count";
} else {
    echo "Connection failed: " . mysqli_connect_error();
}
?>
```

### Step 4: Test Login Query

```php
<?php
// Create test-login-query.php
require_once 'config.php';

$test_username = 'testuser'; // Change this

$stmt = $conn->prepare(
    "SELECT id_user, username, password FROM users WHERE username = ? OR email = ?"
);
$stmt->bind_param('ss', $test_username, $test_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "User found: " . $user['username'];
    echo "<br>Password hash: " . substr($user['password'], 0, 20) . "...";
} else {
    echo "User NOT found";
}
?>
```

### Step 5: Test Password Verification

```php
<?php
// test-password.php
$plain_password = 'password123';
$hashed_password = '$2y$10$...'; // Get from database

if (password_verify($plain_password, $hashed_password)) {
    echo "Password VALID";
} else {
    echo "Password INVALID";
}
?>
```

---

## Quick Fixes Checklist

### ☐ MySQL Running
```bash
# XAMPP Control Panel
MySQL: [Running] ✓
```

### ☐ Database Exists
```sql
SHOW DATABASES LIKE 'mobilenest_db';
-- Result: mobilenest_db
```

### ☐ Users Table Exists
```sql
SHOW TABLES FROM mobilenest_db LIKE 'users';
-- Result: users
```

### ☐ Users Table Has Data
```sql
SELECT COUNT(*) FROM users;
-- Result: > 0
```

### ☐ Password Format Correct
```sql
SELECT username, LENGTH(password) FROM users;
-- Length should be 60 (password_hash)
-- NOT 32 (MD5) or 40 (SHA1)
```

### ☐ Config.php Correct
```php
$db_name = 'mobilenest_db'; // Not 'mobilenest' or other
```

### ☐ Session Working
```php
session_status() === PHP_SESSION_ACTIVE // true
```

### ☐ File Paths Correct
```php
require_once '../config.php'; // From user/proses-login.php
```

---

## Testing Workflow

### 1. Create Test User (If No Users)

```sql
-- Via phpMyAdmin SQL tab
INSERT INTO users (username, password, email, nama_lengkap, nomor_telepon) 
VALUES (
    'testuser',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'test@example.com',
    'Test User',
    '081234567890'
);

-- Username: testuser
-- Password: password
```

### 2. Test Login via Debug Script

```
1. Open: http://localhost/MobileNest/debug-login.php
2. Scroll to "Live Login Test"
3. Enter: testuser
4. Password: password
5. Click "Test Login"
6. Check result
```

### 3. If Debug Test Works, Try Real Login

```
1. Open: http://localhost/MobileNest/user/login.php
2. Enter same credentials
3. Click Login
4. Should redirect to index.php
```

---

## Advanced Debugging

### Check Apache Error Log

**Windows (XAMPP):**
```
C:\xampp\apache\logs\error.log
```

**Look for:**
```
PHP Fatal error
PHP Parse error
Segmentation fault
```

### Enable Detailed MySQL Errors

```php
// In config.php, after connection:
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
```

### Add Debug Logging

```php
// In proses-login.php, add logging:
file_put_contents(
    __DIR__ . '/../debug.log',
    date('Y-m-d H:i:s') . " - Login attempt: $username_or_email\n",
    FILE_APPEND
);
```

---

## Solutions Summary

| Problem | Solution | Time |
|---------|----------|------|
| MySQL not running | Start MySQL in XAMPP | 1 min |
| Database not exists | Import mobilenest_schema.sql | 2 min |
| Wrong password hash | Update with password_hash() | 2 min |
| Session error | Create sessions folder | 1 min |
| Headers sent | Remove whitespace before <?php | 2 min |
| Wrong redirect | Fix header Location path | 1 min |

---

## Prevention Tips

1. **Always use password_hash()** untuk password
2. **Always use prepared statements** untuk queries
3. **Always add exit after header()** redirects
4. **Always enable error logging** during development
5. **Always test** dengan debug script sebelum production

---

## Contact & Support

**If still having issues after trying all solutions:**

1. Run `debug-login.php` dan screenshot hasil
2. Check `error.log` dan copy last 10 lines
3. Check database dengan `verify-database-structure.php`
4. Provide info:
   - PHP Version
   - MySQL Version
   - Exact error message
   - Steps to reproduce

---

**Last Updated:** December 31, 2025  
**Status:** Active Troubleshooting  
**Priority:** High

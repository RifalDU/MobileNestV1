# MobileNest Database - Quick Start Guide

**TL;DR** - Jika database Anda sudah ada, ikuti langkah-langkah di bawah untuk update ke schema terbaru.

---

## 📄 3 Pilihan Migrasi (Pilih Salah Satu)

### 🔴 **PILIHAN A: Reset Total (REKOMENDASI - Paling Cepat & Aman)**

**Cocok untuk:** Database baru, data dummy, atau testing.

**Step 1:** Backup database lama (opsional)
```bash
mysqldump -u root -p mobilenest_db > backup_lama.sql
```

**Step 2:** Drop database lama
```sql
DROP DATABASE IF EXISTS mobilenest_db;
```

**Step 3:** Buat database baru
- Buka phpMyAdmin → Databases → Create database → `mobilenest_db`
- Atau via MySQL CLI:
```sql
CREATE DATABASE mobilenest_db;
```

**Step 4:** Import schema baru
- Di phpMyAdmin:
  1. Select database `mobilenest_db`
  2. Click "Import" tab
  3. Upload file: `mobilenest_schema.sql`
  4. Click "Go"

- Atau via MySQL CLI:
```bash
mysql -u root -p mobilenest_db < mobilenest_schema.sql
```

**Step 5:** Verifikasi
```sql
SHOW TABLES;
-- Should show 8 tables: admin, users, produk, promo, transaksi, detail_transaksi, keranjang, ulasan
```

---

### 🟠 **PILIHAN B: Update Structure (Data Tetap Aman)**

**Cocok untuk:** Database dengan data penting yang ingin dipertahankan.

**Step 1:** Lihat struktur database saat ini
```sql
SHOW TABLES;
DESC users;  -- Lihat struktur per tabel
```

**Step 2:** Run ALTER commands
Jalankan one by one di phpMyAdmin atau MySQL CLI:

```sql
-- 1. Tambah kolom yang hilang ke users table
ALTER TABLE users ADD COLUMN alamat TEXT DEFAULT NULL AFTER email;
ALTER TABLE users ADD COLUMN status_akun ENUM('Aktif','Nonaktif') DEFAULT 'Aktif';

-- 2. Tambah foreign keys
ALTER TABLE transaksi ADD CONSTRAINT fk_transaksi_user 
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE;

ALTER TABLE keranjang ADD CONSTRAINT fk_keranjang_user 
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE;

ALTER TABLE keranjang ADD CONSTRAINT fk_keranjang_produk 
  FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE CASCADE;

ALTER TABLE ulasan ADD CONSTRAINT fk_ulasan_user 
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE;

ALTER TABLE ulasan ADD CONSTRAINT fk_ulasan_produk 
  FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE CASCADE;

ALTER TABLE detail_transaksi ADD CONSTRAINT fk_detail_transaksi 
  FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi) ON DELETE CASCADE;

ALTER TABLE detail_transaksi ADD CONSTRAINT fk_detail_produk 
  FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE RESTRICT;

-- 3. Tambah indexes
ALTER TABLE users ADD KEY idx_users_username (username);
ALTER TABLE users ADD KEY idx_users_email (email);
ALTER TABLE produk ADD KEY idx_produk_kategori (kategori);
ALTER TABLE transaksi ADD KEY idx_transaksi_id_user (id_user);
```

**Step 3:** Verifikasi
```sql
-- Check foreign keys
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'mobilenest_db' AND REFERENCED_TABLE_NAME IS NOT NULL;
```

---

### 🔌 **PILIHAN C: Advanced (Recreate dengan Data Mapping)**

**Cocok untuk:** Perubahan kompleks atau transformasi data.

See: `DATABASE_MIGRATION_GUIDE.md` → Opsi 3

---

## ✅ Verifikasi Setelah Migrasi

### Cara 1: Via Browser (GUI)
```
URL: http://localhost/MobileNest/verify-database-structure.php
```
Buka di browser untuk visual report lengkap.

### Cara 2: Via MySQL (CLI)

```sql
-- Check semua tabel
SHOW TABLES;

-- Check struktur tabel
DESC admin;
DESC users;
DESC produk;
DESC promo;
DESC transaksi;
DESC detail_transaksi;
DESC keranjang;
DESC ulasan;

-- Check Foreign Keys
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'mobilenest_db';

-- Check Indexes
SHOW INDEX FROM users;
SHOW INDEX FROM produk;
```

---

## 📚 File-File Dokumentasi

| File | Tujuan |
|------|--------|
| `mobilenest_schema.sql` | SQL dump lengkap untuk buat database baru |
| `DATABASE_SCHEMA.md` | Dokumentasi lengkap setiap tabel & kolom |
| `DATABASE_ERD.md` | Entity Relationship Diagram & relasi tabel |
| `DATABASE_MIGRATION_GUIDE.md` | Panduan detail untuk migrasi database |
| `verify-database-structure.php` | PHP script untuk verifikasi struktur DB |
| `test-connection.php` | Test koneksi database dari aplikasi |

---

## 📍 Recommended Workflow

### Untuk Development (Testing)
1. **Pilih Opsi A (Reset)** - Paling cepat
2. Jalankan `mobilenest_schema.sql`
3. Buka `verify-database-structure.php` di browser untuk verifikasi
4. Jalankan `test-connection.php` untuk test dari aplikasi
5. Siap development!

### Untuk Production (Data Exist)
1. **Backup** database lama
2. **Pilih Opsi B (Update)** - Lebih aman untuk data
3. Jalankan ALTER commands satu per satu
4. Test setiap ALTER sebelum lanjut
5. Verifikasi dengan query di atas
6. Monitor aplikasi untuk error

---

## ⚠️ Common Issues & Solutions

### ❌ Error: "Unknown database 'mobilenest_db'"
```sql
-- Solusi: Create database dulu
CREATE DATABASE mobilenest_db;
USE mobilenest_db;
```

### ❌ Error: "Syntax error in line X"
```
-- Solusi: Copy-paste SQL queries satu per satu (jangan sekaligus)
-- Atau gunakan:
mysql -u root -p < mobilenest_schema.sql
```

### ❌ Error: "Cannot add Foreign Key"
```sql
-- Solusi: Disable FK check dulu
SET FOREIGN_KEY_CHECKS=0;
-- [jalankan query]
SET FOREIGN_KEY_CHECKS=1;
```

### ❌ Data Hilang Setelah Migrasi
```
Normalkah? ✅ Yes!
- Jika pilih Opsi A (Reset), data lama akan hilang (sudah di-backup kan?)
- Jika pilih Opsi B (Update), data tetap aman
- Restore dari backup: mysql -u root -p mobilenest_db < backup.sql
```

---

## 🔓 Test Database Connectivity

### From Browser
```
http://localhost/MobileNest/test-connection.php
```
Tampilkan test results lengkap.

### From PHP Code
```php
<?php
require_once 'MobileNest/config.php';

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Connection Error: " . mysqli_connect_error());
}
echo "Connected!";
mysqli_close($conn);
?>
```

---

## 📄 Sample Test Queries

```sql
-- Test INSERT
INSERT INTO users (username, password, nama_lengkap, email) 
VALUES ('testuser', SHA2('password', 256), 'Test User', 'test@example.com');

-- Test SELECT
SELECT * FROM users;

-- Test JOIN
SELECT u.nama_lengkap, t.total_harga, t.tanggal_transaksi
FROM users u
JOIN transaksi t ON u.id_user = t.id_user;

-- Test Foreign Key (should fail if constraint works)
INSERT INTO transaksi (id_user, total_harga) 
VALUES (99999, 100000);  -- id_user 99999 tidak ada -> ERROR (baik!)
```

---

## 📈 Database Statistics

| Tabel | Columns | Primary Key | Foreign Keys | Indexes |
|-------|---------|-------------|--------------|----------|
| admin | 7 | id_admin | 0 | 2 |
| users | 9 | id_user | 0 | 3 |
| produk | 11 | id_produk | 0 | 5 |
| promo | 9 | id_promo | 0 | 3 |
| transaksi | 12 | id_transaksi | 1 (users) | 4 |
| detail_transaksi | 6 | id_detail | 2 (transaksi, produk) | 2 |
| keranjang | 5 | id_keranjang | 2 (users, produk) | 2 |
| ulasan | 6 | id_ulasan | 2 (users, produk) | 3 |
| **TOTAL** | **65** | **8** | **9** | **24** |

---

## 👋 Next Steps

1. ✅ **Migrasi database** (pilih salah satu opsi)
2. ✅ **Verifikasi struktur** (buka verify-database-structure.php)
3. ✅ **Test koneksi** (buka test-connection.php)
4. ✅ **Update aplikasi code** (jika ada perubahan config)
5. ✅ **Test aplikasi** (login, browse produk, checkout)
6. ✅ **Done!** 🎉

---

**Last Updated:** December 30, 2025  
**Database Version:** 1.0  
**Status:** Ready for Implementation

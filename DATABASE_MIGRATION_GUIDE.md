# MobileNest Database Migration Guide

**Tujuan:** Panduan lengkap untuk mengubah struktur database yang sudah ada agar sesuai dengan schema terbaru.

---

## ⚠️ PENTING: Backup Terlebih Dahulu!

**SEBELUM melakukan perubahan apapun, SELALU backup database terlebih dahulu:**

```bash
# Via Command Line
mysqldump -u root -p mobilenest_db > mobilenest_db_backup_$(date +%Y%m%d_%H%M%S).sql

# Via phpMyAdmin
# 1. Select database: mobilenest_db
# 2. Click "Export" tab
# 3. Click "Go" untuk download SQL file
```

---

## 🚧 3 Pilihan Migrasi

### **OPSI 1: Reset Database (Recommended - Jika Data Masih Sedikit) ✅**

Hapus database lama dan buat yang baru dengan schema lengkap.

**Keuntungan:**
- ✅ Paling bersih dan aman
- ✅ Schema sesuai standar 100%
- ✅ Tidak ada konflik kolom

**Kerugian:**
- ❌ Semua data lama akan hilang

**Langkah-langkah:**

#### Step 1: Backup Data Lama (Jika Ada yang Ingin Disimpan)
```bash
mysqldump -u root -p mobilenest_db > mobilenest_db_backup_old.sql
```

#### Step 2: Drop Database Lama
```sql
-- Via phpMyAdmin atau MySQL CLI
DROP DATABASE IF EXISTS mobilenest_db;
```

#### Step 3: Buat Database Baru dengan Schema Lengkap
```sql
-- Copy semua query dari file: mobilenest_schema.sql
-- Dan jalankan di phpMyAdmin atau MySQL CLI

CREATE DATABASE IF NOT EXISTS `mobilenest_db`;
USE `mobilenest_db`;

-- [COPY SEMUA CREATE TABLE STATEMENTS dari mobilenest_schema.sql]
```

#### Step 4: Verifikasi
```sql
-- Check semua tabel sudah ada
SHOW TABLES;

-- Check struktur setiap tabel
DESC users;
DESC produk;
DESC admin;
-- ... dan seterusnya
```

---

### **OPSI 2: Alter Tabel Existing (Jika Data Penting) ⚖️**

Mengubah struktur tabel yang sudah ada tanpa menghapus data.

**Keuntungan:**
- ✅ Data lama tetap aman
- ✅ Modular (bisa ubah bertahap)

**Kerugian:**
- ❌ Lebih kompleks
- ❌ Perlu perhatian detail

#### Langkah-langkah Umum:

**A. Tambah Kolom Baru**
```sql
ALTER TABLE users ADD COLUMN alamat TEXT DEFAULT NULL AFTER email;
ALTER TABLE users ADD COLUMN status_akun ENUM('Aktif','Nonaktif') DEFAULT 'Aktif';
```

**B. Ubah Tipe Data Kolom**
```sql
ALTER TABLE produk CHANGE COLUMN harga harga DECIMAL(10,2) NOT NULL;
```

**C. Rename Kolom**
```sql
ALTER TABLE transaksi CHANGE COLUMN status status_pesanan VARCHAR(50);
```

**D. Drop Kolom (Jika Ada)**
```sql
ALTER TABLE users DROP COLUMN kolom_lama;
```

**E. Tambah Foreign Key**
```sql
ALTER TABLE transaksi ADD CONSTRAINT fk_transaksi_user 
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE;
```

**F. Tambah Index**
```sql
ALTER TABLE users ADD KEY idx_users_email (email);
ALTER TABLE produk ADD KEY idx_produk_kategori (kategori);
```

---

### **OPSI 3: Recreate Tabel dengan INSERT...SELECT (Advanced)**

Untuk perubahan kompleks tanpa kehilangan data.

**Keuntungan:**
- ✅ Kontrol penuh
- ✅ Bisa mapping data dengan formula

**Kerugian:**
- ❌ Paling kompleks
- ❌ Rawan error

#### Contoh:
```sql
-- Buat tabel baru dengan struktur yang benar
CREATE TABLE users_new LIKE users;

-- Copy data dari tabel lama ke baru
INSERT INTO users_new 
SELECT id_user, username, password, nama_lengkap, email, 
       no_telepon, alamat, tanggal_daftar, 'Aktif' as status_akun
FROM users;

-- Rename tabel
RENAME TABLE users TO users_old;
RENAME TABLE users_new TO users;

-- Verifikasi kemudian drop tabel lama
DROP TABLE users_old;
```

---

## 📄 Checklist Migrasi Step-by-Step

### **Phase 1: Persiapan (PALING PENTING)**
- [ ] Backup database: `mysqldump -u root -p mobilenest_db > backup.sql`
- [ ] Screenshot struktur tabel lama (untuk referensi)
- [ ] Catat semua data penting yang ingin dipertahankan
- [ ] Hentikan aplikasi sementara (jangan ada request ke DB)

### **Phase 2: Eksekusi Migrasi**

**Jika pilih OPSI 1 (Reset):**
- [ ] Drop database lama: `DROP DATABASE mobilenest_db;`
- [ ] Buat database baru dari `mobilenest_schema.sql`
- [ ] Jalankan semua CREATE TABLE statements
- [ ] Verifikasi semua 8 tabel ada

**Jika pilih OPSI 2 (Alter):**
- [ ] Jalankan ALTER TABLE commands satu per satu
- [ ] Test setiap ALTER
- [ ] Check tidak ada error

### **Phase 3: Verifikasi**
- [ ] Check semua tabel ada: `SHOW TABLES;`
- [ ] Check struktur setiap tabel: `DESC table_name;`
- [ ] Verifikasi Foreign Keys terbentuk
- [ ] Test INSERT data dummy
- [ ] Test SELECT dengan JOIN

### **Phase 4: Update Config & Test**
- [ ] Update `MobileNest/config.php` jika ada perubahan connection
- [ ] Jalankan `test-connection.php`
- [ ] Jalankan `test-cart.php`
- [ ] Test UI aplikasi (login, browse produk, checkout)

### **Phase 5: Go Live**
- [ ] Restart XAMPP (Apache & MySQL)
- [ ] Buka aplikasi di browser
- [ ] Test semua fitur utama
- [ ] Simpan backup akhir

---

## 💻 SQL Scripts Siap Pakai

### **Script A: Cek Struktur Database Saat Ini**
```sql
-- Lihat semua tabel
SHOW TABLES;

-- Lihat struktur tabel users
DESC users;
SHOW CREATE TABLE users\G

-- Lihat semua Foreign Keys
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'mobilenest_db' AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Lihat semua Indexes
SHOW INDEX FROM users;
```

### **Script B: Migrasi Step-by-Step (Aman)**
```sql
-- 1. SET FOREIGN KEY CHECKS OFF untuk menghindari constraint error
SET FOREIGN_KEY_CHECKS=0;

-- 2. Buat tabel baru jika belum ada
CREATE TABLE IF NOT EXISTS admin (...);
CREATE TABLE IF NOT EXISTS users (...);
CREATE TABLE IF NOT EXISTS produk (...);
-- ... dst

-- 3. Restore Foreign Keys
SET FOREIGN_KEY_CHECKS=1;

-- 4. Verifikasi
SHOW TABLES;
DESC users;
```

### **Script C: Reset Penuh (Nuclear Option)**
```sql
-- Hanya jalankan jika yakin ingin menghapus semua data lama!
DROP DATABASE IF EXISTS mobilenest_db;
CREATE DATABASE mobilenest_db;
USE mobilenest_db;

-- [COPY SELURUH ISI mobilenest_schema.sql]
```

---

## ⚠️ Common Mistakes & Solutions

### ❌ Error: "Duplicate key name"
```sql
-- Ini terjadi kalau index sudah ada
-- Solusi: Check dulu sebelum create
SHOW INDEX FROM users;
```

### ❌ Error: "Cannot add or update a child row"
```sql
-- Foreign key violation
-- Solusi: Disable FK check sementara
SET FOREIGN_KEY_CHECKS=0;
-- [jalankan query]
SET FOREIGN_KEY_CHECKS=1;
```

### ❌ Error: "Cannot delete or update a parent row"
```sql
-- Child table masih punya reference
-- Solusi: Hapus child records dulu atau set ON DELETE CASCADE
DELETE FROM detail_transaksi WHERE id_produk = 5;
```

### ❌ Query Timeout (Database Besar)
```sql
-- Gunakan LIMIT untuk batch processing
INSERT INTO users_new 
SELECT * FROM users LIMIT 1000;

-- Repeat sampai semua data ter-copy
```

---

## 👋 Best Practices

### ✅ DO:
1. **ALWAYS BACKUP** sebelum migrasi
2. **Test di development** sebelum production
3. **Dokumentasi** setiap perubahan yang dilakukan
4. **Verifikasi** setelah setiap step
5. **Disable application** selama migrasi
6. **Use transactions** untuk atomic operations

### ❌ DON'T:
1. ❌ Ubah multiple tabel sekaligus tanpa test
2. ❌ Lupa disable Foreign Key checks
3. ❌ Tidak backup sebelum DROP
4. ❌ Rename tabel tanpa verifikasi dulu
5. ❌ Jalankan di production tanpa sandbox test
6. ❌ Lupa update aplikasi code setelah schema change

---

## 📚 Database Version Control

### Versioning System
```
mobilenest_schema.sql      (v1.0 - Current)
mobilenest_schema_v0.9.sql (v0.9 - Previous backup)
mobilenest_schema_v0.8.sql (v0.8 - Archive)
```

### Create Version Backup
```bash
# Setiap kali ada perubahan, backup dengan versi
mysqldump -u root -p mobilenest_db > mobilenest_schema_v1.0_$(date +%Y%m%d).sql
```

---

## 📄 After Migration Checklist

Setelah migrasi selesai:

- [ ] Semua 8 tabel ada dan struktur benar
- [ ] Foreign Keys berfungsi
- [ ] Indexes terbentuk
- [ ] Sample data bisa insert tanpa error
- [ ] JOIN queries berfungsi
- [ ] Config.php tidak perlu diubah (atau sudah diupdate)
- [ ] Aplikasi PHP connect ke database
- [ ] test-connection.php berjalan sukses
- [ ] test-cart.php berjalan sukses
- [ ] Semua halaman aplikasi berfungsi normal

---

## 📧 Support & Help

Jika ada error selama migrasi:

1. **Cek error message** di phpMyAdmin atau MySQL CLI
2. **Restore dari backup** jika ada kesalahan
3. **Lihat file:** `mobilenest_schema.sql` untuk referensi struktur benar
4. **Jalankan verification script** di atas untuk diagnosa

---

**Created:** December 30, 2025  
**Database Version:** 1.0  
**Status:** Ready for Migration

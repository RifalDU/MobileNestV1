-- ============================================
-- MobileNest Database Schema
-- ============================================
-- Database: mobilenest_db
-- Created: 2025-12-30
-- Version: 1.0
-- Description: Complete database schema for MobileNest E-Commerce Platform
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS `mobilenest_db`;
USE `mobilenest_db`;

-- ============================================
-- TABLE: admin
-- Purpose: Store administrator accounts
-- ============================================
CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `tanggal_dibuat` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_admin`),
  KEY `idx_admin_username` (`username`),
  KEY `idx_admin_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: users
-- Purpose: Store customer/user accounts
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_daftar` datetime DEFAULT CURRENT_TIMESTAMP,
  `status_akun` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  PRIMARY KEY (`id_user`),
  KEY `idx_users_username` (`username`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_status_akun` (`status_akun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: produk
-- Purpose: Store product catalog
-- ============================================
CREATE TABLE IF NOT EXISTS `produk` (
  `id_produk` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(100) NOT NULL,
  `merek` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `spesifikasi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `status_produk` enum('Tersedia','Habis') DEFAULT 'Tersedia',
  `tanggal_ditambahkan` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_produk`),
  KEY `idx_produk_nama` (`nama_produk`),
  KEY `idx_produk_kategori` (`kategori`),
  KEY `idx_produk_status` (`status_produk`),
  KEY `idx_produk_merek` (`merek`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: promo
-- Purpose: Store promotion/discount programs
-- ============================================
CREATE TABLE IF NOT EXISTS `promo` (
  `id_promo` int(11) NOT NULL AUTO_INCREMENT,
  `nama_promo` varchar(100) NOT NULL,
  `jenis_promo` varchar(50) DEFAULT NULL COMMENT 'persentase atau nominal',
  `nilai_diskon` decimal(10,2) DEFAULT NULL COMMENT 'untuk diskon nominal',
  `persentase_diskon` decimal(5,2) DEFAULT NULL COMMENT 'untuk diskon persentase (0-100)',
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_promo` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id_promo`),
  KEY `idx_promo_status` (`status_promo`),
  KEY `idx_promo_tanggal_mulai` (`tanggal_mulai`),
  KEY `idx_promo_tanggal_selesai` (`tanggal_selesai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: transaksi
-- Purpose: Store order/transaction records
-- ============================================
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id_transaksi` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `status_pesanan` varchar(50) DEFAULT NULL COMMENT 'Pending, Konfirmasi, Dikirim, Selesai',
  `metode_pembayaran` varchar(50) DEFAULT NULL COMMENT 'Transfer, COD, E-wallet, dll',
  `alamat_pengiriman` text DEFAULT NULL,
  `no_resi` varchar(50) DEFAULT NULL,
  `tanggal_transaksi` datetime DEFAULT CURRENT_TIMESTAMP,
  `tanggal_dikirim` datetime DEFAULT NULL,
  `kode_transaksi` varchar(50) DEFAULT NULL UNIQUE,
  `catatan_user` text DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_transaksi`),
  KEY `idx_transaksi_id_user` (`id_user`),
  KEY `idx_transaksi_status_pesanan` (`status_pesanan`),
  KEY `idx_transaksi_tanggal` (`tanggal_transaksi`),
  KEY `idx_transaksi_kode` (`kode_transaksi`),
  CONSTRAINT `fk_transaksi_id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: detail_transaksi
-- Purpose: Store transaction items breakdown
-- ============================================
CREATE TABLE IF NOT EXISTS `detail_transaksi` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_transaksi` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL COMMENT 'jumlah * harga_satuan',
  PRIMARY KEY (`id_detail`),
  KEY `idx_detail_transaksi_id_transaksi` (`id_transaksi`),
  KEY `idx_detail_transaksi_id_produk` (`id_produk`),
  CONSTRAINT `fk_detail_transaksi_id_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_transaksi_id_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: keranjang
-- Purpose: Store shopping cart items
-- ============================================
CREATE TABLE IF NOT EXISTS `keranjang` (
  `id_keranjang` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `tanggal_ditambahkan` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_keranjang`),
  UNIQUE KEY `idx_keranjang_user_produk` (`id_user`, `id_produk`),
  KEY `idx_keranjang_id_user` (`id_user`),
  KEY `idx_keranjang_id_produk` (`id_produk`),
  CONSTRAINT `fk_keranjang_id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `fk_keranjang_id_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: ulasan
-- Purpose: Store product reviews and ratings
-- ============================================
CREATE TABLE IF NOT EXISTS `ulasan` (
  `id_ulasan` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `rating` int(11) NOT NULL COMMENT 'Rating 1-5 bintang',
  `komentar` text DEFAULT NULL,
  `tanggal_ulasan` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ulasan`),
  KEY `idx_ulasan_id_user` (`id_user`),
  KEY `idx_ulasan_id_produk` (`id_produk`),
  KEY `idx_ulasan_rating` (`rating`),
  CONSTRAINT `fk_ulasan_id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `fk_ulasan_id_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE,
  CONSTRAINT `ck_rating` CHECK (`rating` >= 1 AND `rating` <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Optional: Sample Data Insertion
-- ============================================
-- Uncomment these if you want to add sample data

/*
-- Insert Admin
INSERT INTO admin (username, password, nama_lengkap, email, no_telepon) 
VALUES ('admin', SHA2('password123', 256), 'Administrator MobileNest', 'admin@mobilenest.com', '081234567890');

-- Insert Sample Users
INSERT INTO users (username, password, nama_lengkap, email, no_telepon, alamat) VALUES
('user1', SHA2('pass1', 256), 'Budi Santoso', 'budi@email.com', '081234567891', 'Jl. Merdeka No. 10, Jakarta'),
('user2', SHA2('pass2', 256), 'Siti Nurhaliza', 'siti@email.com', '081234567892', 'Jl. Sudirman No. 25, Bandung');

-- Insert Sample Products
INSERT INTO produk (nama_produk, merek, harga, stok, kategori, spesifikasi) VALUES
('Samsung Galaxy S23', 'Samsung', 12999000, 15, 'Flagship', 'RAM 8GB, Storage 256GB, Battery 3900mAh'),
('iPhone 14 Pro', 'Apple', 16999000, 10, 'Flagship', 'RAM 6GB, Storage 128GB, iOS 16');
*/

-- ============================================
-- Indexes for Performance
-- ============================================

-- Additional indexes (already defined in table creation)
-- These can be created separately if needed:

CREATE INDEX idx_transaksi_metode_pembayaran ON transaksi(metode_pembayaran);
CREATE INDEX idx_detail_transaksi_subtotal ON detail_transaksi(subtotal);
CREATE INDEX idx_keranjang_tanggal ON keranjang(tanggal_ditambahkan);
CREATE INDEX idx_ulasan_tanggal ON ulasan(tanggal_ulasan);
CREATE INDEX idx_produk_harga ON produk(harga);
CREATE INDEX idx_produk_stok ON produk(stok);

-- ============================================
-- End of Schema
-- ============================================

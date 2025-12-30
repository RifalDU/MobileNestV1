# MobileNest Database - Entity Relationship Diagram (ERD)

## Visual ERD Representation

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                        MOBILENEST E-COMMERCE DATABASE                            │
│                                  mobilenest_db                                   │
└─────────────────────────────────────────────────────────────────────────────────┘


┌──────────────────────────────────────────────────────────────────────────────────┐
│  PRIMARY TABLES (Core Entities)                                                  │
└──────────────────────────────────────────────────────────────────────────────────┘


  ┏━━━━━━━━━━━━━━━━━━━━┓
  ┃      ADMIN         ┃
  ┣━━━━━━━━━━━━━━━━━━━━┫
  ┃ id_admin (PK)      ┃
  ┃ username           ┃
  ┃ password           ┃
  ┃ nama_lengkap       ┃
  ┃ email              ┃
  ┃ no_telepon         ┃
  ┃ tanggal_dibuat     ┃
  ┗━━━━━━━━━━━━━━━━━━━━┛


  ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
  ┃          USERS                   ┃
  ┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
  ┃ id_user (PK)                     ┃
  ┃ username (UNIQUE)                ┃
  ┃ password                         ┃
  ┃ nama_lengkap                     ┃
  ┃ email                            ┃
  ┃ no_telepon                       ┃
  ┃ alamat                           ┃
  ┃ tanggal_daftar                   ┃
  ┃ status_akun (Aktif/Nonaktif)     ┃
  ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛
        │
        ├──────────────────────┬──────────────────────┐
        │                      │                      │
        │ (1:N)                │ (1:N)                │ (1:N)
        ▼                      ▼                      ▼
  ┌──────────────────────────────┐  ┌────────────────────────────┐  ┌────────────────────────────┐
  │    TRANSAKSI                 │  │    KERANJANG               │  │    ULASAN                  │
  ├──────────────────────────────┤  ├────────────────────────────┤  ├────────────────────────────┤
  │ id_transaksi (PK)            │  │ id_keranjang (PK)          │  │ id_ulasan (PK)             │
  │ id_user (FK) ──────┐         │  │ id_user (FK) ──────┐       │  │ id_user (FK) ──────┐       │
  │ total_harga        │         │  │ id_produk (FK) ────┼──┐   │  │ id_produk (FK) ────┼──┐   │
  │ status_pesanan     │         │  │ jumlah             │  │   │  │ rating             │  │   │
  │ metode_pembayaran  │         │  │ tanggal_ditambahkan│  │   │  │ komentar           │  │   │
  │ alamat_pengiriman  │         │  └────────────────────┤  │   │  │ tanggal_ulasan     │  │   │
  │ no_resi            │         │                       │  │   │  └────────────────────┤  │   │
  │ tanggal_transaksi  │         │                       │  │   │                       │  │   │
  │ tanggal_dikirim    │         │                       │  │   │                       │  │   │
  │ kode_transaksi     │         │                       │  │   │                       │  │   │
  │ catatan_user       │         │                       │  │   │                       │  │   │
  │ bukti_pembayaran   │         │                       │  │   │                       │  │   │
  └──────────────────────────────┘                       │  │   │                       │  │   │
        │                                                │  │   │                       │  │   │
        │ (1:N)                                          │  │   │                       │  │   │
        ▼                                                │  │   │                       │  │   │
  ┌──────────────────────────────┐                      │  │   │                       │  │   │
  │  DETAIL_TRANSAKSI            │                      │  │   │                       │  │   │
  ├──────────────────────────────┤                      │  │   │                       │  │   │
  │ id_detail (PK)               │                      │  │   │                       │  │   │
  │ id_transaksi (FK) ──────────┐│                      │  │   │                       │  │   │
  │ id_produk (FK) ──────┬──────┘│                      │  │   │                       │  │   │
  │ jumlah              │       │                      │  │   │                       │  │   │
  │ harga_satuan        │       │                      │  │   │                       │  │   │
  │ subtotal            │       │                      │  │   │                       │  │   │
  └──────────────────────────────┘                      │  │   │                       │  │   │
                                   │                    │  │   │                       │  │   │
                                   └────────┬───────────┴──┴───┴───────────────────────┴──┘   │
                                            │                                              │
                                            └──────────────────────────────────────────────┘
                                                          │
                                                          │ (N:1)
                                                          │
  ┌──────────────────────────────────────────────────────▼──────────────────────────────────┐
  │              PRODUK                                                                     │
  ├──────────────────────────────────────────────────────────────────────────────────────────┤
  │ id_produk (PK)                                                                           │
  │ nama_produk                                                                              │
  │ merek                                                                                    │
  │ deskripsi                                                                                │
  │ spesifikasi                                                                              │
  │ harga                                                                                    │
  │ stok                                                                                     │
  │ gambar                                                                                   │
  │ kategori                                                                                 │
  │ status_produk (Tersedia/Habis)                                                           │
  │ tanggal_ditambahkan                                                                      │
  └──────────────────────────────────────────────────────────────────────────────────────────┘


  ┏━━━━━━━━━━━━━━━━━━━━━┓
  ┃      PROMO          ┃
  ┣━━━━━━━━━━━━━━━━━━━━━┫
  ┃ id_promo (PK)       ┃
  ┃ nama_promo          ┃
  ┃ jenis_promo         ┃
  ┃ nilai_diskon        ┃
  ┃ persentase_diskon   ┃
  ┃ tanggal_mulai       ┃
  ┃ tanggal_selesai     ┃
  ┃ status_promo        ┃
  ┃ deskripsi           ┃
  ┗━━━━━━━━━━━━━━━━━━━━━┛
```

---

## 📊 Table Relationships Summary

### 1️⃣ **One-to-Many (1:N) Relationships**

| Parent Table | Child Table | Foreign Key | Description |
|--------------|-------------|-------------|-------------|
| **USERS** | TRANSAKSI | id_user | One user has many transactions |
| **USERS** | KERANJANG | id_user | One user has many cart items |
| **USERS** | ULASAN | id_user | One user writes many reviews |
| **PRODUK** | DETAIL_TRANSAKSI | id_produk | One product appears in many transaction details |
| **PRODUK** | KERANJANG | id_produk | One product can be in many carts |
| **PRODUK** | ULASAN | id_produk | One product receives many reviews |
| **TRANSAKSI** | DETAIL_TRANSAKSI | id_transaksi | One transaction has many items |

### 2️⃣ **Standalone Tables**

| Table | Purpose | Relationships |
|-------|---------|---------------|
| **ADMIN** | Administrator accounts | No FK (independent) |
| **PROMO** | Promotions/discounts | Linked via business logic (not enforced FK) |

---

## 🔑 Primary & Foreign Keys

### Primary Keys (Unique Identifiers)
```sql
ADMIN:              id_admin (AUTO_INCREMENT)
USERS:              id_user (AUTO_INCREMENT)
PRODUK:             id_produk (AUTO_INCREMENT)
PROMO:              id_promo (AUTO_INCREMENT)
TRANSAKSI:          id_transaksi (AUTO_INCREMENT)
DETAIL_TRANSAKSI:   id_detail (AUTO_INCREMENT)
KERANJANG:          id_keranjang (AUTO_INCREMENT)
ULASAN:             id_ulasan (AUTO_INCREMENT)
```

### Foreign Keys (Referential Integrity)
```sql
TRANSAKSI.id_user → USERS.id_user
  ON DELETE: CASCADE (if user deleted, transactions deleted)
  ON UPDATE: CASCADE

DETAIL_TRANSAKSI.id_transaksi → TRANSAKSI.id_transaksi
  ON DELETE: CASCADE (if transaction deleted, details deleted)
  ON UPDATE: CASCADE

DETAIL_TRANSAKSI.id_produk → PRODUK.id_produk
  ON DELETE: RESTRICT (prevent product deletion if in transaction details)
  ON UPDATE: CASCADE

KERANJANG.id_user → USERS.id_user
  ON DELETE: CASCADE (if user deleted, cart items deleted)
  ON UPDATE: CASCADE

KERANJANG.id_produk → PRODUK.id_produk
  ON DELETE: CASCADE (if product deleted, cart items deleted)
  ON UPDATE: CASCADE

ULASAN.id_user → USERS.id_user
  ON DELETE: CASCADE (if user deleted, reviews deleted)
  ON UPDATE: CASCADE

ULASAN.id_produk → PRODUK.id_produk
  ON DELETE: CASCADE (if product deleted, reviews deleted)
  ON UPDATE: CASCADE
```

---

## 🔄 Data Flow Example

### Typical E-Commerce Transaction Flow

```
1. USER REGISTRATION
   └─ INSERT INTO users (username, password, email, ...)
      └─ id_user generated

2. BROWSE PRODUCTS
   └─ SELECT * FROM produk WHERE status_produk = 'Tersedia'

3. ADD TO CART
   └─ INSERT INTO keranjang (id_user, id_produk, jumlah)
      └─ Link: users.id_user → keranjang.id_user
      └─ Link: produk.id_produk → keranjang.id_produk

4. CHECKOUT (Create Transaction)
   └─ INSERT INTO transaksi (id_user, total_harga, ...)
      └─ Link: users.id_user → transaksi.id_user
      └─ id_transaksi generated

5. ADD TRANSACTION ITEMS
   └─ INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, ...)
      └─ Link: transaksi.id_transaksi → detail_transaksi.id_transaksi
      └─ Link: produk.id_produk → detail_transaksi.id_produk

6. UPDATE PRODUCT STOCK
   └─ UPDATE produk SET stok = stok - jumlah WHERE id_produk = ?

7. CLEAR CART
   └─ DELETE FROM keranjang WHERE id_user = ?

8. PRODUCT REVIEW
   └─ INSERT INTO ulasan (id_user, id_produk, rating, komentar)
      └─ Link: users.id_user → ulasan.id_user
      └─ Link: produk.id_produk → ulasan.id_produk
```

---

## 📋 Join Queries Examples

### Get All Transactions for a User
```sql
SELECT t.*, u.nama_lengkap, u.email
FROM transaksi t
JOIN users u ON t.id_user = u.id_user
WHERE u.username = 'user1';
```

### Get Transaction Details with Product Info
```sql
SELECT dt.*, p.nama_produk, p.harga, t.tanggal_transaksi
FROM detail_transaksi dt
JOIN produk p ON dt.id_produk = p.id_produk
JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
WHERE dt.id_transaksi = 1;
```

### Get Product Reviews
```sql
SELECT u.nama_lengkap, p.nama_produk, ul.rating, ul.komentar
FROM ulasan ul
JOIN users u ON ul.id_user = u.id_user
JOIN produk p ON ul.id_produk = p.id_produk
WHERE p.id_produk = 5
ORDER BY ul.rating DESC;
```

### Get User Cart Details
```sql
SELECT k.*, p.nama_produk, p.harga, (p.harga * k.jumlah) as total
FROM keranjang k
JOIN produk p ON k.id_produk = p.id_produk
WHERE k.id_user = 1;
```

---

## 🔒 Referential Integrity Rules

1. **Cannot delete user with active transactions** (RESTRICT via business logic)
2. **Cannot delete product if it's in active orders** (RESTRICT)
3. **Deleting transaction automatically deletes its details** (CASCADE)
4. **Deleting user removes all their data** (CASCADE)
5. **Updating IDs cascades to all related records** (CASCADE)

---

## 📈 Database Growth Projection

| Table | Rows (Month 1) | Rows (Month 6) | Rows (Year 1) |
|-------|----------------|----------------|---------------|
| users | 50 | 300 | 1,000 |
| produk | 13 | 50 | 200 |
| transaksi | 10 | 200 | 1,500 |
| detail_transaksi | 25 | 500 | 4,000 |
| keranjang | 5 | 80 | 300 |
| ulasan | 3 | 100 | 500 |
| **Total Rows** | **106** | **1,230** | **7,500** |

---

**Created:** December 30, 2025  
**Database Version:** 1.0  
**Status:** Ready for Integration with MobileNest Application

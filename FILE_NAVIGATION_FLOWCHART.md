# MobileNest - File Navigation & Interaction Flowchart

**Document Type:** Technical Reference  
**Date:** December 31, 2025  
**Purpose:** Visual guide untuk memahami navigasi dan interaksi antar file

---

## Table of Contents

1. [Entry Points](#entry-points)
2. [Complete User Journey](#complete-user-journey)
3. [Admin Journey](#admin-journey)
4. [API Interaction Flows](#api-interaction-flows)
5. [File Include Chain](#file-include-chain)
6. [Database Query Flow](#database-query-flow)

---

## 1. Entry Points

### Primary Entry Points

```
┌──────────────────────────────────────────────────────┐
│                  MOBILENEST APPLICATION                      │
└──────────────────────────────────────────────────────┘
                           │
          ┌────────────────┼────────────────┐
          │                │                │
          ↓                ↓                ↓
   ┌──────────┐  ┌──────────┐  ┌──────────┐
   │  PUBLIC  │  │  ADMIN   │  │   API    │
   │  PORTAL  │  │  PORTAL  │  │ ENDPOINTS│
   └──────────┘  └──────────┘  └──────────┘
       │              │              │
   index.php    admin/index.php   api/*.php
```

### Entry Point Details

| Entry Point | URL | Purpose | Auth Required |
|------------|-----|---------|---------------|
| **index.php** | `/MobileNest/` | Homepage | No |
| **admin/index.php** | `/MobileNest/admin/` | Admin login | No |
| **admin/dashboard.php** | `/MobileNest/admin/dashboard.php` | Admin panel | Yes (admin) |
| **user/login.php** | `/MobileNest/user/login.php` | User login | No |
| **api/*.php** | `/MobileNest/api/*.php` | API endpoints | Varies |

---

## 2. Complete User Journey

### 2.1 Guest User (Browse & Purchase)

```
┌─────────────────────────────────────────────────────────────┐
│                     GUEST USER JOURNEY                            │
└─────────────────────────────────────────────────────────────┘

1. LANDING
   ┌──────────────────────────────────────────────────┐
   │ FILE: index.php                                    │
   │ INCLUDES: config.php, header.php, footer.php       │
   │ QUERY: SELECT * FROM produk LIMIT 4               │
   │ DISPLAYS: Hero section + Featured products        │
   └──────────────────────────────────────────────────┘
         │
         │ (User clicks "Belanja Sekarang" or product)
         ↓

2. BROWSE PRODUCTS
   ┌──────────────────────────────────────────────────┐
   │ FILE: produk/list-produk.php                       │
   │ INCLUDES: config.php, header.php, footer.php       │
   │ FEATURES:                                          │
   │  - Search by keyword                               │
   │  - Filter by category                              │
   │  - Sort by price/name                              │
   │  - Pagination                                      │
   │ QUERY: SELECT * FROM produk WHERE ...             │
   └──────────────────────────────────────────────────┘
         │
         │ (User clicks specific product)
         ↓

3. VIEW PRODUCT DETAIL
   ┌──────────────────────────────────────────────────┐
   │ FILE: produk/detail-produk.php                     │
   │ PARAMS: ?id={id_produk}                            │
   │ INCLUDES: config.php, header.php, footer.php       │
   │ QUERIES:                                           │
   │  1. SELECT * FROM produk WHERE id_produk = ?       │
   │  2. SELECT * FROM ulasan WHERE id_produk = ?       │
   │  3. SELECT * FROM produk (related products)        │
   │ FEATURES:                                          │
   │  - Product images gallery                          │
   │  - Product specifications                          │
   │  - Customer reviews                                │
   │  - Add to cart button                              │
   │  - Related products                                │
   └──────────────────────────────────────────────────┘
         │
         │ (User clicks "Tambah ke Keranjang")
         │
         │ ┌───────────────────────────────────────┐
         │ │ CHECK: Is user logged in?            │
         │ │  └── YES → Add to cart (Session/DB)  │
         │ │  └── NO  → Redirect to login         │
         │ └───────────────────────────────────────┘
         ↓

4. LOGIN/REGISTER
   ┌──────────────────────────────────────────────────┐
   │ FILE: user/login.php                               │
   │ OR: user/register.php                              │
   │                                                    │
   │ LOGIN FLOW:                                        │
   │   user/login.php (form display)                    │
   │        ↓                                          │
   │   user/proses-login.php (form processing)          │
   │        ↓                                          │
   │   - Verify credentials                             │
   │   - Set session $_SESSION['user']                  │
   │   - Redirect to previous page or index             │
   │                                                    │
   │ REGISTER FLOW:                                     │
   │   user/register.php (form display)                 │
   │        ↓                                          │
   │   user/proses-register.php (form processing)       │
   │        ↓                                          │
   │   - Validate input                                 │
   │   - Check duplicate username/email                 │
   │   - Insert to users table                          │
   │   - Auto-login & redirect                          │
   └──────────────────────────────────────────────────┘
         │
         │ (After successful login)
         ↓

5. VIEW CART
   ┌──────────────────────────────────────────────────┐
   │ FILE: transaksi/keranjang.php                      │
   │ AUTH: Required (user must be logged in)            │
   │ INCLUDES: includes/auth-check.php                  │
   │                                                    │
   │ QUERY:                                             │
   │   SELECT k.*, p.nama_produk, p.harga, p.gambar     │
   │   FROM keranjang k                                 │
   │   JOIN produk p ON k.id_produk = p.id_produk       │
   │   WHERE k.id_user = ?                              │
   │                                                    │
   │ FEATURES:                                          │
   │  - Display cart items                              │
   │  - Update quantity (AJAX)                          │
   │  - Remove items                                    │
   │  - Calculate subtotal                              │
   │  - Show applicable promos                          │
   │  - Proceed to checkout button                      │
   └──────────────────────────────────────────────────┘
         │
         │ (User clicks "Lanjutkan ke Checkout")
         ↓

6. CHECKOUT
   ┌──────────────────────────────────────────────────┐
   │ FILE: transaksi/checkout.php                       │
   │ AUTH: Required                                     │
   │                                                    │
   │ DISPLAYS:                                          │
   │  - Order summary                                   │
   │  - Shipping address form                           │
   │  - Payment method selection                        │
   │  - Promo code input                                │
   │  - Total calculation                               │
   └──────────────────────────────────────────────────┘
         │
         │ (User submits order)
         ↓

7. PROCESS ORDER
   ┌──────────────────────────────────────────────────┐
   │ FILE: transaksi/proses-checkout.php                │
   │ METHOD: POST                                       │
   │                                                    │
   │ PROCESS:                                           │
   │  1. Begin transaction                              │
   │  2. Insert to transaksi table                      │
   │  3. Insert to detail_transaksi (cart items)        │
   │  4. Update product stock                           │
   │  5. Clear cart                                     │
   │  6. Commit transaction                             │
   │  7. Send email confirmation (optional)             │
   │  8. Redirect to success page                       │
   └──────────────────────────────────────────────────┘
         │
         ↓

8. ORDER CONFIRMATION
   ┌──────────────────────────────────────────────────┐
   │ FILE: user/pesanan.php                             │
   │ AUTH: Required                                     │
   │                                                    │
   │ DISPLAYS:                                          │
   │  - Order number                                    │
   │  - Order status                                    │
   │  - Order items                                     │
   │  - Tracking information (if available)             │
   │  - Payment instructions                            │
   │  - Option to add review (after received)           │
   └──────────────────────────────────────────────────┘
```

---

## 3. Admin Journey

```
┌─────────────────────────────────────────────────────────────┐
│                      ADMIN JOURNEY                             │
└─────────────────────────────────────────────────────────────┘

1. ADMIN LOGIN
   ┌──────────────────────────────────────────────────┐
   │ FILE: admin/index.php                              │
   │ VERIFY: username + password from admin table       │
   │ SESSION: $_SESSION['admin'] = admin_id            │
   │ REDIRECT: admin/dashboard.php                      │
   └──────────────────────────────────────────────────┘
         │
         ↓

2. DASHBOARD
   ┌──────────────────────────────────────────────────┐
   │ FILE: admin/dashboard.php                          │
   │ AUTH CHECK: require admin session                  │
   │                                                    │
   │ STATISTICS:                                        │
   │  - Total products                                  │
   │  - Total transactions                              │
   │  - Total revenue                                   │
   │  - Pending orders                                  │
   │  - Recent transactions (last 10)                   │
   │  - Low stock alerts                                │
   │  - Sales chart                                     │
   └──────────────────────────────────────────────────┘
         │
         ├─────────────────────────────────────────────┐
         │                                               │
         ↓                                               ↓
   MANAGE PRODUCTS                          MANAGE TRANSACTIONS
   kelola-produk.php                        kelola-transaksi.php
```

### Admin Menu Structure

```
admin/dashboard.php
    │
    ├──→ admin/kelola-produk.php
    │       │
    │       ├── Add new product
    │       ├── Edit product
    │       ├── Delete product
    │       └── Manage stock
    │
    ├──→ admin/kelola-transaksi.php
    │       │
    │       ├── View all transactions
    │       ├── Update status
    │       ├── View details
    │       └── Process refund
    │
    ├──→ admin/kelola-pesanan.php
    │       │
    │       ├── Pending orders
    │       ├── Processing orders
    │       └── Completed orders
    │
    └──→ admin/laporan.php
            │
            ├── Sales report
            ├── Product report
            ├── Customer report
            └── Export to PDF/Excel
```

---

## 4. API Interaction Flows

### 4.1 Products API

```
api/products.php
    │
    ├── GET ?action=list
    │       └── Response: JSON array of products
    │
    ├── GET ?action=get&id=X
    │       └── Response: JSON single product
    │
    ├── POST ?action=search
    │       ├── Body: {keyword, category, price_min, price_max}
    │       └── Response: JSON filtered products
    │
    └── GET ?action=categories
            └── Response: JSON list of categories
```

### 4.2 Cart API

```
api/cart.php
    │
    ├── GET ?action=get
    │       └── Response: JSON cart items with totals
    │
    ├── POST ?action=add
    │       ├── Body: {id_produk, jumlah}
    │       └── Response: {success, message, cart_count}
    │
    ├── PUT ?action=update
    │       ├── Body: {id_keranjang, jumlah}
    │       └── Response: {success, new_subtotal}
    │
    └── DELETE ?action=remove
            ├── Body: {id_keranjang}
            └── Response: {success, cart_count}
```

### 4.3 Reviews API

```
api/reviews.php
    │
    ├── GET ?action=list&product_id=X
    │       └── Response: JSON array of reviews
    │
    ├── POST ?action=create
    │       ├── Body: {id_produk, rating, komentar}
    │       └── Response: {success, review_id}
    │
    └── GET ?action=stats&product_id=X
            └── Response: {avg_rating, total_reviews}
```

---

## 5. File Include Chain

### Typical Page Load Sequence

```
1. PAGE FILE (e.g., index.php)
   │
   ├──→ require_once 'config.php'
   │       │
   │       ├── Database connection ($conn)
   │       ├── Session start
   │       ├── Constants (SITE_URL, etc.)
   │       └── Helper functions
   │
   ├──→ include 'includes/header.php'
   │       │
   │       ├── DOCTYPE & HTML start
   │       ├── Meta tags
   │       ├── CSS links
   │       ├── Navigation bar
   │       └── User session display
   │
   ├──→ PAGE CONTENT (HTML + PHP logic)
   │       │
   │       ├── Database queries
   │       ├── Data processing
   │       └── HTML rendering
   │
   └──→ include 'includes/footer.php'
           │
           ├── Footer HTML
           ├── JavaScript includes
           └── HTML close tags
```

### Protected Page Include Chain

```
PROTECTED PAGE (e.g., admin/dashboard.php)
   │
   ├──→ require_once '../config.php'
   │
   ├──→ require_once '../includes/auth-check.php'
   │       │
   │       ├── Check if $_SESSION['admin'] exists
   │       │   └── YES: continue
   │       │   └── NO: redirect to login
   │       │
   │       └── Check permissions if needed
   │
   ├──→ include 'includes/admin-header.php'
   │
   ├──→ PAGE CONTENT
   │
   └──→ include 'includes/admin-footer.php'
```

---

## 6. Database Query Flow

### Example: Product Detail Page

```
USER REQUEST: /produk/detail-produk.php?id=5
    │
    ↓
┌──────────────────────────────────────────────────┐
│ QUERY 1: Get Product Details                        │
│                                                    │
│ SELECT * FROM produk WHERE id_produk = 5           │
│                                                    │
│ Result: Product data (name, price, description...)  │
└──────────────────────────────────────────────────┘
    │
    ↓
┌──────────────────────────────────────────────────┐
│ QUERY 2: Get Reviews                                 │
│                                                    │
│ SELECT u.*, r.rating, r.komentar, r.tanggal        │
│ FROM ulasan r                                      │
│ JOIN users u ON r.id_user = u.id_user              │
│ WHERE r.id_produk = 5                              │
│ ORDER BY r.tanggal DESC                            │
│                                                    │
│ Result: Array of reviews with user info            │
└──────────────────────────────────────────────────┘
    │
    ↓
┌──────────────────────────────────────────────────┐
│ QUERY 3: Get Related Products                       │
│                                                    │
│ SELECT * FROM produk                               │
│ WHERE kategori = (SELECT kategori                  │
│                   FROM produk WHERE id_produk = 5) │
│   AND id_produk != 5                               │
│ LIMIT 4                                            │
│                                                    │
│ Result: 4 similar products                         │
└──────────────────────────────────────────────────┘
    │
    ↓
┌──────────────────────────────────────────────────┐
│ RENDER PAGE                                        │
│                                                    │
│ - Display product info                             │
│ - Display reviews                                  │
│ - Display related products                         │
│ - Show "Add to Cart" button                        │
└──────────────────────────────────────────────────┘
```

---

## Summary

Dokumen ini memberikan **visual guide lengkap** untuk memahami:

1. ✅ Entry points aplikasi
2. ✅ User journey dari landing sampai checkout
3. ✅ Admin flow dan menu structure
4. ✅ API endpoints dan interaction
5. ✅ File dependency chain
6. ✅ Database query flow

**Gunakan dokumen ini sebagai reference** saat:
- Menambah fitur baru
- Debugging navigation issues
- Understanding data flow
- Onboarding developer baru

---

**Related Documents:**
- CODE_REVIEW_ARCHITECTURE.md - Architecture analysis
- DATABASE_SCHEMA.md - Database structure
- DATABASE_ERD.md - Entity relationships

**Last Updated:** December 31, 2025

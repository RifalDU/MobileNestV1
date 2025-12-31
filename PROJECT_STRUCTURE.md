# 📂 MobileNest Project Structure

**Last Updated:** 31 Desember 2025, 07:45 WIB  
**Version:** 1.0  

---

## 🎯 Overview

Project structure yang terorganisir dengan baik memastikan:
- ✅ Code maintainability
- ✅ Easy collaboration
- ✅ Clear separation of concerns
- ✅ Reusable components

---

## 📁 Directory Structure

```
MobileNest/
├── 📂 admin/                    # Admin panel (untuk administrator)
│   ├── dashboard.php            # Admin dashboard
│   ├── manage-products.php      # Manage produk
│   └── manage-users.php         # Manage users
│
├── 📂 api/                      # API endpoints
│   ├── get-brands.php           # Get brands (JSON)
│   ├── get-categories.php       # Get categories (JSON)
│   └── ...
│
├── 📂 assets/                   # Static assets
│   ├── 📂 css/                  # Stylesheets
│   │   ├── style.css            # Main styles
│   │   ├── bootstrap.min.css    # Bootstrap framework
│   │   └── ...
│   │
│   ├── 📂 js/                   # JavaScript files
│   │   ├── filter.js            # ⭐ Product filtering logic
│   │   ├── cart.js              # Shopping cart functions
│   │   ├── checkout.js          # Checkout logic
│   │   ├── api-handler.js       # API communication
│   │   ├── script.js            # Global scripts
│   │   └── ...
│   │
│   └── 📂 images/               # Images & icons
│       ├── logo.png
│       ├── banner.jpg
│       └── ...
│
├── 📂 includes/                 # Reusable PHP includes
│   ├── header.php               # Page header/navbar
│   ├── footer.php               # Page footer
│   ├── brand-logos.php          # Brand logo data & functions
│   ├── db-functions.php         # Database helper functions
│   └── ...
│
├── 📂 produk/                   # 🔑 Product pages
│   ├── list-produk.php          # ⭐ Product listing with filter
│   ├── detail-produk.php        # Product detail page
│   ├── cari-produk.php          # Product search page
│   └── get-produk.php           # ⭐ API endpoint untuk filter
│
├── 📂 transaksi/                # Transaction/Cart pages
│   ├── keranjang.php            # Shopping cart page
│   ├── keranjang-aksi.php       # Cart actions (add/remove)
│   ├── checkout.php             # Checkout page
│   └── ...
│
├── 📂 user/                     # User management
│   ├── login.php                # Login page
│   ├── register.php             # Register page
│   ├── profile.php              # User profile
│   └── ...
│
├── 📄 index.php                 # 🏠 Homepage
├── 📄 config.php                # Database configuration
├── 📄 .htaccess                 # URL rewriting (if using)
└── ...
```

---

## 🔑 Key Files Explanation

### Core Files

#### `config.php`
- **Purpose:** Database connection & configuration
- **Contains:** DB credentials, constants, connection setup
- **Never commit:** Sensitive info (passwords)

#### `index.php`
- **Purpose:** Homepage
- **Features:** Brand showcase, featured products, banner

#### `includes/header.php`
- **Purpose:** Navigation bar & page header
- **Included in:** Every page

#### `includes/footer.php`
- **Purpose:** Footer content
- **Included in:** Every page

#### `includes/brand-logos.php`
- **Purpose:** Brand logo data & helper functions
- **Functions:**
  - `get_all_brands()` - Get list of all brands
  - `get_brand_logo_data($brand)` - Get logo URL for a brand

---

### 🔥 Main Feature Files

#### `produk/list-produk.php` ⭐
- **Purpose:** Product listing page with filters
- **Features:**
  - Brand filter with logos
  - Price range filter
  - Real-time search
  - Sorting options
  - Responsive grid layout
- **Dependencies:**
  - `assets/js/filter.js` - Filter logic
  - `produk/get-produk.php` - API endpoint
  - `includes/brand-logos.php` - Brand data

#### `produk/get-produk.php` ⭐
- **Purpose:** API endpoint for product filtering
- **Returns:** JSON array of products
- **Query Parameters:**
  - `brand` - Filter by brand(s)
  - `min_price` - Minimum price
  - `max_price` - Maximum price
  - `search` - Search query
  - `sort` - Sort option (terbaru, harga_rendah, harga_tinggi, populer)

**Example URLs:**
```
GET /produk/get-produk.php
GET /produk/get-produk.php?brand=Samsung
GET /produk/get-produk.php?min_price=3000000&max_price=7000000
GET /produk/get-produk.php?search=Samsung+S20&sort=harga_rendah
```

#### `produk/detail-produk.php`
- **Purpose:** Individual product detail page
- **Parameters:** `?id=<product_id>`
- **Features:** Full product info, images, reviews, related products

#### `assets/js/filter.js` ⭐
- **Purpose:** Handle all filtering logic
- **Key Functions:**
  - `applyFilter()` - Apply selected filters
  - `resetFilter()` - Clear all filters
  - `renderProducts(products)` - Render product cards
  - `getSelectedFilters()` - Get current filter state

**Features:**
- Fetch from API with filter params
- Client-side rendering
- Real-time search
- Sorting
- Error handling
- Notifications

#### `assets/js/cart.js`
- **Purpose:** Shopping cart functionality
- **Features:** Add/remove items, update quantity, calculate total

---

## 📊 Data Flow

### Filter Flow Diagram

```
User Interface (list-produk.php)
         ↓
    [User selects filters]
         ↓
    [Clicks "Terapkan Filter"]
         ↓
    JavaScript (filter.js)
    - Collect filter data
    - Build API query string
         ↓
    API Request (get-produk.php)
    GET /produk/get-produk.php?brand=Samsung&min_price=3000000&max_price=7000000
         ↓
    PHP Backend (get-produk.php)
    - Parse query params
    - Build SQL query
    - Filter from database
    - Return JSON
         ↓
    JSON Response
    [products: [{id:1, name:'...', price:...}, ...]]
         ↓
    JavaScript (filter.js)
    - Parse JSON
    - Render product cards
         ↓
    Updated UI (product grid)
```

---

## 📝 File Naming Conventions

### PHP Files
- **Pages:** `page-name.php` (kebab-case)
- **API endpoints:** `get-something.php`, `add-something.php`
- **Includes:** `included-file.php` (kebab-case)

### JavaScript Files
- **Feature files:** `feature-name.js` (kebab-case)
- **Library files:** `library.min.js` (minified if possible)

### CSS Files
- **Main styles:** `style.css`
- **Component styles:** `component-name.css`
- **Framework:** `framework.min.css` (minified)

### Image Files
- **Format:** `.png`, `.jpg`, `.svg` (prefer SVG for logos)
- **Naming:** `descriptive-name.ext` (lowercase, kebab-case)

---

## 🔄 Workflow Example: Adding New Product Feature

### Step 1: Create Backend API
```php
// File: produk/get-new-feature.php
<?php
header('Content-Type: application/json');
require_once '../config.php';

// Get parameters
$param = $_GET['param'] ?? '';

// Build query
// Return JSON
echo json_encode($results);
?>
```

### Step 2: Create Frontend Page
```php
// File: produk/new-feature.php
<?php
require_once '../config.php';
include '../includes/header.php';
?>
<div class="container">
    <!-- HTML content -->
</div>
<?php include '../includes/footer.php'; ?>
```

### Step 3: Create JavaScript Handler
```javascript
// File: assets/js/new-feature.js
function handleNewFeature() {
    // Fetch from API
    // Update UI
}
```

### Step 4: Include in Page
```php
<script src="../assets/js/new-feature.js"></script>
```

---

## ✅ Best Practices

### ✓ DO
- ✅ Keep files organized in their respective folders
- ✅ Use meaningful file names
- ✅ Separate concerns (logic in JS, styling in CSS, etc.)
- ✅ Reuse code (use includes/functions)
- ✅ Use API endpoints for data fetching
- ✅ Comment complex functions
- ✅ Follow naming conventions

### ✗ DON'T
- ❌ Don't mix HTML, CSS, and JS in one file
- ❌ Don't create new folders unnecessarily
- ❌ Don't put logic in views
- ❌ Don't hardcode database queries
- ❌ Don't commit sensitive files (config.php with credentials)

---

## 📚 Related Documentation

- `FILTER_ACTIVATION_GUIDE.md` - How filter works
- `DEBUG_LOGO_ONLY_APPLE.md` - Logo debugging
- `BUGFIX_LOG.md` - Previous fixes
- `UPDATE_SUMMARY.md` - Recent changes

---

## 🔍 Quick Reference

### Where to find:
- **Styling?** → `assets/css/`
- **JavaScript?** → `assets/js/`
- **Product pages?** → `produk/`
- **User features?** → `user/`
- **Admin features?** → `admin/`
- **API endpoints?** → `produk/` or `api/`
- **Reusable code?** → `includes/`

### How to add:
- **New page:** Create in appropriate folder + include header/footer
- **New API?** Create in `produk/` or `api/` → return JSON
- **New style?** Add to `assets/css/` → include in page
- **New script?** Add to `assets/js/` → include at bottom of page
- **New component?** Add to `includes/` → require_once in pages

---

## 📞 Support

For questions about project structure:
1. Check this file first
2. Look at `FILTER_ACTIVATION_GUIDE.md` for filter-specific info
3. Check commit history for changes
4. Review existing similar files as examples

---

**Status:** ✅ COMPLETE  
**Last Updated:** 31 Desember 2025, 07:45 WIB  
**Version:** 1.0

# 🚀 Quick Start Guide - Filter & Product Listing

**Last Updated:** 31 Desember 2025, 07:45 WIB  
**Status:** ✅ READY TO USE

---

## 👋 Getting Started

### Prerequisites
- ✅ XAMPP or similar local server
- ✅ MySQL database set up with MobileNest schema
- ✅ Browser (Chrome, Firefox, Safari, Edge)

### Setup

```bash
# 1. Clone repository
git clone https://github.com/RifalDU/MobileNestV1.git

# 2. Place in htdocs
cp -r MobileNestV1 /path/to/htdocs/

# 3. Start XAMPP
# - Start Apache
# - Start MySQL

# 4. Import database
# - Open phpMyAdmin
# - Import MobileNest database (if exists)

# 5. Configure database
# - Edit: MobileNest/config.php
# - Set DB credentials

# 6. Access website
# http://localhost/MobileNestV1/MobileNest/
```

---

## 🔘 Using Product Filter

### Access Filter Page
```
URL: http://localhost/MobileNestV1/MobileNest/produk/list-produk.php
```

### Filter Features

#### 1. Search Products
```
1. Type "Samsung" di search box
2. Products auto-filter as you type
3. No button click needed
```

#### 2. Filter by Brand
```
1. Check brand checkboxes (with logo)
   - Apple
   - Samsung
   - Xiaomi
   - OPPO
   - Vivo
   - Realme
2. Click "Terapkan Filter"
3. Only selected brand products shown
```

#### 4. Filter by Price
```
1. Check price range checkboxes
   - Rp 1 - 3 Juta
   - Rp 3 - 7 Juta
   - Rp 7 - 15 Juta
   - Rp 15+ Juta
2. Click "Terapkan Filter"
3. Products filtered by price
```

#### 5. Combine Multiple Filters
```
1. Select Brand: Samsung
2. Select Price: Rp 3 - 7 Juta
3. Click "Terapkan Filter"
4. Result: Samsung products in 3-7M range
```

#### 6. Sort Products
```
1. Select from dropdown:
   - Terbaru (default)
   - Harga Terendah
   - Harga Tertinggi
   - Paling Populer
2. Products auto-sort
```

#### 7. Reset Filters
```
1. Click "Reset Filter" button
2. All filters cleared
3. All products shown
```

---

## 🔐 How It Works

### Architecture

```
[User Interface]
  list-produk.php
       ↓
[JavaScript]
  assets/js/filter.js
  - Collect filters
  - Call API
       ↓
[Backend API]
  produk/get-produk.php
  - Query database
  - Return JSON
       ↓
[Results]
  JavaScript renders products
  Display in grid
```

### File Roles

**Frontend:**
- `produk/list-produk.php` - HTML structure with filters
- `assets/js/filter.js` - All filter logic & rendering
- `assets/css/style.css` - Styling

**Backend:**
- `produk/get-produk.php` - API endpoint
- `config.php` - Database connection
- `includes/brand-logos.php` - Brand data

---

## 🐍 Troubleshooting

### Problem: Products Not Loading

**Solution:**
```
1. Open browser DevTools (F12)
2. Go to Console tab
3. Check for JavaScript errors
4. Look for failed API calls
5. Check Network tab
6. Verify /produk/get-produk.php returns JSON
```

**Common Causes:**
- Database not connected
- Wrong path to get-produk.php
- PHP errors in API

### Problem: Filter Not Working

**Solution:**
```
1. Check filter.js is loaded (Network tab)
2. Open Console
3. Type: applyFilter()
4. Should fetch products
5. If error, check file permissions
```

### Problem: Logos Not Showing

**Solution:**
```
1. Check Network tab for image requests
2. Verify CDN URLs in brand-logos.php
3. Test URLs manually in browser
4. Check for 404 errors
```

### Problem: Slow Filter Response

**Solution:**
```
1. Check database indexes
2. Monitor MySQL queries
3. Add pagination if too many products
4. Cache frequently used results
```

---

## 📃 Common Tasks

### Add New Brand

1. **Update Database**
   ```sql
   UPDATE produk SET merek = 'NewBrand' WHERE ...;
   ```

2. **Add Logo to `includes/brand-logos.php`**
   ```php
   'NewBrand' => [
       'image_url' => 'https://cdn.example.com/logo.png',
       'color' => '#FF5733'
   ]
   ```

3. **Test in Filter**
   - Reload page
   - Should appear in filter

### Add New Price Range

1. **Update `produk/list-produk.php`**
   ```html
   <input class="form-check-input price-checkbox" 
          type="checkbox" 
          value="1000000:2000000" 
          id="harga_new" />
   <label for="harga_new">Rp 1 - 2 Juta</label>
   ```

2. **Test Filter**
   - Page auto-recognizes new checkbox
   - Filter works automatically

### Modify Filter Behavior

1. **Update `assets/js/filter.js`**
   - Change sorting logic
   - Modify filter parameters
   - Update rendering

2. **Update `produk/get-produk.php`**
   - Change API logic
   - Add new filters
   - Optimize queries

---

## 📎 API Reference

### GET /produk/get-produk.php

**Parameters:**

| Param | Type | Example | Required |
|-------|------|---------|----------|
| brand | string | Samsung,Xiaomi | No |
| min_price | integer | 3000000 | No |
| max_price | integer | 7000000 | No |
| search | string | Samsung S20 | No |
| sort | string | harga_rendah | No |

**Response:** JSON array of products

**Examples:**

```
# All products
GET /produk/get-produk.php

# By brand
GET /produk/get-produk.php?brand=Samsung

# Multiple brands
GET /produk/get-produk.php?brand=Samsung,Xiaomi

# By price
GET /produk/get-produk.php?min_price=3000000&max_price=7000000

# Search
GET /produk/get-produk.php?search=Samsung

# Sort
GET /produk/get-produk.php?sort=harga_rendah

# Combined
GET /produk/get-produk.php?brand=Samsung&min_price=3000000&sort=harga_rendah
```

**Response Format:**
```json
[
  {
    "id_produk": 1,
    "nama_produk": "Samsung Galaxy S20",
    "merek": "Samsung",
    "harga": 5000000,
    "stok": 10,
    "kategori": "Flagship",
    "gambar": "https://...",
    "deskripsi": "...",
    "rating": 4.5,
    "terjual": 150
  },
  ...
]
```

---

## 📚 Documentation Links

- **Project Structure:** `PROJECT_STRUCTURE.md`
- **Filter Details:** `FILTER_ACTIVATION_GUIDE.md`
- **Debugging:** `DEBUG_LOGO_ONLY_APPLE.md`
- **Bug Fixes:** `BUGFIX_LOG.md`
- **Recent Changes:** `UPDATE_SUMMARY.md`

---

## 💫 Tips & Tricks

### Performance
- Use keyboard shortcut: Ctrl+Click to select multiple brands
- Type while filtering to narrow results further
- Use sort dropdown to organize results

### Debugging
- Open DevTools (F12) to monitor API calls
- Check Console for any JavaScript errors
- Use Network tab to verify API responses
- Check MySQL error logs if data doesn't update

### Customization
- Edit colors in `assets/css/style.css`
- Modify filter options in HTML
- Add custom sorting in `filter.js`
- Change API logic in `get-produk.php`

---

## 🚆 What's Next

### Improvements to Consider
- ✅ Add pagination (show 20 products per page)
- ✅ Add filter tags/pills (show active filters)
- ✅ Add filter history (remember recent filters)
- ✅ Add comparison feature (compare products)
- ✅ Add wishlist functionality
- ✅ Add user reviews/ratings
- ✅ Add related products
- ✅ Add product recommendations

---

## 🗐️ Support

### Getting Help

1. **Check Documentation**
   - Project Structure
   - Filter Guide
   - Debugging Guide

2. **Run Tests**
   - Test each filter individually
   - Test combined filters
   - Test search
   - Test sorting

3. **Check Console**
   - F12 → Console
   - Look for error messages
   - Run diagnostic commands

4. **Review Code**
   - Check git history
   - Review recent changes
   - Look at similar implementations

---

**Status:** ✅ COMPLETE  
**Last Updated:** 31 Desember 2025, 07:45 WIB  
**Version:** 1.0

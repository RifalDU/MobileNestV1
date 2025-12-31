# 🔍 Debug: Hanya Logo Apple yang Muncul

**Date:** 31 Desember 2025, 07:40 WIB  
**Status:** 🔍 INVESTIGATING  

---

## 🧡 Problem Statement

Di halaman utama (index.php) atau list-produk.php, hanya logo **Apple** yang tampil. Logo lain (Samsung, Xiaomi, OPPO, Vivo, Realme) tidak ditampilkan.

**Expected:** 6 brand logos
**Actual:** 1 brand logo (Apple)

---

## 📄 Investigation Checklist

### 1. Verify CDN URLs Status

**Check file:** `MobileNest/includes/brand-logos.php`

```bash
# URLs yang sudah diperbaiki:
✅ Apple    : https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/800px-Apple_logo_black.svg.png
✅ Samsung  : https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Samsung_Logo.svg/800px-Samsung_Logo.svg.png
✅ Xiaomi   : https://upload.wikimedia.org/wikipedia/commons/thumb/b/b1/Xiaomi_logo.svg/800px-Xiaomi_logo.svg.png
✅ OPPO     : https://upload.wikimedia.org/wikipedia/commons/thumb/f/f6/OPPO_LOGO.svg/800px-OPPO_LOGO.svg.png
✅ Vivo     : https://upload.wikimedia.org/wikipedia/commons/thumb/8/80/Vivo_logo.svg/800px-Vivo_logo.svg.png
✅ Realme   : https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/Realme_logo.svg/800px-Realme_logo.svg.png
```

**To verify:** Open each URL di browser, pastikan semuanya bisa diakses

### 2. Check Browser Console

**Steps:**
1. Open index.php di browser
2. Press **F12** untuk buka DevTools
3. Go to **Console** tab
4. Check apakah ada **error messages** (warna merah)
5. Perhatikan khususnya errors tentang:
   - Image loading errors
   - 404 Not Found
   - CORS errors
   - JavaScript errors

**Screenshot command:**
```javascript
// Paste di console untuk test:
console.log(document.querySelectorAll('img[src*="wikimedia"]'));
// Akan list semua images dari CDN
```

### 3. Check Network Tab

**Steps:**
1. Open DevTools (F12)
2. Go to **Network** tab
3. Reload halaman (F5)
4. Filter: "Image" atau "Wikimedia"
5. Check status code untuk setiap image:
   - **200** = OK (berhasil dimuat)
   - **404** = Not Found
   - **403** = Forbidden
   - **0** = Blocked/CORS error

**Analysis:**
- Apple: 200 OK ✓
- Samsung: Status? 👉 Check here
- Xiaomi: Status? 👉 Check here
- OPPO: Status? 👉 Check here
- Vivo: Status? 👉 Check here
- Realme: Status? 👉 Check here

### 4. Check HTML Structure

**Steps:**
1. Open DevTools (F12)
2. Go to **Elements** tab
3. Find section dengan brand logos
4. Right-click → Inspect
5. Check apakah semua 6 `<img>` tags ada di HTML

**Expected HTML:**
```html
<div class="brand-container">
    <img src="...Apple..." alt="Apple"> <!-- Visible ✓ -->
    <img src="...Samsung..." alt="Samsung"> <!-- Missing? Visible? -->
    <img src="...Xiaomi..." alt="Xiaomi"> <!-- Missing? Visible? -->
    ...
</div>
```

**Questions:**
- Apakah semua 6 `<img>` tags ada di HTML?
- Atau hanya 1?
- Apakah src attribute terpenuhi?
- Apakah `display: none` di CSS?

### 5. Test Each Image URL Directly

**Paste di browser address bar:**

```
https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/800px-Apple_logo_black.svg.png
```

Ulangi untuk semua 6 brand. Semua harus menampilkan image, bukan error.

### 6. Check PHP Array

**Run di PHP environment:**

```php
<?php
include 'includes/brand-logos.php';

$brands = get_all_brands();
echo "<pre>";
print_r($brands);
echo "</pre>";

foreach ($brands as $brand) {
    $data = get_brand_logo_data($brand);
    echo "$brand: " . ($data ? 'OK' : 'MISSING') . "<br>";
}
?>
```

Jika ada brand yang return `FALSE` atau `NULL`, itu problem-nya.

---

## 🍐 Common Causes & Solutions

### Cause #1: CDN URL Typo or Incomplete

**Symptom:** Some logos load, some don't

**Check:**
```php
// In brand-logos.php
$brand_logos = [
    'Apple' => [
        'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/800px-Apple_logo_black.svg.png',
        ...
    ],
    'Samsung' => [
        'image_url' => 'https://...', // Check ini
        ...
    ],
];
```

**Fix:** Verify semua URLs complete dan correct

### Cause #2: Database `merek` Field Mismatch

**Symptom:** Filter shows all 6 brands, tapi logo hanya 1

**Check:**
```sql
-- Run di MySQL:
SELECT DISTINCT merek FROM produk;
```

**Expected output:**
```
Apple
Samsung
Xiaomi
OPPO
Vivo
Realme
```

**If different:**
Jika database punya "SAMSUNG" (uppercase) tapi array ada "Samsung", akan tidak match.

**Fix:** Standardize naming

### Cause #3: JavaScript Rendering Issue

**Symptom:** Semua image tags di HTML, tapi hanya 1 yang visible

**Check:**
```javascript
// Paste di console:
var imgs = document.querySelectorAll('img[alt*=""]');
for (var i = 0; i < imgs.length; i++) {
    console.log(imgs[i].alt + ': ' + window.getComputedStyle(imgs[i]).display);
}
```

Jika ada yang `display: none`, cari di CSS.

**Fix:** Remove CSS hiding rules

### Cause #4: CORS Blocked

**Symptom:** Console shows CORS error, images not loading

**Check:**
```
[CORS error] The value of the 'Access-Control-Allow-Origin' header 
in the response must not be the wildcard '*'
```

**Fix:** CDN sudah CORS-compatible, jadi tidak seharusnya issue

### Cause #5: Image File Renamed/Moved

**Symptom:** 404 Not Found for all except Apple

**Check:** Network tab status codes

**Fix:** Update URLs sesuai actual Wikimedia filenames

---

## 🔊 Diagnostic Commands

### Check 1: List all brand logos di halaman
```javascript
// Paste di console:
var images = document.querySelectorAll('img[src*="wikimedia"]');
console.log('Total logo images: ' + images.length);
for (var i = 0; i < images.length; i++) {
    console.log((i+1) + '. ' + images[i].alt + ' - ' + images[i].src);
}
```

### Check 2: Test API response
```javascript
// Paste di console:
fetch('../includes/brand-logos.php')
    .then(r => r.json())
    .then(d => console.log(d));
```

### Check 3: Inspect CSS for hidden elements
```javascript
// Paste di console:
var elements = document.querySelectorAll('[style*="display:none"], [style*="visibility:hidden"]');
console.log('Hidden elements: ' + elements.length);
```

### Check 4: PHP brand validation
```php
<?php
include 'MobileNest/includes/brand-logos.php';
echo json_encode(get_all_brands());
?>
```

---

## 🌟 Step-by-Step Debugging

### Step 1: Visual Inspection
```
1. Open index.php
2. Look at brand section
3. Count logos visible: ?
4. Are they arranged correctly?
```

### Step 2: Browser Console
```
1. F12 → Console
2. Run diagnostic commands above
3. Check untuk errors
4. Note any 404 or CORS errors
```

### Step 3: Network Analysis
```
1. F12 → Network
2. Reload page
3. Filter by "img"
4. Check status code untuk setiap logo
5. Identify yang 404
```

### Step 4: Database Check
```
1. Open phpMyAdmin
2. Run: SELECT DISTINCT merek FROM produk;
3. Compare dengan brand names di brand-logos.php
4. Check untuk spelling differences
```

### Step 5: Code Review
```
1. Check brand-logos.php
2. Verify semua URLs
3. Test URLs di browser
4. Check PHP functions (get_all_brands, get_brand_logo_data)
```

### Step 6: Fix & Verify
```
1. Apply fix berdasarkan diagnosis
2. Clear browser cache (Ctrl+Shift+R)
3. Reload page
4. Verify 6 logos muncul
```

---

## 📊 Diagnosis Worksheet

Silakan isi saat debugging:

```
[ ] Total logo images visible: ____ (harus 6)
[ ] Browser console errors: YES / NO
[ ] Network 404 errors: YES / NO
[ ] CDN URLs accessible: YES / NO
[ ] Database merek values: _______________
[ ] PHP functions working: YES / NO
[ ] CSS hiding rules: YES / NO

Probable Cause: ____________________________
Proposed Fix: ______________________________
```

---

## 🚀 Recovery Checklist

Jika sudah identify masalah:

- [ ] Fix URLs di brand-logos.php
- [ ] Update database merek values jika needed
- [ ] Clear browser cache
- [ ] Verify di 3 browser berbeda
- [ ] Check on mobile devices
- [ ] Test all 6 logos clickable
- [ ] Document root cause
- [ ] Add preventive measures

---

## 📑 Log Your Findings

```
Debug Date: _____________
Browser: ________________
URL: _____________________

Finding #1:
- Symptom: 
- Cause: 
- Solution: 

Finding #2:
- Symptom: 
- Cause: 
- Solution: 

Final Status: ❌ UNRESOLVED / 😇 PARTIALLY FIXED / ✅ RESOLVED
```

---

## 🔗 Related Files

- `MobileNest/includes/brand-logos.php` - Logo array & functions
- `MobileNest/index.php` - Homepage yang display logos
- `MobileNest/produk/list-produk.php` - Produk listing
- `BUGFIX_LOG.md` - Previous CDN URL fixes
- `FILTER_ACTIVATION_GUIDE.md` - Filter debugging

---

**Status:** 🔍 WAITING FOR DIAGNOSTIC DATA  
**Next Action:** Run diagnostic commands above and report findings
**Last Updated:** 31 Desember 2025, 07:41 WIB

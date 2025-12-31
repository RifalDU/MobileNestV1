# 📄 Quick Reference - Brand Logo Usage

**Panduan cepat untuk menggunakan brand logo di file-file MobileNest**

---

## ⚡ Quick Setup (3 Steps)

### Step 1: Include File

Di awal file PHP Anda, tambahkan:

```php
<?php
require_once 'includes/brand-logos.php'; // Sesuaikan path
?>
```

### Step 2: Gunakan Helper Function

Pilih salah satu:

```php
// A. Display logo HTML langsung
<?php echo get_brand_logo_html('Samsung'); ?>

// B. Display dengan custom styling
<?php echo get_brand_logo_html('Apple', ['style' => 'width: 80px; height: 80px;']); ?>

// C. Hanya ambil URL
<?php $url = get_brand_logo_url('Xiaomi'); ?>

// D. Ambil data lengkap
<?php 
$data = get_brand_logo_data('OPPO');
echo $data['image_url']; 
?>

// E. Loop semua brand
<?php foreach(get_all_brands() as $brand): ?>
    <?php echo get_brand_logo_html($brand); ?>
<?php endforeach; ?>
```

### Step 3: Done!

Logo akan otomatis ditampilkan dengan fallback jika tidak ditemukan.

---

## 📋 Available Functions

| Function | Return Type | Deskripsi |
|----------|-----------|----------|
| `get_brand_logo_url($brand)` | String (URL) | Ambil URL logo brand |
| `get_brand_logo_html($brand, $attrs)` | String (HTML) | Ambil HTML img tag |
| `get_brand_logo_data($brand)` | Array \| null | Ambil data lengkap |
| `get_all_brands()` | Array | Ambil semua brand |

---

## 📊 Examples

### 1. Simple Display

```php
<?php echo get_brand_logo_html('Samsung'); ?>
```

**Output:** `<img src="..." alt="Samsung Logo" class="brand-logo" style="width: 50px; height: 50px;">`

---

### 2. Custom Size & Class

```php
<?php 
echo get_brand_logo_html('iPhone', [
    'class' => 'brand-logo-large my-custom-class',
    'style' => 'width: 80px; height: 80px; border-radius: 5px;'
]); 
?>
```

---

### 3. In a Loop

```php
<div class="brand-grid">
    <?php foreach(get_all_brands() as $brand): ?>
        <div class="brand-item">
            <?php echo get_brand_logo_html($brand, ['class' => 'brand-logo-grid']); ?>
            <p><?php echo $brand; ?></p>
        </div>
    <?php endforeach; ?>
</div>
```

---

### 4. Conditional Display

```php
<?php 
$logo_data = get_brand_logo_data($product_brand);
if ($logo_data) {
    ?><img src="<?php echo $logo_data['image_url']; ?>" alt="<?php echo $logo_data['alt']; ?>"><?php
} else {
    ?><img src="/assets/placeholder.png" alt="Brand"><?php
}
?>
```

---

### 5. With Bootstrap Classes

```php
<div class="d-flex align-items-center gap-2">
    <?php echo get_brand_logo_html('Xiaomi', ['class' => 'img-fluid', 'style' => 'max-width: 40px;']); ?>
    <h5>Xiaomi Products</h5>
</div>
```

---

## 🔛 Available Brands

```
1. Apple
2. Samsung
3. Xiaomi
4. OPPO
5. Vivo
6. Realme
```

---

## 🔧 CSS Styling

### Default Class

```css
.brand-logo {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}
```

### Custom Classes

```css
.brand-logo-large { width: 80px; height: 80px; }
.brand-logo-small { width: 30px; height: 30px; }
.brand-logo-grid { width: 60px; height: 60px; }
```

---

## ⚠️ Important Notes

- ✅ Semua URL stabil dan di-cache CDN
- ✅ Fallback otomatis jika brand tidak ditemukan
- ✅ Responsive dan mobile-friendly
- ✅ Performance optimal
- ⚡ Require internet untuk load logo
- ⚡ Case-sensitive brand name ("Samsung" ≠ "samsung")

---

## 🚀 Common Mistakes to Avoid

❌ **SALAH:**
```php
<?php echo get_brand_logo_html('samsung'); ?> <!-- lowercase -->
<?php echo get_brand_logo_html('iPhone'); ?> <!-- Bukan 'Apple' -->
```

✅ **BENAR:**
```php
<?php echo get_brand_logo_html('Samsung'); ?>
<?php echo get_brand_logo_html('Apple'); ?>
```

---

## 📂 File References

- **File Konfigurasi:** `MobileNest/includes/brand-logos.php`
- **Dokumentasi Lengkap:** `BRAND_LOGOS_GUIDE.md`
- **Contoh Implementasi:**
  - `MobileNest/index.php` - Home page
  - `MobileNest/produk/list-produk.php` - Product listing
  - `MobileNest/produk/detail-produk.php` - Product detail

---

## 🔍 Debugging Tips

**Logo tidak tampil?**

1. Pastikan file `brand-logos.php` di-include
2. Check brand name (case-sensitive)
3. Check internet connection (CDN requirement)
4. Check browser console untuk error

**Ingin debug?**

```php
<?php
// Debug: lihat semua brand
echo '<pre>'; print_r(get_all_brands()); echo '</pre>';

// Debug: lihat data specific brand
echo '<pre>'; print_r(get_brand_logo_data('Samsung')); echo '</pre>';
?>
```

---

**Updated:** 31 Desember 2025  
**Version:** 1.0  
**Status:** 🚀 Ready to Use

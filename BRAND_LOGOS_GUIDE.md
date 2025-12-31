# Brand Logos Integration Guide

Panduan lengkap untuk menggunakan sistem logo brand HP di berbagai file aplikasi MobileNest.

## 📋 Daftar Brand yang Tersedia

Saat ini, aplikasi mendukung logo untuk 6 brand utama:

1. **Apple** - Logo resmi Apple Inc.
2. **Samsung** - Logo resmi Samsung
3. **Xiaomi** - Logo resmi Xiaomi
4. **OPPO** - Logo resmi OPPO
5. **Vivo** - Logo resmi Vivo
6. **Realme** - Logo resmi Realme

## 🔧 Setup Awal

### File Konfigurasi Brand Logos

File utama: `MobileNest/includes/brand-logos.php`

File ini berisi:
- Array `$brand_logos` dengan URL CDN untuk setiap brand
- Helper functions untuk mengakses logo
- Fallback/placeholder jika logo tidak ditemukan

## 📚 Cara Penggunaan

### 1. Memuat File Brand Logos

Di awal file PHP yang ingin menggunakan logo:

```php
require_once 'includes/brand-logos.php';
```

### 2. Menggunakan Helper Functions

#### A. Mengambil URL Logo

```php
// Mendapatkan URL logo brand
$logo_url = get_brand_logo_url('Samsung');

// Menggunakan dalam HTML
<img src="<?php echo $logo_url; ?>" alt="Samsung Logo">
```

#### B. Mendapatkan HTML Logo Langsung

```php
// Dengan atribut default
<?php echo get_brand_logo_html('Xiaomi'); ?>

// Dengan custom class dan style
<?php echo get_brand_logo_html('Realme', [
    'class' => 'brand-logo-large',
    'style' => 'width: 80px; height: 80px; margin: 10px;'
]); ?>
```

#### C. Mendapatkan Data Logo Lengkap

```php
// Mendapatkan array data (url, alt, image_url)
$logo_data = get_brand_logo_data('OPPO');

if ($logo_data) {
    echo '<img src="' . $logo_data['image_url'] . '" alt="' . $logo_data['alt'] . '">';
}
```

#### D. Mendapatkan Semua Brand

```php
// Untuk membuat dropdown atau list lengkap
$all_brands = get_all_brands();

foreach ($all_brands as $brand) {
    echo "<option>" . $brand . "</option>";
}
```

## 📝 Contoh Implementasi di Berbagai File

### Contoh 1: Di File Listing Produk (produk/list-produk.php)

```php
<?php
require_once '../config.php';
require_once '../includes/brand-logos.php';

// Dalam filter brand
$brands = get_all_brands();
?>

<div class="brand-filter">
    <h5>Pilih Brand:</h5>
    <?php foreach ($brands as $brand): ?>
        <label class="brand-option">
            <?php echo get_brand_logo_html($brand, ['style' => 'width: 40px; height: 40px;']); ?>
            <input type="radio" name="brand" value="<?php echo $brand; ?>">
            <span><?php echo $brand; ?></span>
        </label>
    <?php endforeach; ?>
</div>
```

### Contoh 2: Di File Admin (admin/dashboard.php)

```php
<?php
require_once '../config.php';
require_once '../includes/brand-logos.php';

// Menampilkan statistik per brand dengan logo
$brands = get_all_brands();
?>

<table class="table">
    <thead>
        <tr>
            <th>Brand</th>
            <th>Logo</th>
            <th>Jumlah Produk</th>
            <th>Total Penjualan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($brands as $brand): ?>
        <tr>
            <td><?php echo $brand; ?></td>
            <td><?php echo get_brand_logo_html($brand); ?></td>
            <td><?php // query count from DB ?></td>
            <td><?php // query sales from DB ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

### Contoh 3: Di Search/Filter Component

```php
<?php
// Search filter dengan brand logos
$selected_brand = $_GET['brand'] ?? '';
$all_brands = get_all_brands();
?>

<div class="search-filters">
    <div class="brand-carousel">
        <?php foreach ($all_brands as $brand): ?>
            <a href="?brand=<?php echo urlencode($brand); ?>" 
               class="brand-chip <?php echo ($selected_brand === $brand ? 'active' : ''); ?>">
                <?php echo get_brand_logo_html($brand, ['style' => 'width: 50px; height: 50px;']); ?>
                <span><?php echo $brand; ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>
```

### Contoh 4: Dalam Tabel Database

Jika produk di database memiliki kolom `brand`, Anda bisa menggunakan:

```php
<?php
while ($row = mysqli_fetch_assoc($result)) {
    $logo_html = get_brand_logo_html($row['brand'], ['style' => 'width: 30px; height: 30px;']);
    echo "<td>" . $logo_html . $row['brand'] . "</td>";
}
?>
```

## 🎨 CSS Classes untuk Styling

Default class yang diterapkan pada img tag adalah `brand-logo`. Anda bisa menambahkan CSS di style Anda:

```css
.brand-logo {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

.brand-logo-large {
    width: 80px;
    height: 80px;
}

.brand-logo-small {
    width: 30px;
    height: 30px;
}
```

## 🔄 CDN URLs yang Digunakan

Semua logo diambil dari sumber yang reliable:

- **Wikimedia Commons** - Logo resmi brand (https://upload.wikimedia.org/)
- **jsdelivr** - Flag icons untuk negara asal brand (https://cdn.jsdelivr.net/)

## 📌 Best Practices

1. **Selalu gunakan helper functions** - Jangan hardcode URL
2. **Cek apakah brand tersedia** - Gunakan `get_brand_logo_data()` sebelum display
3. **Tambahkan fallback images** - Placeholder otomatis tersedia jika brand tidak ditemukan
4. **Optimasi sizing** - Gunakan CSS untuk responsive logo sizing
5. **Caching** - CDN url sudah di-cache browser, performa tidak masalah

## 🛠️ Menambah Brand Baru

Jika perlu menambah brand baru, edit file `MobileNest/includes/brand-logos.php`:

```php
$brand_logos = [
    // Brand existing...
    'Samsung' => [...],
    
    // Tambah brand baru
    'OnePlus' => [
        'url' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.11.0/flags/4x3/cn.svg',
        'alt' => 'OnePlus Logo',
        'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e2/OnePlus_logo.svg/220px-OnePlus_logo.svg.png'
    ],
];
```

Lalu gunakan seperti biasa:
```php
get_brand_logo_html('OnePlus');
```

## 📂 File yang Sudah Diupdate

- ✅ `MobileNest/includes/brand-logos.php` - Baru dibuat
- ✅ `MobileNest/index.php` - Sudah diupdate dengan brand logos

## 📝 File yang Bisa Diupdate Selanjutnya

- `MobileNest/produk/list-produk.php` - Untuk filter brand
- `MobileNest/admin/dashboard.php` - Untuk statistik per brand
- `MobileNest/user/wishlist.php` - Untuk tampilan wishlist
- Semua file di folder `MobileNest/` yang menampilkan daftar brand

## ❓ Troubleshooting

**Q: Logo tidak tampil?**
A: Pastikan:
- File `brand-logos.php` di-include dengan benar
- Nama brand sesuai (case-sensitive)
- CDN dapat diakses (cek internet connection)

**Q: Ingin ganti logo CDN?**
A: Edit URL di array `$brand_logos` di file `brand-logos.php`

**Q: Performa loading lambat?**
A: Semua URL sudah menggunakan CDN yang dioptimasi, biasanya tidak ada masalah. Jika perlu, bisa caching di sisi server.

---

**Last Updated:** 31 Desember 2025
**Version:** 1.0

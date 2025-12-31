<?php
session_start();
require_once '../config.php';
require_once '../includes/brand-logos.php';

$page_title = "Daftar Produk";
include '../includes/header.php';
?>

<style>
.filter-active .form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.brand-filter-item {
    transition: all 0.3s ease;
}

.brand-filter-item:hover {
    background-color: #f5f5f5;
    border-radius: 8px;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
}
</style>

<div class="container-fluid py-4">
    <div class="container">
        <!-- Header -->
        <h1 class="mb-2">Daftar Produk</h1>
        <p class="text-muted mb-4">Temukan smartphone pilihan terbaik di MobileNest</p>
        
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">🔍 Filter</h6>
                        
                        <!-- Cari Produk -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Cari Produk</label>
                            <input type="text" class="form-control" placeholder="Ketik nama produk..." id="search_produk">
                        </div>
                        
                        <!-- Filter Merek dengan Logo -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">📱 Merek</label>
                            <div id="brand_filter_container">
                                <?php
                                $available_brands = get_all_brands();
                                foreach ($available_brands as $brand):
                                    $logo_data = get_brand_logo_data($brand);
                                    if ($logo_data):
                                ?>
                                <div class="form-check d-flex align-items-center mb-3 brand-filter-item p-2">
                                    <div style="width: 30px; height: 30px; margin-right: 10px; flex-shrink: 0;">
                                        <img src="<?php echo htmlspecialchars($logo_data['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($brand); ?> Logo" 
                                             style="width: 100%; height: 100%; object-fit: contain;" 
                                             onerror="this.src='https://via.placeholder.com/30?text=<?php echo $brand; ?>';">
                                    </div>
                                    <div class="form-check" style="flex-grow: 1;">
                                        <input class="form-check-input brand-checkbox" type="checkbox" 
                                               value="<?php echo htmlspecialchars($brand); ?>" 
                                               id="merek_<?php echo strtolower(str_replace(' ', '_', $brand)); ?>">
                                        <label class="form-check-label" for="merek_<?php echo strtolower(str_replace(' ', '_', $brand)); ?>">
                                            <?php echo htmlspecialchars($brand); ?>
                                        </label>
                                    </div>
                                </div>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                        
                        <!-- Filter Harga -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">💰 Harga</label>
                            <div class="form-check">
                                <input class="form-check-input price-checkbox" type="checkbox" value="1000000:3000000" id="harga_1">
                                <label class="form-check-label" for="harga_1">Rp 1 - 3 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-checkbox" type="checkbox" value="3000000:7000000" id="harga_2">
                                <label class="form-check-label" for="harga_2">Rp 3 - 7 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-checkbox" type="checkbox" value="7000000:15000000" id="harga_3">
                                <label class="form-check-label" for="harga_3">Rp 7 - 15 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-checkbox" type="checkbox" value="15000000:999999999" id="harga_4">
                                <label class="form-check-label" for="harga_4">Rp 15+ Juta</label>
                            </div>
                        </div>
                        
                        <!-- Tombol Filter -->
                        <button class="btn btn-primary w-100 mb-2" onclick="applyFilters()">
                            <i class="bi bi-funnel"></i> Terapkan Filter
                        </button>
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                            <i class="bi bi-arrow-clockwise"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-md-9">
                <!-- Products Count & Sort -->
                <div class="row mb-4 align-items-center">
                    <div class="col-md-6">
                        <p class="text-muted mb-0">Menampilkan <strong id="product_count">0</strong> produk</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <select class="form-select form-select-sm w-auto" style="display: inline-block;" id="sort_option" onchange="applyFilters()">
                            <option value="terbaru">Terbaru</option>
                            <option value="harga_rendah">Harga Terendah</option>
                            <option value="harga_tinggi">Harga Tertinggi</option>
                            <option value="populer">Paling Populer</option>
                        </select>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div id="products_container" class="product-grid">
                    <div class="col-12 text-center text-muted py-5">
                        <p>Loading produk...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load products dari API
async function loadProducts(filters = {}) {
    try {
        // Get all products
        const response = await fetch('../produk/get-produk.php');
        const allProducts = await response.json();
        
        // Apply filters
        let filtered = allProducts;
        
        // Filter by brand
        const selectedBrands = Array.from(document.querySelectorAll('.brand-checkbox:checked')).map(el => el.value);
        if (selectedBrands.length > 0) {
            filtered = filtered.filter(p => selectedBrands.includes(p.merek));
        }
        
        // Filter by price
        const selectedPrices = Array.from(document.querySelectorAll('.price-checkbox:checked')).map(el => {
            const [min, max] = el.value.split(':').map(Number);
            return { min, max };
        });
        if (selectedPrices.length > 0) {
            filtered = filtered.filter(p => 
                selectedPrices.some(range => p.harga >= range.min && p.harga <= range.max)
            );
        }
        
        // Sort products
        const sortOption = document.getElementById('sort_option').value;
        switch(sortOption) {
            case 'harga_rendah':
                filtered.sort((a, b) => a.harga - b.harga);
                break;
            case 'harga_tinggi':
                filtered.sort((a, b) => b.harga - a.harga);
                break;
            case 'populer':
                filtered.sort((a, b) => b.id_produk - a.id_produk);
                break;
            case 'terbaru':
            default:
                filtered.sort((a, b) => new Date(b.tanggal_ditambahkan) - new Date(a.tanggal_ditambahkan));
        }
        
        // Search products
        const searchQuery = document.getElementById('search_produk').value.toLowerCase();
        if (searchQuery) {
            filtered = filtered.filter(p => 
                p.nama_produk.toLowerCase().includes(searchQuery) || 
                p.merek.toLowerCase().includes(searchQuery)
            );
        }
        
        // Update count
        document.getElementById('product_count').textContent = filtered.length;
        
        // Render products
        const container = document.getElementById('products_container');
        if (filtered.length === 0) {
            container.innerHTML = '<div class="col-12 text-center text-muted py-5"><p>Tidak ada produk yang sesuai dengan filter Anda</p></div>';
            return;
        }
        
        container.innerHTML = filtered.map(produk => `
            <div class="card border-0 shadow-sm h-100 transition" style="cursor: pointer;">
                <!-- Product Image -->
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px; position: relative;">
                    <i class="bi bi-phone" style="font-size: 3rem; color: #ccc;"></i>
                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">-15%</span>
                </div>
                
                <!-- Product Info -->
                <div class="card-body">
                    <h6 class="card-title mb-2">${escapeHtml(produk.nama_produk)}</h6>
                    
                    <!-- Brand Info -->
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-secondary">${escapeHtml(produk.merek)}</span>
                    </div>
                    
                    <!-- Rating -->
                    <div class="mb-2">
                        <span class="text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </span>
                        <span class="text-muted small">(152)</span>
                    </div>
                    
                    <!-- Price -->
                    <h5 class="text-primary mb-3">Rp ${formatPrice(produk.harga)}</h5>
                    
                    <!-- Buttons -->
                    <div class="d-grid gap-2">
                        <a href="detail-produk.php?id=${produk.id_produk}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-search"></i> Lihat Detail
                        </a>
                        <button class="btn btn-primary btn-sm" onclick="addToCart(${produk.id_produk}, 1)">
                            <i class="bi bi-cart-plus"></i> Tambah Keranjang
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Error loading products:', error);
        document.getElementById('products_container').innerHTML = '<div class="col-12 text-center text-danger py-5"><p>Error loading products</p></div>';
    }
}

// Helper function to format price
function formatPrice(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Helper function to escape HTML
function escapeHtml(text) {
    const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Apply filters
function applyFilters() {
    loadProducts();
}

// Reset filters
function resetFilters() {
    document.querySelectorAll('.brand-checkbox').forEach(el => el.checked = false);
    document.querySelectorAll('.price-checkbox').forEach(el => el.checked = false);
    document.getElementById('search_produk').value = '';
    document.getElementById('sort_option').value = 'terbaru';
    loadProducts();
}

// Search on input
document.getElementById('search_produk').addEventListener('input', function(e) {
    loadProducts();
});

// Load products on page load
window.addEventListener('DOMContentLoaded', function() {
    loadProducts();
});

// Add to cart function (stub - implement based on your existing function)
async function addToCart(id_produk, quantity = 1) {
    console.log('Adding to cart:', id_produk, quantity);
    // Call your existing addToCart function from cart.js
    if (typeof window.originalAddToCart === 'function') {
        await window.originalAddToCart(id_produk, quantity);
    } else {
        alert('Produk berhasil ditambahkan ke keranjang!');
    }
}
</script>

<script src="../assets/js/api-handler.js"></script>
<script src="../assets/js/cart.js"></script>

<?php include '../includes/footer.php'; ?>
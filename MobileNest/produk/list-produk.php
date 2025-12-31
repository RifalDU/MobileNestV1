<?php
session_start();
require_once '../config.php';
require_once '../includes/brand-logos.php';

$page_title = "Daftar Produk";
include '../includes/header.php';
?>

<style>
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .card.transition {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .card.transition:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
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
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">🔍 Filter Produk</h6>
                        
                        <!-- Cari Produk -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Cari Produk</label>
                            <input type="text" class="form-control form-control-sm" 
                                   placeholder="Ketik nama produk..." id="search_produk">
                        </div>
                        
                        <!-- Filter Merek dengan Logo -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small mb-3">📱 Merek</label>
                            <div id="brand_filter_container">
                                <?php
                                $available_brands = get_all_brands();
                                foreach ($available_brands as $brand):
                                    $logo_data = get_brand_logo_data($brand);
                                    if ($logo_data):
                                ?>
                                <div class="form-check d-flex align-items-center mb-2 p-2" style="cursor: pointer;">
                                    <div style="width: 28px; height: 28px; margin-right: 8px; flex-shrink: 0;">
                                        <img src="<?php echo htmlspecialchars($logo_data['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($brand); ?> Logo" 
                                             style="width: 100%; height: 100%; object-fit: contain;" 
                                             onerror="this.src='https://via.placeholder.com/28?text=<?php echo urlencode($brand); ?>';" />
                                    </div>
                                    <div class="form-check" style="flex-grow: 1;">
                                        <input class="form-check-input brand-checkbox" type="checkbox" 
                                               value="<?php echo htmlspecialchars($brand); ?>" 
                                               id="merek_<?php echo strtolower(str_replace(' ', '_', $brand)); ?>" />
                                        <label class="form-check-label small" 
                                               for="merek_<?php echo strtolower(str_replace(' ', '_', $brand)); ?>">
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
                            <label class="form-label fw-bold small mb-3">💰 Harga</label>
                            <div class="form-check">
                                <input class="form-check-input price-checkbox" type="checkbox" 
                                       value="1000000:3000000" id="harga_1" />
                                <label class="form-check-label small" for="harga_1">Rp 1 - 3 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-checkbox" type="checkbox" 
                                       value="3000000:7000000" id="harga_2" />
                                <label class="form-check-label small" for="harga_2">Rp 3 - 7 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-checkbox" type="checkbox" 
                                       value="7000000:15000000" id="harga_3" />
                                <label class="form-check-label small" for="harga_3">Rp 7 - 15 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-checkbox" type="checkbox" 
                                       value="15000000:999999999" id="harga_4" />
                                <label class="form-check-label small" for="harga_4">Rp 15+ Juta</label>
                            </div>
                        </div>
                        
                        <!-- Tombol Filter -->
                        <button class="btn btn-primary btn-sm w-100 mb-2" onclick="applyFilter()">
                            <i class="bi bi-funnel"></i> Terapkan Filter
                        </button>
                        <button class="btn btn-outline-secondary btn-sm w-100" onclick="resetFilter()">
                            <i class="bi bi-arrow-clockwise"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-lg-9 col-md-8">
                <!-- Products Count & Sort -->
                <div class="row mb-4 align-items-center">
                    <div class="col-md-6">
                        <p class="text-muted mb-0 small">Menampilkan <strong id="product_count">0</strong> produk</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <select class="form-select form-select-sm w-auto" style="display: inline-block;" 
                                id="sort_option" onchange="applyFilter()">
                            <option value="terbaru">Terbaru</option>
                            <option value="harga_rendah">Harga Terendah</option>
                            <option value="harga_tinggi">Harga Tertinggi</option>
                            <option value="populer">Paling Populer</option>
                        </select>
                    </div>
                </div>
                
                <!-- Products Grid Container (AJAX Dynamic) -->
                <div id="products_container" class="product-grid">
                    <div class="col-12 text-center text-muted py-5">
                        <i class="bi bi-hourglass-split" style="font-size: 2rem;"></i>
                        <p class="mt-3">Memuat produk...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    // Check if user is logged in (set this from backend if needed)
    var userLoggedIn = <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>;
    
    // Debug info
    console.log('list-produk.php loaded');
    console.log('userLoggedIn:', userLoggedIn);
    console.log('products_container element:', document.getElementById('products_container'));
</script>

<!-- Load filter.js FIRST (contains rendering logic) -->
<script src="../assets/js/filter.js"></script>

<!-- Load cart functionality -->
<script src="../assets/js/cart.js"></script>
<script src="../assets/js/api-handler.js"></script>

<?php include '../includes/footer.php'; ?>

/**
 * ============================================
 * FILE: filter.js
 * PURPOSE: Handle product filtering
 * LOCATION: MobileNest/assets/js/filter.js
 * ============================================
 */

/**
 * Get all selected filters from checkboxes
 */
function getSelectedFilters() {
    const filters = {
        brands: [],
        prices: []
    };

    // Get selected brands
    const brandCheckboxes = document.querySelectorAll('input[type="checkbox"][id^="merek_"]');
    brandCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            filters.brands.push(checkbox.value);
        }
    });

    // Get selected prices
    const priceCheckboxes = document.querySelectorAll('input[type="checkbox"][id^="harga_"]');
    priceCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            filters.prices.push(checkbox.value);
        }
    });

    return filters;
}

/**
 * Apply filters and fetch products from API
 */
async function applyFilter() {
    try {
        const filters = getSelectedFilters();
        console.log('Applying filters:', filters);

        // Build query params
        const params = new URLSearchParams();
        
        if (filters.brands.length > 0) {
            params.append('brand', filters.brands.join(','));
        }

        if (filters.prices.length > 0) {
            // Parse price filters
            let minPrice = 0;
            let maxPrice = 999999999;

            filters.prices.forEach(priceRange => {
                const [min, max] = priceRange.split(':').map(Number);
                if (minPrice < min) minPrice = min;
                if (maxPrice > max) maxPrice = max;
            });

            params.append('min_price', minPrice);
            params.append('max_price', maxPrice);
        }

        // Get search query if exists
        const searchInput = document.getElementById('search_produk');
        if (searchInput && searchInput.value) {
            params.append('search', searchInput.value);
        }

        // Get sort option if exists
        const sortSelect = document.getElementById('sort_option');
        if (sortSelect) {
            params.append('sort', sortSelect.value);
        }

        // Fetch from API
        const response = await fetch(`produk/get-produk.php?${params.toString()}`);
        
        if (!response.ok) {
            throw new Error('API Error: ' + response.statusText);
        }

        const products = await response.json();
        renderProducts(products);
        showFilterNotification('success', `Filter applied - Showing ${products.length} products`);

    } catch (error) {
        console.error('Error applying filter:', error);
        showFilterNotification('error', 'Error applying filter: ' + error.message);
    }
}

/**
 * Render products to the page
 */
function renderProducts(products) {
    const container = document.getElementById('products_container');
    
    if (!container) {
        console.error('products_container not found');
        return;
    }

    // Update product count
    const countElement = document.getElementById('product_count');
    if (countElement) {
        countElement.textContent = products.length;
    }

    if (products.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-3">Tidak ada produk yang sesuai dengan filter Anda</p>
            </div>
        `;
        return;
    }

    // Render product cards
    container.innerHTML = products.map(product => `
        <div class="card border-0 shadow-sm h-100 transition">
            <!-- Product Image -->
            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px; position: relative; overflow: hidden;">
                ${product.gambar ? `
                    <img src="${escapeHtml(product.gambar)}" alt="${escapeHtml(product.nama_produk)}" style="width: 100%; height: 100%; object-fit: cover;">
                ` : `
                    <i class="bi bi-phone" style="font-size: 3rem; color: #ccc;"></i>
                `}
                <span class="badge bg-danger position-absolute top-0 end-0 m-2">-${Math.floor(Math.random() * 30) + 5}%</span>
            </div>
            
            <!-- Product Info -->
            <div class="card-body">
                <h6 class="card-title mb-2" title="${escapeHtml(product.nama_produk)}">${escapeHtml(product.nama_produk)}</h6>
                
                <!-- Brand Info -->
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-secondary">${escapeHtml(product.merek)}</span>
                    <span class="badge bg-info ms-2">Stok: ${product.stok}</span>
                </div>
                
                <!-- Rating -->
                <div class="mb-2">
                    <span class="text-warning">
                        ${getRatingStars(4.5)}
                    </span>
                    <span class="text-muted small">(${Math.floor(Math.random() * 200) + 50})</span>
                </div>
                
                <!-- Price -->
                <h5 class="text-primary mb-3">Rp ${formatPrice(product.harga)}</h5>
                
                <!-- Buttons -->
                <div class="d-grid gap-2">
                    <a href="produk/detail-produk.php?id=${product.id_produk}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-search"></i> Lihat Detail
                    </a>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addToCartFromFilter(${product.id_produk}, 1, '${escapeHtml(product.nama_produk)}'" >
                        <i class="bi bi-cart-plus"></i> Keranjang
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Reset all filters
 */
function resetFilter() {
    console.log('Resetting filters');

    // Uncheck all checkboxes
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Clear search
    const searchInput = document.getElementById('search_produk');
    if (searchInput) {
        searchInput.value = '';
    }

    // Reset sort
    const sortSelect = document.getElementById('sort_option');
    if (sortSelect) {
        sortSelect.value = 'terbaru';
    }

    // Reload all products
    applyFilter();
    showFilterNotification('info', 'Filter reset - Showing all products');
}

/**
 * Show notification
 */
function showFilterNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';

    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed bottom-0 end-0 m-3`;
    alert.style.zIndex = '9999';
    alert.innerHTML = `
        <i class="bi bi-${icon}"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alert);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        alert.remove();
    }, 3000);
}

/**
 * Format price to Indonesian format
 */
function formatPrice(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

/**
 * Escape HTML special characters
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

/**
 * Get rating stars HTML
 */
function getRatingStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(rating)) {
            stars += '<i class="bi bi-star-fill"></i>';
        } else if (i - rating < 1) {
            stars += '<i class="bi bi-star-half"></i>';
        } else {
            stars += '<i class="bi bi-star"></i>';
        }
    }
    return stars;
}

/**
 * Add to cart from filter page
 */
function addToCartFromFilter(id_produk, quantity = 1, nama_produk = '') {
    console.log('Adding to cart:', id_produk, quantity, nama_produk);
    
    // Check if user logged in
    if (typeof userLoggedIn === 'undefined' || !userLoggedIn) {
        alert('Silakan login terlebih dahulu untuk menambahkan produk ke keranjang');
        window.location.href = 'user/login.php';
        return;
    }

    // Call existing cart function if available
    if (typeof window.addToCart === 'function') {
        window.addToCart(id_produk, quantity);
    } else {
        // Fallback: send to cart API
        fetch('transaksi/keranjang-aksi.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add&id_produk=${id_produk}&quantity=${quantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showFilterNotification('success', `${nama_produk} ditambahkan ke keranjang`);
            } else {
                showFilterNotification('error', data.message || 'Gagal menambahkan ke keranjang');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showFilterNotification('error', 'Gagal menambahkan ke keranjang');
        });
    }
}

/**
 * Initialize filter event listeners
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Filter JS initialized');

    // Get filter buttons
    const applyBtn = document.querySelector('button[onclick="applyFilters()"]') || 
                     document.querySelector('button:contains("Terapkan")');
    const resetBtn = document.querySelector('button[onclick="resetFilters()"]') || 
                     document.querySelector('button:contains("Reset")');

    // Setup filter button click handlers
    document.querySelectorAll('button').forEach(btn => {
        if (btn.textContent.includes('Terapkan')) {
            btn.onclick = applyFilter;
        }
        if (btn.textContent.includes('Reset')) {
            btn.onclick = resetFilter;
        }
    });

    // Setup checkbox change handlers
    document.querySelectorAll('.brand-checkbox, .price-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Optional: auto-apply filter when checkbox changes
            // applyFilter();
        });
    });

    // Setup search input handler
    const searchInput = document.getElementById('search_produk');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            applyFilter();
        });
    }

    // Setup sort change handler
    const sortSelect = document.getElementById('sort_option');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            applyFilter();
        });
    }

    // Load initial products
    applyFilter();

    console.log('Filter JS setup complete');
});

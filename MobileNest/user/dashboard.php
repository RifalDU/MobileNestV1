<?php
/**
 * User Dashboard
 * Only accessible by regular users
 * Includes role check middleware
 */

require_once '../api/auth/check_auth.php';

// Check if user is regular user (not admin)
if ($user_role !== 'user') {
    http_response_code(403);
    echo '<div style="text-align: center; padding: 50px; background: #fee; color: #c33; border-radius: 8px; margin: 20px;">';
    echo '<h2>❌ Access Denied!</h2>';
    echo '<p>Only regular users can access this page.</p>';
    echo '<a href="../login.php">Go to Login</a>';
    echo '</div>';
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - MobileNest</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            color: #333;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar h2 {
            font-size: 24px;
        }
        
        .user-info {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .badge-user {
            background: rgba(255,255,255,0.3);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .welcome-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        
        .welcome-box h1 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .welcome-box p {
            color: #666;
            margin-bottom: 5px;
        }
        
        .user-details {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 14px;
        }
        
        .user-details strong {
            color: #667eea;
        }
        
        .menu-section {
            margin-bottom: 30px;
        }
        
        .menu-section h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .menu-item {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s;
            border-top: 3px solid #667eea;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);
        }
        
        .menu-item-icon {
            font-size: 40px;
        }
        
        .menu-item-title {
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }
        
        .menu-item-desc {
            font-size: 13px;
            color: #666;
        }
        
        .status-info {
            background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        
        .status-info h3 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .status-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(102,126,234,0.2);
            font-size: 14px;
        }
        
        .status-item:last-child {
            border-bottom: none;
        }
        
        .status-value {
            font-weight: 600;
            color: #667eea;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div>
            <h2>📱 MobileNest Shopping</h2>
        </div>
        <div class="user-info">
            <span class="badge-user">👤 USER</span>
            <span><?= htmlspecialchars($nama_lengkap) ?></span>
            <a href="../api/auth/logout.php" class="btn-logout">🚪 Logout</a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container">
        <!-- Welcome Box -->
        <div class="welcome-box">
            <h1>👋 Selamat Datang, <?= htmlspecialchars($nama_lengkap) ?>!</h1>
            <p>Anda login sebagai <strong>Pelanggan</strong> MobileNest</p>
            <p>Nikmati belanja smartphone terbaik dengan harga kompetitif.</p>
            <div class="user-details">
                <strong>Profile Info:</strong><br>
                Username: <strong><?= htmlspecialchars($username) ?></strong><br>
                Email: <strong><?= htmlspecialchars($_SESSION['email'] ?? 'N/A') ?></strong><br>
                Login Time: <strong><?= date('d M Y H:i:s', $_SESSION['login_time'] ?? time()) ?></strong>
            </div>
        </div>
        
        <!-- Status Info -->
        <div class="status-info">
            <h3>📋 My Activity</h3>
            <div class="status-item">
                <span>Pesanan Aktif:</span>
                <span class="status-value">0</span>
            </div>
            <div class="status-item">
                <span>Total Pesanan:</span>
                <span class="status-value">0</span>
            </div>
            <div class="status-item">
                <span>Total Pengeluaran:</span>
                <span class="status-value">Rp 0</span>
            </div>
            <div class="status-item">
                <span>Wishlist Items:</span>
                <span class="status-value">0</span>
            </div>
        </div>
        
        <!-- User Menu -->
        <div class="menu-section">
            <h2>🛍️ Shopping & Account Menu</h2>
            <div class="menu-grid">
                <a href="../produk/list-produk.php" class="menu-item">
                    <div class="menu-item-icon">📦</div>
                    <div class="menu-item-title">Belanja Produk</div>
                    <div class="menu-item-desc">Lihat katalog smartphone</div>
                </a>
                
                <a href="order-history.php" class="menu-item">
                    <div class="menu-item-icon">📋</div>
                    <div class="menu-item-title">Riwayat Pesanan</div>
                    <div class="menu-item-desc">Lihat pesanan Anda</div>
                </a>
                
                <a href="wishlist.php" class="menu-item">
                    <div class="menu-item-icon">❤️</div>
                    <div class="menu-item-title">Wishlist</div>
                    <div class="menu-item-desc">Produk yang disimpan</div>
                </a>
                
                <a href="cart.php" class="menu-item">
                    <div class="menu-item-icon">🛌</div>
                    <div class="menu-item-title">Keranjang Belanja</div>
                    <div class="menu-item-desc">Lihat keranjang Anda</div>
                </a>
                
                <a href="profile.php" class="menu-item">
                    <div class="menu-item-icon">👤</div>
                    <div class="menu-item-title">Edit Profil</div>
                    <div class="menu-item-desc">Update data pribadi</div>
                </a>
                
                <a href="settings.php" class="menu-item">
                    <div class="menu-item-icon">⚙️</div>
                    <div class="menu-item-title">Pengaturan Akun</div>
                    <div class="menu-item-desc">Ubah password & preferensi</div>
                </a>
            </div>
        </div>
        
        <!-- Featured Products -->
        <div class="menu-section">
            <h2>🌟 Produk Unggulan</h2>
            <div style="background: white; padding: 20px; border-radius: 12px; color: #666; text-align: center;">
                <p>🔍 Sedang memuat produk unggulan...</p>
                <p style="font-size: 12px; margin-top: 10px;">Klik "Belanja Produk" untuk melihat semua katalog</p>
            </div>
        </div>
        
        <!-- Promo Banner -->
        <div class="menu-section">
            <h2>🎁 Promosi Terbaru</h2>
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 12px; text-align: center;">
                <h3 style="margin-bottom: 10px;">Dapatkan Diskon Spesial Hari Ini!</h3>
                <p>Promosi terbaru dan penawaran eksklusif menanti Anda di MobileNest</p>
                <a href="../produk/list-produk.php" style="display: inline-block; margin-top: 15px; background: white; color: #667eea; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600;">🛍️ Belanja Sekarang</a>
            </div>
        </div>
    </div>
</body>
</html>

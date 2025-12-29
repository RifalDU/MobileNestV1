<?php
// FILE: index.php
require_once 'config.php'; // Load config dulu

$page_title = "Beranda"; 
// Kita TIDAK butuh $css_path atau $js_path lagi karena header.php sudah pakai SITE_URL

include 'includes/header.php';
?>

<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold">Gadget Impian,<br>Harga Teman.</h1>
                <p class="lead mb-4">Temukan smartphone terbaru dengan garansi resmi dan penawaran terbaik hanya di MobileNest.</p>
                <a href="<?php echo SITE_URL; ?>/produk/list-produk.php" class="btn btn-warning btn-lg fw-bold px-4 shadow-sm">
                    <i class="bi bi-bag-check"></i> Belanja Sekarang
                </a>
            </div>
            <div class="col-lg-6 text-center mt-4 mt-lg-0">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.jpg" alt="MobileNest" class="img-fluid rounded shadow-lg" style="max-height: 300px;">
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Produk Terbaru</h2>
            <a href="<?php echo SITE_URL; ?>/produk/list-produk.php" class="text-decoration-none">Lihat Semua &rarr;</a>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php
            // Ambil 4 produk terbaru
            $sql = "SELECT * FROM produk WHERE status_produk = 'Tersedia' ORDER BY tanggal_ditambahkan DESC LIMIT 4";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Logic Gambar
                    $gambar_path = "uploads/" . $row['gambar'];
                    $img_src = (!empty($row['gambar']) && file_exists($gambar_path)) 
                                ? SITE_URL . '/' . $gambar_path 
                                : SITE_URL . '/assets/images/logo.jpg'; // Fallback image
            ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <img src="<?php echo $img_src; ?>" class="card-img-top p-3" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>" style="height: 200px; object-fit: contain;">
                        <div class="card-body">
                            <h5 class="card-title text-truncate"><?php echo htmlspecialchars($row['nama_produk']); ?></h5>
                            <p class="card-text text-primary fw-bold">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                            <div class="d-grid">
                                <a href="<?php echo SITE_URL; ?>/produk/detail-produk.php?id=<?php echo $row['id_produk']; ?>" class="btn btn-outline-primary btn-sm">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                }
            } else {
                echo '<div class="col-12 text-center text-muted">Belum ada produk.</div>';
            }
            ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
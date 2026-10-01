<?php
include 'koneksi.php';

$kategori = $_GET['kategori'] ?? '';

// Query Kategori
$q_kat = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM products WHERE kategori != ''");

// Query Produk
$sql = $kategori ? "SELECT * FROM products WHERE kategori = '$kategori'" : "SELECT * FROM products";
$q_prod = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Sesi 8 - Katalog Produk</title>
</head>
<body>

    <h2>Katalog Produk E-Commerce</h2>

    <!-- Form Filter Kategori -->
    <form method="GET">
        <label>Filter Kategori:</label>
        <select name="kategori" onchange="this.form.submit()">
            <option value="">-- Semua Kategori --</option>
            <?php while ($row = mysqli_fetch_assoc($q_kat)): ?>
                <option value="<?= $row['kategori'] ?>" <?= $kategori == $row['kategori'] ? 'selected' : '' ?>>
                    <?= $row['kategori'] ?>
                </option>
            <?php endwhile; ?>
        </select>
        <?php if ($kategori): ?> <a href="index.php">Reset</a> <?php endif; ?>
    </form>

    <hr>

    <!-- Looping Produk -->
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <?php if (mysqli_num_rows($q_prod) > 0): ?>
            <?php while ($p = mysqli_fetch_assoc($q_prod)): ?>
                <div style="border: 1px solid #ccc; padding: 15px; width: 200px; border-radius: 5px;">
                    <small><b><?= $p['kategori'] ?? 'Umum' ?></b></small>
                    <h3><?= $p['nama_produk'] ?></h3>
                    <p><?= $p['deskripsi'] ?></p>
                    <p><b>Rp <?= number_format($p['harga'], 0, ',', '.') ?></b></p>
                    <small>Stok: <?= $p['stok'] ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Produk tidak ditemukan.</p>
        <?php endif; ?>
    </div>

</body>
</html>
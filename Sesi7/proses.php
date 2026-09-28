<?php
// Cek apakah form dikirim lewat method POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Deklarasi Variabel & Tangkap Data dari Form
    $nama_produk = trim($_POST['nama_produk']);
    $harga       = trim($_POST['harga']);
    $deskripsi   = trim($_POST['deskripsi']);

    // 2. Validasi Sederhana (Menggunakan If-Else & Operator)
    // Cek apakah ada kolom yang kosong
    if (empty($nama_produk) || empty($harga) || empty($deskripsi)) {
        echo "<h3 style='color: red;'>Eror: Semua bidang (Nama, Harga, Deskripsi) wajib diisi!</h3>";
        echo "<a href='index.php'>&laquo; Kembali ke Form</a>";
    } 
    // Cek apakah harga kurang dari atau sama dengan 0
    elseif ($harga <= 0) {
        echo "<h3 style='color: red;'>Eror: Harga produk harus lebih dari 0!</h3>";
        echo "<a href='index.php'>&laquo; Kembali ke Form</a>";
    } 
    // Jika semua data valid
    else {
        echo "<h3 style='color: green;'>Sukses! Data produk berhasil divalidasi.</h3>";
        echo "<hr>";
        echo "<p><b>Nama Produk:</b> " . htmlspecialchars($nama_produk) . "</p>";
        echo "<p><b>Harga:</b> Rp " . number_format($harga, 0, ',', '.') . "</p>";
        echo "<p><b>Deskripsi:</b> " . nl2br(htmlspecialchars($deskripsi)) . "</p>";
        echo "<br><a href='index.php'>+ Tambah Produk Lain</a>";
    }

} else {
    // Tendang balik ke form kalau coba akses file proses.php secara langsung
    header("Location: index.php");
    exit();
}
?>
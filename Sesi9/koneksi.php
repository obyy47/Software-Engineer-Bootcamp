<?php
$host = "localhost";
$user = "root";
$pass = ""; // Kosongkan jika pakai Laragon default
$db   = "sesi9";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>
<?php
$host     = "localhost";
$user     = "root";
$password = ""; // Default Laragon kosong
$database = "ecommerce_db";

$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>
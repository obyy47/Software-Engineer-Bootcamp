<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi 7 - Form Tambah Produk</title>
</head>
<body>
    <h2>Tugas Form Input Produk</h2>
    
    <!-- Data dikirim ke proses.php menggunakan method POST -->
    <form action="proses.php" method="POST">
        <div>
            <label for="nama">Nama Produk:</label><br>
            <input type="text" id="nama" name="nama_produk">
        </div>
        <br>
        <div>
            <label for="harga">Harga Produk (Rp):</label><br>
            <input type="number" id="harga" name="harga">
        </div>
        <br>
        <div>
            <label for="deskripsi">Deskripsi Produk:</label><br>
            <textarea id="deskripsi" name="deskripsi" rows="4" cols="30"></textarea>
        </div>
        <br>
        <button type="submit" name="submit">Simpan Produk</button>
    </form>
</body>
</html>
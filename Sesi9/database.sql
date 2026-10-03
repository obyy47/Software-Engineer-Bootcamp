-- 1. Buat database sesi9
CREATE DATABASE IF NOT EXISTS sesi9;
USE sesi9;

-- 2. Buat tabel products (Fitur CRUD Produk)
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_produk VARCHAR(255) NOT NULL,
  deskripsi TEXT,
  harga INT NOT NULL,
  gambar VARCHAR(255) DEFAULT 'default.jpg'
);

-- 3. Buat tabel cart (Fitur Keranjang Belanja)
CREATE TABLE IF NOT EXISTS cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  jumlah INT DEFAULT 1,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 4. Isikan dummy data produk awal Kilaura
INSERT INTO products (nama_produk, deskripsi, harga, gambar) VALUES
('Gelang Custom Manik', 'Gelang buatan tangan dengan manik-manik estetik Kilaura', 15000, 'default.jpg'),
('Cincin Manik Minimalis', 'Cincin manik cantik ukuran adjustable', 10000, 'default.jpg');
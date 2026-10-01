-- bikin database
CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- bikin tabel products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(255),
    harga INT,
    deskripsi VARCHAR(255),
    stok INT
);

-- bikin tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255),
    email VARCHAR(255),
    `password` VARCHAR(255)
);

-- bikin tabel orders
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT,
    total INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- membuat/menambahkan (CREATE) data/barang ke tabel products
INSERT INTO products
    (nama_produk, harga, deskripsi, stok)
VALUES 
    ('Nike Blazer', 1750000, 'Sepatu kasual warna putih ukuran 42', 5);

INSERT INTO products
    (nama_produk, harga, deskripsi, stok)
VALUES 
    ('Adidas Samba', 3000000, 'Sepatu kasual warna hitam ukuran 43', 12);

-- membaca/mengambil/menampilkan data (READ) pada tabel products
SELECT * FROM products;

-- mengubah (UPDATE) data pada tabel products
UPDATE products
SET harga = 2500000, stok = 22
WHERE id = 1;

UPDATE products
SET harga = 2700000, stok = 9
WHERE id = 2;

-- menghapus (DELETE) data pada tabel products yang mengacu kepada id
DELETE FROM products
WHERE id = 2;

-- menambah kolom kategori untuk kebutuhan Sesi 8
ALTER TABLE products ADD COLUMN kategori VARCHAR(100) AFTER deskripsi;

-- Update kategori data produk yang tersisa
UPDATE products SET kategori = 'Gelang' WHERE id = 1;

-- Tambah data produk baru biar pilihan kategorinya bervariasi
INSERT INTO products (nama_produk, harga, deskripsi, kategori, stok) VALUES 
('Cincin Crystal Sparkle', 45000, 'Cincin manik-manik aksen kristal', 'Cincin', 15),
('Gantungan Kunci Character', 25000, 'Gantungan kunci manik lucu', 'Gantungan Kunci', 20);
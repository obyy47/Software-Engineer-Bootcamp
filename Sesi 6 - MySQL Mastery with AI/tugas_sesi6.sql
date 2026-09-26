-- bikin database
CREATE DATABASE ecommerce_db;
USE ecommerce_db;

-- bikin tabel products
CREATE TABLE products (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nama_produk VARCHAR(255),
	harga INT,
	deskripsi VARCHAR(255),
	stok INT
);

-- bikin tabel users
CREATE TABLE users (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nama VARCHAR(255),
	email VARCHAR(255),
	`password` VARCHAR(255)
);

-- bikin tabel orders
CREATE TABLE orders (
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

-- menambahkan (CREATE) barang ke tabel products
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

-- mengubah (UPDATE) data pada tabel products
UPDATE products
SET harga = 2700000, stok = 9
WHERE id = 2;

-- menghapus (DELETE) data pada tabel products yang mengacu kepada id
DELETE FROM products
WHERE id = 2;



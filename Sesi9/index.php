<?php
include 'koneksi.php';

// ==================== 1. LOGIKA CRUD PRODUK ====================

// A. CREATE: Tambah Produk Baru
if (isset($_POST['tambah_produk'])) {
    $nama_produk = $_POST['nama_produk'];
    $deskripsi   = $_POST['deskripsi'];
    $harga       = $_POST['harga'];

    $gambar = 'default.jpg';
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . '_' . $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $gambar);
    }

    $query = "INSERT INTO products (nama_produk, deskripsi, harga, gambar) 
              VALUES ('$nama_produk', '$deskripsi', '$harga', '$gambar')";
    mysqli_query($conn, $query);
    header("Location: index.php");
    exit();
}

// B. DELETE: Hapus Produk
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    header("Location: index.php");
    exit();
}

// C. UPDATE: Edit Data Produk
if (isset($_POST['edit_produk'])) {
    $id          = $_POST['id'];
    $nama_produk = $_POST['nama_produk'];
    $deskripsi   = $_POST['deskripsi'];
    $harga       = $_POST['harga'];

    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . '_' . $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $gambar);
        $query = "UPDATE products SET nama_produk='$nama_produk', deskripsi='$deskripsi', harga='$harga', gambar='$gambar' WHERE id=$id";
    } else {
        $query = "UPDATE products SET nama_produk='$nama_produk', deskripsi='$deskripsi', harga='$harga' WHERE id=$id";
    }

    mysqli_query($conn, $query);
    header("Location: index.php");
    exit();
}

// ==================== 2. LOGIKA KERANJANG (CART) ====================

// A. ADD TO CART
if (isset($_GET['tambah_cart'])) {
    $product_id = $_GET['tambah_cart'];
    $cek = mysqli_query($conn, "SELECT * FROM cart WHERE product_id = $product_id");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE cart SET jumlah = jumlah + 1 WHERE product_id = $product_id");
    } else {
        mysqli_query($conn, "INSERT INTO cart (product_id, jumlah) VALUES ($product_id, 1)");
    }
    header("Location: index.php");
    exit();
}

// B. KURANGI QTY / TAMBAH QTY DIRECT
if (isset($_GET['update_qty'])) {
    $cart_id = $_GET['update_qty'];
    $action  = $_GET['action'];

    if ($action == 'plus') {
        mysqli_query($conn, "UPDATE cart SET jumlah = jumlah + 1 WHERE id = $cart_id");
    } elseif ($action == 'minus') {
        $get_item = mysqli_query($conn, "SELECT jumlah FROM cart WHERE id = $cart_id");
        $item = mysqli_fetch_assoc($get_item);
        if ($item['jumlah'] > 1) {
            mysqli_query($conn, "UPDATE cart SET jumlah = jumlah - 1 WHERE id = $cart_id");
        }
    }
    header("Location: index.php");
    exit();
}

// C. UPDATE QTY MANUAL VIA INPUT ANGKA
if (isset($_GET['update_manual_qty'])) {
    $cart_id = $_GET['update_manual_qty'];
    $new_qty = intval($_GET['qty']);

    if ($new_qty >= 1) {
        mysqli_query($conn, "UPDATE cart SET jumlah = $new_qty WHERE id = $cart_id");
    }
    header("Location: index.php");
    exit();
}

// D. HAPUS CART ITEM
if (isset($_GET['hapus_cart'])) {
    $cart_id = $_GET['hapus_cart'];
    mysqli_query($conn, "DELETE FROM cart WHERE id = $cart_id");
    header("Location: index.php");
    exit();
}

// E. HAPUS HANYA ITEM YANG DIBAYAR
if (isset($_POST['checkout_success'])) {
    if (!empty($_POST['selected_cart_ids'])) {
        $selected_ids = implode(',', array_map('intval', explode(',', $_POST['selected_cart_ids'])));
        mysqli_query($conn, "DELETE FROM cart WHERE id IN ($selected_ids)");
    }
    header("Location: index.php?checkout=done");
    exit();
}

// Ambil Data Produk Untuk Mode Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
    $edit_data = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database Integration (Sesi 9)</title>
  <style>
    * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background-color: #f0f2f5; margin: 0; padding: 30px; }
    h2 { color: #333; margin-bottom: 20px; text-align: center; }
    
    .main-container { display: flex; gap: 25px; align-items: flex-start; }
    
    .card-box { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .side-panel { width: 360px; }
    
    .product-grid { flex: 1; display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
    .product-card { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); display: flex; flex-direction: column; }
    .product-img { width: 100%; height: 160px; object-fit: cover; background-color: #e9ecef; }
    .product-details { padding: 15px; flex: 1; display: flex; flex-direction: column; }
    .product-title { font-weight: bold; font-size: 1.1em; margin-bottom: 5px; color: #2c3e50; }
    .product-desc { font-size: 0.9em; color: #6c757d; margin-bottom: 10px; flex: 1; }
    .product-price { font-weight: bold; color: #28a745; font-size: 1.1em; margin-bottom: 12px; }
    
    label { font-size: 0.9em; font-weight: 600; color: #495057; display: block; margin-top: 10px; }
    input[type="text"], input[type="number"], textarea, input[type="file"] {
      width: 100%; padding: 8px 10px; margin-top: 5px; border: 1px solid #ced4da; border-radius: 5px; font-size: 0.9em;
    }
    
    .btn { display: inline-block; width: 100%; padding: 9px; text-align: center; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; font-size: 0.9em; text-decoration: none; margin-top: 6px; }
    .btn-submit { background-color: #28a745; color: white; }
    .btn-submit:hover { background-color: #218838; }
    .btn-cart { background-color: #007bff; color: white; }
    .btn-cart:hover { background-color: #0069d9; }
    .btn-edit { background-color: #ffc107; color: #212529; }
    .btn-delete { background-color: #dc3545; color: white; }
    .btn-cancel { background-color: #6c757d; color: white; }
    .btn-checkout { background-color: #28a745; color: white; margin-top: 15px; font-size: 1em; padding: 11px; }
    .action-group { display: flex; gap: 5px; }

    /* Cart Section & Checkbox */
    .cart-title-center { text-align: center; margin-top: 0; margin-bottom: 15px; color: #2c3e50; }
    .cart-header-select { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e9ecef; padding-bottom: 8px; margin-bottom: 10px; font-size: 0.9em; font-weight: 600; }
    .cart-item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #dee2e6; padding: 10px 0; font-size: 0.9em; }
    .qty-control { display: flex; align-items: center; gap: 4px; margin-top: 4px; }
    .btn-qty { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 4px; border: 1px solid #ced4da; background: #f8f9fa; color: #333; text-decoration: none; font-weight: bold; line-height: 1; cursor: pointer; }
    .btn-qty:hover { background: #e2e6ea; }
    
    /* Style Khusus Input Angka Qty */
    .input-qty-manual {
      width: 45px !important;
      height: 24px !important;
      padding: 0 !important;
      margin: 0 !important;
      text-align: center;
      font-weight: 600;
      font-size: 0.9em;
      border: 1px solid #ced4da;
      border-radius: 4px;
      appearance: textfield;
      -webkit-appearance: none;
      -moz-appearance: textfield;
    }
    .input-qty-manual::-webkit-outer-spin-button,
    .input-qty-manual::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    .cart-total { margin-top: 15px; padding-top: 10px; border-top: 2px solid #343a40; font-weight: bold; display: flex; justify-content: space-between; font-size: 1.05em; }

    .item-checkbox, .select-all-checkbox { width: 18px; height: 18px; cursor: pointer; accent-color: #28a745; }

    /* Modal Styling */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: none; justify-content: center; align-items: center; z-index: 9999; }
    .modal-card { background: #fff; width: 380px; padding: 25px; border-radius: 12px; text-align: center; box-shadow: 0 8px 24px rgba(0,0,0,0.2); animation: popIn 0.25s ease-out; }
    @keyframes popIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    /* Success Icon Circle */
    .success-circle { width: 70px; height: 70px; background-color: #d4edda; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; border: 2px solid #28a745; }
    .success-circle svg { width: 40px; height: 40px; fill: #28a745; }

    /* Payment Option Box */
    .payment-option { display: flex; align-items: center; gap: 12px; border: 1px solid #ced4da; padding: 12px; border-radius: 8px; margin-bottom: 10px; cursor: pointer; text-align: left; }
    .payment-option:hover { border-color: #007bff; background: #f8f9fa; }
    .payment-option input[type="radio"] { width: 18px; height: 18px; cursor: pointer; }
  </style>
</head>
<body>

  <h2>Kilaura.id</h2>

  <div class="main-container">
    
    <!-- 1. FORM CREATE / UPDATE PRODUK -->
    <div class="card-box side-panel">
      <h3><?= $edit_data ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h3>
      
      <form action="" method="POST" enctype="multipart/form-data">
        <?php if ($edit_data): ?>
          <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
        <?php endif; ?>

        <label>Nama Produk:</label>
        <input type="text" name="nama_produk" value="<?= $edit_data ? htmlspecialchars($edit_data['nama_produk']) : ''; ?>" required>

        <label>Deskripsi Produk:</label>
        <textarea name="deskripsi" rows="3" required><?= $edit_data ? htmlspecialchars($edit_data['deskripsi']) : ''; ?></textarea>

        <label>Harga Produk (Rp):</label>
        <input type="number" name="harga" value="<?= $edit_data ? $edit_data['harga'] : ''; ?>" required>

        <label>Upload Gambar <?= $edit_data ? '(Opsional)' : ''; ?>:</label>
        <input type="file" name="gambar" accept="image/*">

        <button type="submit" name="<?= $edit_data ? 'edit_produk' : 'tambah_produk'; ?>" class="btn btn-submit">
          <?= $edit_data ? 'Simpan Perubahan' : 'Tambah Produk'; ?>
        </button>

        <?php if ($edit_data): ?>
          <a href="index.php" class="btn btn-cancel">Batal Edit</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- 2. READ & DISPLAY KATALOG PRODUK -->
    <div class="product-grid">
      <?php
      $query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
      if (mysqli_num_rows($query) > 0):
        while ($p = mysqli_fetch_assoc($query)):
      ?>
          <div class="product-card">
            <img src="uploads/<?= $p['gambar']; ?>" class="product-img" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'160\'><rect width=\'100%\' height=\'100%\' fill=\'%23e9ecef\'/><text x=\'50%\' y=\'50%\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%236c757d\'>No Image</text></svg>'">
            <div class="product-details">
              <div class="product-title"><?= htmlspecialchars($p['nama_produk']); ?></div>
              <div class="product-desc"><?= htmlspecialchars($p['deskripsi']); ?></div>
              <div class="product-price">Rp<?= number_format($p['harga'], 0, ',', '.'); ?></div>
              
              <a href="index.php?tambah_cart=<?= $p['id']; ?>" class="btn btn-cart">+ Keranjang</a>
              
              <div class="action-group">
                <a href="index.php?edit=<?= $p['id']; ?>" class="btn btn-edit">Edit</a>
                <a href="index.php?hapus=<?= $p['id']; ?>" onclick="return confirm('Yakin ingin menghapus produk ini?')" class="btn btn-delete">Hapus</a>
              </div>
            </div>
          </div>
      <?php 
        endwhile;
      else:
      ?>
        <p>Belum ada produk tersimpan di database.</p>
      <?php endif; ?>
    </div>

    <!-- 3. SHOPPING CART -->
    <div class="card-box side-panel">
      <h3 class="cart-title-center">Keranjang Belanja 🛒</h3>
      
      <?php
      $cart_query = mysqli_query($conn, "
        SELECT cart.id as cart_id, cart.jumlah, products.nama_produk, products.harga, (cart.jumlah * products.harga) as subtotal 
        FROM cart 
        JOIN products ON cart.product_id = products.id
      ");

      $has_items = mysqli_num_rows($cart_query) > 0;

      if ($has_items):
      ?>
        <!-- OPTION PILIH SEMUA -->
        <div class="cart-header-select">
          <span>Pilih Semua</span>
          <input type="checkbox" id="selectAll" class="select-all-checkbox" checked onclick="toggleSelectAll(this)">
        </div>

        <?php
        while ($c = mysqli_fetch_assoc($cart_query)):
        ?>
            <div class="cart-item">
              <div>
                <strong><?= htmlspecialchars($c['nama_produk']); ?></strong><br>
                <div class="qty-control">
                  <?php if ($c['jumlah'] > 1): ?>
                    <a href="index.php?update_qty=<?= $c['cart_id']; ?>&action=minus" class="btn-qty">-</a>
                  <?php else: ?>
                    <button type="button" class="btn-qty" onclick="confirmDeleteCart(<?= $c['cart_id']; ?>, '<?= htmlspecialchars(addslashes($c['nama_produk'])); ?>')">-</button>
                  <?php endif; ?>

                  <!-- INPUT QTY BISA DI-KLIK & DIKETIK MANUAL -->
                  <input type="number" 
                         class="input-qty-manual" 
                         value="<?= $c['jumlah']; ?>" 
                         min="1" 
                         onchange="changeQtyManual(<?= $c['cart_id']; ?>, this.value)">

                  <a href="index.php?update_qty=<?= $c['cart_id']; ?>&action=plus" class="btn-qty">+</a>
                  <span style="color:#6c757d; margin-left:4px;">x Rp<?= number_format($c['harga'], 0, ',', '.'); ?></span>
                </div>
              </div>

              <!-- HARGA & CHECKBOX DI SEBELAH KANAN -->
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-weight:bold;">Rp<?= number_format($c['subtotal'], 0, ',', '.'); ?></span>
                <input type="checkbox" 
                       class="item-checkbox" 
                       data-cart-id="<?= $c['cart_id']; ?>" 
                       data-subtotal="<?= $c['subtotal']; ?>" 
                       checked 
                       onchange="calculateTotal()">
              </div>
            </div>
        <?php 
        endwhile;
      else:
      ?>
        <p style="color:#888; font-size:0.9em; text-align:center;">Keranjang masih kosong.</p>
      <?php endif; ?>

      <div class="cart-total">
        <span>Total:</span>
        <span id="displayTotal" style="color:#28a745;">Rp0</span>
      </div>

      <?php if ($has_items): ?>
        <button type="button" class="btn btn-checkout" onclick="openPaymentModal()">Checkout Sekarang</button>
      <?php endif; ?>
    </div>

  </div>

  <!-- MODAL 1: KONFIRMASI HAPUS DARI KERANJANG -->
  <div class="modal-overlay" id="modalDeleteCart">
    <div class="modal-card">
      <h3 style="margin-top:0; color:#dc3545;">Hapus Item?</h3>
      <p id="deleteCartText">Yakin ingin menghapus produk dari daftar keranjang?</p>
      <div style="display:flex; gap:10px; margin-top:20px;">
        <button type="button" class="btn btn-cancel" onclick="closeModal('modalDeleteCart')">Batal</button>
        <a id="btnConfirmDeleteCart" href="#" class="btn btn-delete" style="margin-top:0;">Ya, Hapus</a>
      </div>
    </div>
  </div>

  <!-- MODAL 2: PILIHAN PEMBAYARAN -->
  <div class="modal-overlay" id="modalPayment">
    <div class="modal-card">
      <h3 style="margin-top:0; color:#2c3e50;">Pilih Metode Pembayaran</h3>
      <p style="font-size:0.9em; color:#6c757d; margin-bottom:15px;">Total Tagihan: <strong id="paymentTotal" style="color:#28a745;"></strong></p>
      
      <form id="formPayment" action="" method="POST" onsubmit="handlePaymentSubmit(event)">
        <input type="hidden" name="checkout_success" value="1">
        <input type="hidden" name="selected_cart_ids" id="selectedCartIds">

        <label class="payment-option">
          <input type="radio" name="payment_method" value="qris" checked onclick="togglePaymentDetail('qris')">
          <div>
            <strong>QRIS (Scan Barcode)</strong><br>
            <small style="color:#6c757d;">Bisa BCA, GoPay, OVO, Dana, dll</small>
          </div>
        </label>

        <label class="payment-option">
          <input type="radio" name="payment_method" value="cod" onclick="togglePaymentDetail('cod')">
          <div>
            <strong>COD (Bayar di Tempat)</strong><br>
            <small style="color:#6c757d;">Bayar tunai saat kurir sampai</small>
          </div>
        </label>

        <!-- Preview QRIS -->
        <div id="qrisBox" style="margin: 15px 0; text-align:center;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=KILAURA-STORE-SESSION9" alt="QRIS Code" style="border:1px solid #ddd; padding:5px; border-radius:8px;">
          <p style="font-size:0.8em; color:#6c757d; margin:5px 0 0 0;">Scan QR diatas untuk bayar</p>
        </div>

        <div style="display:flex; gap:10px; margin-top:20px;">
          <button type="button" class="btn btn-cancel" onclick="closeModal('modalPayment')">Batal</button>
          <button type="submit" class="btn btn-submit" style="margin-top:0;">Bayar Sekarang</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL 3: PEMBAYARAN BERHASIL -->
  <div class="modal-overlay" id="modalSuccess">
    <div class="modal-card">
      <div class="success-circle">
        <svg viewBox="0 0 24 24">
          <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
        </svg>
      </div>
      <h3 style="margin:0; color:#28a745;">PEMBAYARAN BERHASIL!</h3>
      <p style="font-size:0.9em; color:#6c757d; margin:10px 0 20px 0;">Pesanan Kilaura.id kamu sedang diproses. Terima kasih sudah berbelanja!</p>
      <button type="button" class="btn btn-submit" onclick="closeSuccessAndReload()">Selesai</button>
    </div>
  </div>

  <script>
    let currentGrandTotal = 0;
    let selectedIdsArray = [];

    // 1. Update Qty Manual via Input Ketik
    function changeQtyManual(cartId, val) {
      const qty = parseInt(val);
      if (isNaN(qty) || qty < 1) {
        alert('Jumlah produk minimal 1!');
        window.location.reload();
        return;
      }
      window.location.href = "index.php?update_manual_qty=" + cartId + "&qty=" + qty;
    }

    // 2. Kalkulasi Dynamic Total berdasarkan Checkbox
    function calculateTotal() {
      const checkboxes = document.querySelectorAll('.item-checkbox');
      const selectAllCb = document.getElementById('selectAll');
      
      let total = 0;
      let checkedCount = 0;
      selectedIdsArray = [];

      checkboxes.forEach(cb => {
        if (cb.checked) {
          total += parseInt(cb.getAttribute('data-subtotal'));
          selectedIdsArray.push(cb.getAttribute('data-cart-id'));
          checkedCount++;
        }
      });

      currentGrandTotal = total;
      document.getElementById('displayTotal').innerText = 'Rp' + total.toLocaleString('id-ID');

      if (selectAllCb) {
        selectAllCb.checked = (checkedCount === checkboxes.length && checkboxes.length > 0);
      }
    }

    // 3. Toggle Pilih Semua Checkbox
    function toggleSelectAll(master) {
      const checkboxes = document.querySelectorAll('.item-checkbox');
      checkboxes.forEach(cb => {
        cb.checked = master.checked;
      });
      calculateTotal();
    }

    // 4. Modal Hapus Cart
    function confirmDeleteCart(cartId, productName) {
      document.getElementById('deleteCartText').innerText = "Yakin ingin menghapus '" + productName + "' dari daftar keranjang?";
      document.getElementById('btnConfirmDeleteCart').href = "index.php?hapus_cart=" + cartId;
      document.getElementById('modalDeleteCart').style.display = 'flex';
    }

    // 5. Modal Payment Validasi Checkbox
    function openPaymentModal() {
      if (selectedIdsArray.length === 0) {
        alert('Pilih minimal 1 produk di keranjang untuk di-checkout!');
        return;
      }
      document.getElementById('paymentTotal').innerText = 'Rp' + currentGrandTotal.toLocaleString('id-ID');
      document.getElementById('selectedCartIds').value = selectedIdsArray.join(',');
      document.getElementById('modalPayment').style.display = 'flex';
    }

    function togglePaymentDetail(method) {
      const qrisBox = document.getElementById('qrisBox');
      qrisBox.style.display = (method === 'qris') ? 'block' : 'none';
    }

    // 6. Submit Payment
    function handlePaymentSubmit(e) {
      e.preventDefault();
      document.getElementById('modalPayment').style.display = 'none';
      document.getElementById('modalSuccess').style.display = 'flex';
    }

    function closeSuccessAndReload() {
      document.getElementById('formPayment').submit();
    }

    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
      calculateTotal();
    });
  </script>

</body>
</html>
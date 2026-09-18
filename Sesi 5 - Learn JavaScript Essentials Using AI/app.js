// 📦 DATABASE MINI: Buku catatan isi daftar barang beserta detailnya
const dataSkincare = [
  {
    nama: "Skintific 5X Ceramide Moisturizer",
    harga: 89000,
    deskripsi: "Menjaga kelembapan & memperbaiki skin barrier.",
    gambar: "assets/moisturizer.jpeg",
    kategori: "moisturizer",
  },
  {
    nama: "Azarine Sunscreen SPF 35 PA+++",
    harga: 65000,
    deskripsi: "Proteksi maksimal dari sinar UVA & UVB.",
    gambar: "assets/sunscreen.jpeg",
    kategori: "sunscreen",
  },
  {
    nama: "GLOW fx glow BOMB Serum",
    harga: 120000,
    deskripsi: "Mencerahkan kulit dan menyamarkan noda hitam.",
    gambar: "assets/serum.jpeg",
    kategori: "serum",
  },
  {
    nama: "BIOAQUA 7X Ceramide Toner",
    harga: 75000,
    deskripsi: "Menseimbangkan pH kulit dan memberi hidrasi ekstra.",
    gambar: "assets/toner.jpeg",
    kategori: "toner",
  },
  {
    nama: "COSRX Low PH Face Wash",
    harga: 55000,
    deskripsi: "Membersihkan wajah tanpa bikin kulit tertarik.",
    gambar: "assets/facewash.jpeg",
    kategori: "facewash",
  },
];

// 🧲 PENANGKAP ELEMEN: Mengambil wadah dan tombol dari HTML menggunakan ID/Class
const produkContainer = document.querySelector("#produk-container");
const filterWrapper = document.querySelector(".filter-wrapper");
const filterButtons = document.querySelectorAll(".btn-filter");

// 🏷️ CETAKAN RUPIAH: Mesin cetak format uang (dibuat sekali di memori biar efisien)
const formatRupiah = new Intl.NumberFormat("id-ID", {
  style: "currency",
  currency: "IDR",
  maximumFractionDigits: 0,
});

// 💵 FUNGSI PENERJEMAH HARGA: Masukkan angka biasa, keluar format Rupiah (cth: Rp 89.000)
function formatCurrency(amount) {
  return formatRupiah.format(amount);
}

// 🛠️ TUKANG RAKIT KARTU: Buat 1 elemen elemen div baru, isi dengan foto + teks, lalu kembalikan hasilnya
function createCardHTML(produk) {
  const card = document.createElement("div");
  card.className = "card-skincare";
  card.innerHTML = `
    <img src="${produk.gambar}" alt="${produk.nama}" />
    <h3>${produk.nama}</h3>
    <p class="deskripsi">${produk.deskripsi}</p>
    <p class="harga">${formatCurrency(produk.harga)}</p>
  `;
  return card;
}

// 🚚 MANDOR RAK PRODUK: Kosongkan rak layar, lalu suruh 'Tukang Rakit' bikin kartu satu per satu dan tempelkan
function tampilkanProduk(listProduk) {
  produkContainer.innerHTML = ""; // Lap bersih rak lama
  listProduk.forEach((produk) => {
    const card = createCardHTML(produk); // Rakit kartu
    produkContainer.appendChild(card); // Pajang kartu di rak
  });
}

// 🎛️ SAKELAR FILTER: Saring barang sesuai tombol yang diklik user
function handleFilterClick(e) {
  const targetBtn = e.target.closest(".btn-filter");
  if (!targetBtn) return; // Kalau yang diklik bukan tombol, abaikan

  // Matikan lampu 'active' di semua tombol, lalu nyalakan cuma di tombol yang diklik
  filterButtons.forEach((btn) => btn.classList.remove("active"));
  targetBtn.classList.add("active");

  // Ambil cap kategori dari tombol, lalu saring daftar barangnya
  const kategoriDipilih = targetBtn.getAttribute("data-kategori");
  const hasilFilter =
    kategoriDipilih === "semua"
      ? dataSkincare // Tampilkan semua tanpa disaring
      : dataSkincare.filter((produk) => produk.kategori === kategoriDipilih); // Ambil yang kategorinya cocok aja

  tampilkanProduk(hasilFilter); // Pajang barang hasil saringan ke layar
}

// 🚀 TOMBOL START: Nyalakan indikator awal dan tampilkan semua barang pas web pertama dibuka
if (filterButtons.length > 0) {
  filterButtons[0].classList.add("active"); // Nyalakan lampu tombol 'Semua'
}
tampilkanProduk(dataSkincare); // Render awal
filterWrapper.addEventListener("click", handleFilterClick); // Pasang pendengar klik di area tombol

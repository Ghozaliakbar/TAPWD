<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/Responsipwd/login dan register/login.php"); // Arahkan ke login jika belum login
    exit();
}


// Koneksi ke database
$servername = "localhost";
$username = "root"; // Ubah jika username database Anda berbeda
$password = ""; // Ubah jika password database Anda berbeda
$dbname = "wisata"; // Nama database Anda

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Logika pencarian
$search_name = isset($_GET['search_name']) ? $_GET['search_name'] : '';
$search_min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : NULL;
$search_max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : NULL;

// Query SQL Dinamis
$sql = "SELECT id, image, title, description, price FROM packages WHERE 1=1";

// Tambahkan filter nama jika ada input
if (!empty($search_name)) {
    $escaped_search_name = $conn->real_escape_string($search_name);
    $sql .= " AND title LIKE '%" . $escaped_search_name . "%'";
}

// Tambahkan filter harga minimum jika ada input
if (!is_null($search_min_price)) {
    $sql .= " AND price >= " . $search_min_price;
}

// Tambahkan filter harga maksimum jika ada input
if (!is_null($search_max_price)) {
    $sql .= " AND price <= " . $search_max_price;
}

// Eksekusi Query
$result = $conn->query($sql);

// Ambil hasil pencarian
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    $data = []; // Jika tidak ada hasil
}

// Ambil nama pengguna berdasarkan user_id dari session
$user_name = '';
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']); // Pastikan user_id berupa integer
    $sql_user = "SELECT Nama FROM user WHERE ID = $user_id"; // Sesuaikan kolom ID dan Nama
    $result_user = $conn->query($sql_user);

    if ($result_user && $result_user->num_rows > 0) {
        $user_row = $result_user->fetch_assoc();
        $user_name = $user_row['Nama']; // Gunakan kolom 'Nama'
    }
}


// Tutup koneksi database
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Packages</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="#home" class="logo">
                <img src="/Responsipwd/asset/asind/wfi.png" alt="Logo" class="putih" />
            </a>
            <?php if (!empty($user_name)): ?>
            <span class="user-greeting">Hallo, <?= htmlspecialchars($user_name); ?>!</span>
            <?php endif; ?>
            <nav>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="keranjang.php">Keranjang</a></li>
                    <li><a href="history.php">Histori</a></li>
                    <li><a href="profile.php">Profile</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>

    <section id="search" class="search-section">
            <div class="container">
                <form method="GET" action="" class="search-form">
                    <div class="form-group">
                        <label for="search_name">Nama:</label>
                        <input type="text" id="search_name" name="search_name" placeholder="Cari berdasarkan nama...">
                    </div>
                    <div class="form-group">
                        <label for="min_price">Harga Min:</label>
                        <input type="number" id="min_price" name="min_price" placeholder="Harga minimum">
                    </div>
                    <div class="form-group">
                        <label for="max_price">Harga Max:</label>
                        <input type="number" id="max_price" name="max_price" placeholder="Harga maksimum">
                    </div>
                    <button type="submit" class="btn-search">Cari</button>
                    <?php if (!empty($search_name) || (!is_null($search_min_price)) > 0 || (!is_null($search_max_price)) < PHP_INT_MAX): ?>
                        <!-- Tampilkan tombol "Tampilkan Semua" hanya jika ada pencarian -->
                        <a href="?" class="btn-reset">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
        </section>

        
    <section id="packages" class="packages-section">
    <div class="packages">
        <?php foreach ($data as $item): ?>
        <div class="package-card">
            <img src="<?= $item['image'] ?>" alt="<?= $item['title'] ?>" class="card-image">
            <div class="card-content">
                <h3><?= $item['title'] ?></h3>
                <p><?= $item['description'] ?></p>
                <div class="card-footer">
                    <a href="detail.php?id=<?= $item['id'] ?>" class="btn-booking">Pesan</a>
                    <span class="price">Rp. <?= number_format($item['price'], 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
    </main>

    <footer>
        <p>&copy; 2024.Noval Lias Ramadani-2200018083.</p>
    </footer>
    <script>
        // Mendapatkan elemen header
        const header = document.querySelector('header');

        // Menambahkan event listener untuk mendeteksi scroll
        window.addEventListener('scroll', () => {
            // Jika scroll lebih dari 0, tambahkan kelas sticky
            if (window.scrollY > 0) {
                header.classList.add('sticky');
            } else {
                // Jika scroll kembali ke atas, hapus kelas sticky
                header.classList.remove('sticky');
            }
        });
    </script>
</body>
</html>

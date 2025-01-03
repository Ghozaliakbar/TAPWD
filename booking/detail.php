<?php
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

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data berdasarkan ID
$sql = "SELECT * FROM packages WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $package = $result->fetch_assoc();
} else {
    echo "Paket tidak ditemukan.";
    exit;
}

// Menambahkan ke keranjang
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    session_start();  // Memulai sesi untuk menyimpan ID pengguna
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];  // Ambil ID pengguna dari sesi
        $package_id = (int)$_GET['id'];
        
        // Cek apakah paket sudah ada di keranjang
        $check_query = "SELECT * FROM cart WHERE user_id = $user_id AND package_id = $package_id";
        $check_result = $conn->query($check_query);
        
        if ($check_result->num_rows > 0) {
            // Jika sudah ada, update quantity
            $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = $user_id AND package_id = $package_id";
            $conn->query($update_query);
        } else {
            // Jika belum ada, insert data baru ke cart
            $insert_query = "INSERT INTO cart (user_id, package_id, quantity) VALUES ($user_id, $package_id, 1)";
            $conn->query($insert_query);
        }

        // Redirect kembali ke halaman keranjang
        header("Location: keranjang.php");
        exit();
    } else {
        echo "Anda harus login terlebih dahulu.";
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
    <title>Detail <?= $package['title'] ?></title>
    <link rel="stylesheet" href="detail.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <img src="/Responsipwd/asset/asind/wfi.png" alt="Logo">
            </a>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="keranjang.php">Keranjang</a></li>
                    <li><a href="histori.php">Histori</a></li>
                    <li><a href="profile.php">Profile</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="about-section">
            <div class="about-image">
                <img src="<?= $package['image'] ?>" alt="<?= $package['title'] ?>">
            </div>
            <div class="about-content">
                <h1><?= $package['title'] ?></h1>
                <p><?= $package['description'] ?></p>
                <p><strong>Harga: </strong>Rp<?= number_format($package['price'], 0, ',', '.') ?></p>
                <div class="button-group">
                    <a href="bookingorder.php?checkout_now=<?= $package['id'] ?>" class="btn-order">Pesan Sekarang</a>
                    <a href="detail.php?action=add&id=<?= $package['id'] ?>" class="btn-cart">Tambah Keranjang</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024. Noval Lias Ramadani-2200018083</p>
    </footer>
</body>
</html>

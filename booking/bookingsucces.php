<?php
session_start();

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wisata";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data user yang login dari tabel user
$user_id = $_SESSION['user_id']; // Pastikan user sudah login
$sql_user = "SELECT Nama AS name, Email AS email FROM user WHERE ID = $user_id";
$result_user = $conn->query($sql_user);

// Jika user ditemukan
if ($result_user && $result_user->num_rows > 0) {
    $user_info = $result_user->fetch_assoc();
} else {
    die("User tidak ditemukan.");
}

// Periksa apakah ada parameter checkout_now
$checkout_now = isset($_GET['checkout_now']) ? intval($_GET['checkout_now']) : null;
$cart_items = [];
$total_price = 0;

if ($checkout_now) {
    // Jika pengguna memilih "Checkout Now"
    $sql = "SELECT packages.id AS package_id, packages.title, packages.price, packages.image
            FROM packages
            WHERE packages.id = $checkout_now";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['quantity'] = 1; // Set jumlah menjadi 1 untuk checkout now
            $cart_items[] = $row;
            $total_price += $row['price']; // Total hanya untuk item checkout now
        }
    } else {
        die("Item yang dipilih tidak valid atau tidak tersedia.");
    }
} else {
    // Jika pengguna melakukan checkout seluruh keranjang
    $sql = "SELECT cart.id AS cart_id, packages.id AS package_id, packages.title, packages.price, packages.image, cart.quantity
            FROM cart
            JOIN packages ON cart.package_id = packages.id
            WHERE cart.user_id = $user_id";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cart_items[] = $row;
            $total_price += $row['price'] * $row['quantity']; // Hitung total harga
        }
    } else {
        die("Keranjang Anda kosong. Silakan tambahkan produk untuk melanjutkan.");
    }
}

foreach ($cart_items as $item) {
    $package_id = $item['package_id'];
    $quantity = $item['quantity'];
    $total_price = $item['price'] * $quantity;
    $order_date = date('Y-m-d H:i:s');

    $sql_insert = "INSERT INTO history (user_id, package_id, order_date, quantity, total_price) 
                   VALUES ($user_id, $package_id, '$order_date', $quantity, $total_price)";
    $conn->query($sql_insert);
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f7;
        }
        .success-header {
            background: linear-gradient(90deg, #28a745, #34d058);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 15px 15px;
        }
        .success-header h1 {
            font-size: 2.8rem;
            font-weight: bold;
        }
        .success-header p {
            margin-top: 10px;
            font-size: 1.2rem;
        }
        .ticket-container {
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            max-width: 900px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .ticket-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
        }
        .ticket-header h3 {
            font-size: 1.8rem;
            color: #28a745;
            margin-bottom: 15px;
        }
        .ticket-summary img {
            border-radius: 10px;
            max-width: 120px;
            margin-right: 20px;
        }
        .btn-print, .btn-home {
            padding: 15px 30px;
            font-size: 1.2rem;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-print {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: white;
            border: none;
        }
        .btn-print:hover {
            background: linear-gradient(90deg, #0056b3, #00408f);
        }
        .btn-home {
            background: linear-gradient(90deg, #6c757d, #5a6268);
            color: white;
            border: none;
            margin-left: 10px;
            padding: 18px 35px;
        }
        .btn-home:hover {
            background: linear-gradient(90deg, #5a6268, #495057);
        }
        .summary-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .summary-item .details {
            flex-grow: 1;
        }
        .summary-item .details p {
            margin: 0;
        }
        .total-price {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: right;
            color: #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="success-header">
        <h1><i class="fas fa-check-circle"></i> Pesanan Berhasil!</h1>
        <p>Terima kasih telah memesan melalui sistem kami. Tiket Anda siap dicetak.</p>
    </header>

    <!-- Main Content -->
    <div class="container ticket-container">
        <!-- Informasi Pemesan -->
        <div class="ticket-header">
            <h3><i class="fas fa-user-circle"></i> Informasi Pemesan</h3>
            <p><strong>Nama:</strong> <?= htmlspecialchars($user_info['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user_info['email']) ?></p>
        </div>

        <!-- Ringkasan Pesanan -->
        <div class="ticket-summary">
            <h3><i class="fas fa-shopping-cart"></i> Ringkasan Pesanan</h3>
            <?php foreach ($cart_items as $item): ?>
                <div class="summary-item">
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="img-thumbnail">
                    <div class="details">
                        <p><strong><?= htmlspecialchars($item['title']) ?></strong></p>
                        <p>Jumlah: <?= htmlspecialchars($item['quantity']) ?></p>
                        <p>Harga Satuan: Rp<?= number_format($item['price'], 0, ',', '.') ?></p>
                    </div>
                    <p class="total">Rp<?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></p>
                </div>
            <?php endforeach; ?>
            <hr>
            <p class="total-price">Total Harga: Rp<?= number_format($total_price, 0, ',', '.') ?></p>
        </div>

        <!-- Tombol Cetak dan Kembali ke Beranda -->
        <div class="text-center">
            <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Cetak Tiket</button>
            <a href="main.php" class="btn-home"><i class="fas fa-home"></i>Beranda</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Noval Lias Ramadani. 2200018083.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
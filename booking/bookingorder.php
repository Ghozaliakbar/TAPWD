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

// Cek apakah ada parameter checkout_now
$checkout_now = isset($_GET['checkout_now']) ? intval($_GET['checkout_now']) : null;

if ($checkout_now) {
    // Ambil hanya data item yang dipilih untuk pesan sekarang
    $sql = "SELECT packages.id AS package_id, packages.title, packages.price, packages.image
            FROM packages
            WHERE packages.id = $checkout_now";

    $result = $conn->query($sql);

    if ($result->num_rows === 0) {
        die("Item yang dipilih tidak valid atau tidak tersedia.");
    }

    // Hanya satu item dengan jumlah 1 untuk pesan sekarang
    $cart_items = [];
    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = 1; // Set jumlah menjadi 1
        $cart_items[] = $row;
    }
    $total_price = $cart_items[0]['price']; // Total hanya untuk item pesan sekarang
} else {
    // Ambil semua data keranjang
    $sql = "SELECT cart.id AS cart_id, packages.id AS package_id, packages.title, packages.price, packages.image, cart.quantity
            FROM cart
            JOIN packages ON cart.package_id = packages.id
            WHERE cart.user_id = $user_id";

    $result = $conn->query($sql);

    // Total Harga
    $total_price = 0;
    $cart_items = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cart_items[] = $row;
            $total_price += $row['price'] * $row['quantity'];
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="bookingorder.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .checkout-header {
            font-size: 1.8rem;
            color: white;
            padding: 20px;
            text-align: center;
            color: white;
            background: linear-gradient(90deg,rgb(17, 173, 246),rgb(7, 175, 252));
            padding: 20px;
            border-radius: 0 0 15px 15px;
            opacity: 0.9;
        
        }
        .checkout-header h1 {
            font-size: 2rem;
            font-size: 2.8rem;
            font-weight: bold;
            color: #FFFF;
        }
        .checkout-section {
            margin-bottom: 20px;
        }
        .ringkasan-pesanan img {
            border-radius: 5px;
        }
        .btn-checkout {
            background-color: #28a745;
            border: none;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 10px 20px;
            transition: 0.3s ease;
        }
        .btn-checkout:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="checkout-header">
        <div class="container">
            <h1>Checkout</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mt-5">
        <div class="row">
            <!-- Informasi Pemesan -->
            <div class="col-lg-8 mb-4">
                <div class="p-4 bg-white rounded shadow-sm">
                    <h2 class="mb-3"><i class="fas fa-user-circle me-2"></i> Informasi Pemesan</h2>
                    <p><strong>Nama:</strong> <?= htmlspecialchars($user_info['name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user_info['email']) ?></p>
                    <a href="edit-profile.php" class="btn btn-sm btn-primary">Edit Informasi Pemesan</a>
                </div>

                <!-- Informasi Traveler -->
                <div class="checkout-section mt-4">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <h2 class="mb-3"><i class="fas fa-users me-2"></i> Informasi Traveler</h2>
                        <?php foreach ($cart_items as $item): ?>
                            <?php for ($i = 1; $i <= $item['quantity']; $i++): ?>
                                <div class="mb-4 border-bottom pb-3">
                                    <h5 class="mb-2"><?= htmlspecialchars($item['title']) ?> - Traveler <?= $i ?></h5>
                                    <form class="row g-3">
                                        <div class="col-md-6">
                                            <label for="traveler-firstname-<?= $i ?>" class="form-label">Nama Depan</label>
                                            <input type="text" class="form-control" id="traveler-firstname-<?= $i ?>" name="traveler_firstname[]" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="traveler-lastname-<?= $i ?>" class="form-label">Nama Belakang</label>
                                            <input type="text" class="form-control" id="traveler-lastname-<?= $i ?>" name="traveler_lastname[]" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="traveler-email-<?= $i ?>" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="traveler-email-<?= $i ?>" name="traveler_email[]" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="traveler-phone-<?= $i ?>" class="form-label">No Telepon</label>
                                            <input type="text" class="form-control" id="traveler-phone-<?= $i ?>" name="traveler_phone[]" required>
                                        </div>
                                    </form>
                                </div>
                            <?php endfor; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Pesanan -->
            <div class="col-lg-4">
                <div class="p-4 bg-white rounded shadow-sm ringkasan-pesanan">
                    <h2 class="mb-3"><i class="fas fa-shopping-cart me-2"></i> Ringkasan Pesanan</h2>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                <div>
                                    <p class="mb-0 fw-bold"><?= htmlspecialchars($item['title']) ?></p>
                                    <small>Rp<?= number_format($item['price'], 0, ',', '.') ?> × <?= $item['quantity'] ?></small>
                                </div>
                            </div>
                            <span class="text-end fw-bold">Rp<?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></span>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <p class="mb-0"><strong>Total:</strong></p>
                        <p class="mb-0"><strong>Rp<?= number_format($total_price, 0, ',', '.') ?></strong></p>
                    </div>
                    <!-- Tombol Lanjutkan Pembayaran -->
            <a href="#" id="checkout-button" class="btn btn-success btn-checkout w-100 mt-4" data-bs-toggle="modal" data-bs-target="#checkoutConfirmationModal">Lanjutkan Pembayaran</a>
                </div>
            </div>


             <!-- Modal Notifikasi -->
             <div class="modal fade" id="checkoutConfirmationModal" tabindex="-1" aria-labelledby="checkoutConfirmationLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="checkoutConfirmationLabel">Konfirmasi Checkout</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Anda akan melakukan checkout untuk pesanan ini.</p>
                            <div class="alert alert-info">
                                <strong>Total Pembayaran:</strong> Rp<?= number_format($total_price, 0, ',', '.') ?>
                            </div>
                            <p>Dengan melanjutkan, Anda setuju untuk membeli produk sesuai dengan ketentuan yang berlaku.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="confirmCheckoutButton">Ya, Checkout Sekarang</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-3 mt-4 border-top">
        <p class="mb-0">© 2024 Your Company. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('confirmCheckoutButton').addEventListener('click', function () {
            window.location.href = "bookingsucces.php";
        });
    </script>

</body>
</html>


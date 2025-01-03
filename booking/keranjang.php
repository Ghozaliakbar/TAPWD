<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Arahkan ke halaman login jika belum login
    exit();
}

$user_id = $_SESSION['user_id'];

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

// Ambil data keranjang pengguna
$sql = "SELECT cart.id, packages.title, packages.price, cart.quantity
        FROM cart
        JOIN packages ON cart.package_id = packages.id
        WHERE cart.user_id = $user_id";
$result = $conn->query($sql);

$total = 0;
// Proses update kuantitas
if (isset($_GET['action']) && isset($_GET['item_id']) && isset($_GET['action_type'])) {
    $item_id = (int)$_GET['item_id'];
    $action_type = $_GET['action_type'];

    if ($action_type == 'increase') {
        $update_sql = "UPDATE cart SET quantity = quantity + 1 WHERE id = $item_id AND user_id = $user_id";
        $conn->query($update_sql);
    } elseif ($action_type == 'decrease') {
        $update_sql = "UPDATE cart SET quantity = quantity - 1 WHERE id = $item_id AND user_id = $user_id AND quantity > 1";
        $conn->query($update_sql);
    }

    // Redirect untuk refresh halaman
    header("Location: keranjang.php");
    exit();
}

// Proses hapus item
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['item_id'])) {
    $item_id = (int)$_GET['item_id'];
    $delete_sql = "DELETE FROM cart WHERE id = $item_id AND user_id = $user_id";
    $conn->query($delete_sql);
    
    // Redirect setelah delete
    header("Location: keranjang.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="keranjang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <img src="/Responsipwd/asset/asind/wfi.png" alt="Logo">
            </a>
            <nav>
                <ul>
                    <li><a href="main.php">Home</a></li>
                    <li><a href="keranjang.php">Keranjang</a></li>
                    <li><a href="history.php">Histori</a></li>
                    <li><a href="profile.php">Profile</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="cart-section">
            <h1>Keranjang Belanja</h1>
            <?php if ($result->num_rows > 0): ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Paket</th>
                            <th>Harga</th>
                            <th>Kuantitas</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['title'] ?></td>
                                <td>Rp<?= number_format($row['price'], 0, ',', '.') ?></td>
                                <td>
                                    <div class="quantity-controls">
                                        <a href="keranjang.php?action=update&item_id=<?= $row['id'] ?>&action_type=decrease" class="btn-icon">
                                            <i class="fas fa-minus"></i>
                                        </a>
                                        <span><?= $row['quantity'] ?></span>
                                        <a href="keranjang.php?action=update&item_id=<?= $row['id'] ?>&action_type=increase" class="btn-icon">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>Rp<?= number_format($row['price'] * $row['quantity'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="keranjang.php?action=delete&item_id=<?= $row['id'] ?>" class="btn-icon delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php $total += $row['price'] * $row['quantity']; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="cart-total">
                    <p><strong>Total: </strong>Rp<?= number_format($total, 0, ',', '.') ?></p>
                    <a href="bookingorder.php" class="btn-checkout">Checkout</a>
                </div>
            <?php else: ?>
                <p class="empty-cart">Keranjang Anda kosong.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024. Noval Lias Ramadani-2200018083</p>
    </footer>
</body>
</html>


<?php
// Tutup koneksi database
$conn->close();
?>

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

// Ambil data user yang login dari session
$user_id = $_SESSION['user_id']; // Pastikan user sudah login

// Jika tombol "Clear All" ditekan
if (isset($_POST['clear_all'])) {
    $conn->query("DELETE FROM history WHERE user_id = $user_id");
    header("Location: history.php");
    exit;
}

// Jika tombol "Clear" pada suatu histori ditekan
if (isset($_POST['clear_item'])) {
    $history_id = $_POST['history_id'];
    $conn->query("DELETE FROM history WHERE id = $history_id AND user_id = $user_id");
    header("Location: history.php");
    exit;
}


$sql_history = "SELECT history.id, history.order_date, history.total_price, 
                history.quantity, packages.title, packages.price, packages.image
                FROM history
                JOIN packages ON history.package_id = packages.id
                WHERE history.user_id = $user_id
                ORDER BY history.order_date DESC";

$result_history = $conn->query($sql_history);

// Array untuk menyimpan data histori
$history_items = [];
if ($result_history && $result_history->num_rows > 0) {
    while ($row = $result_history->fetch_assoc()) {
        $history_items[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Pemesanan</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .history-header {
            text-align: center;
            margin: 30px auto;
            color: #007bff;
        }
        .history-header h1 {
            font-weight: bold;
        }
        .history-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            background-color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .history-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .history-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .history-card .card-body {
            padding: 15px 20px;
        }
        .history-card .card-body h5 {
            margin-bottom: 10px;
            font-size: 1.25rem;
            font-weight: bold;
            color: #007bff;
        }
        .history-card .info-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
        .history-card .info-group span {
            font-weight: 500;
        }
        .total-price {
            font-size: 1.1rem;
            font-weight: bold;
            color: #28a745;
            text-align: right;
        }
        .btn-back {
            text-align: center;
            margin: 20px 0;
        }
        .btn-back a {
            text-decoration: none;
            color: #fff;
            background: linear-gradient(90deg, #007bff, #0056b3);
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .btn-back a:hover {
            background: linear-gradient(90deg, #0056b3, #003f8c);
        }

        .footer {
            position: fixed center;
            margin-top: 100px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="history-header">
                <h1><i class="fas fa-history"></i> Histori Pemesanan</h1>
                <p>Riwayat perjalanan Anda tersedia di bawah ini.</p>
            </div>
            <form method="POST">
                <button type="submit" name="clear_all" class="btn btn-danger">Clear All</button>
            </form>
        </div>

        <?php if (count($history_items) > 0): ?>
            <div class="row g-4">
                <?php foreach ($history_items as $item): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card history-card">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="card-img-top">
                            <div class="card-body">
                                <h5><?= htmlspecialchars($item['title']) ?></h5>
                                <div class="info-group">
                                    <span><i class="far fa-calendar-alt"></i> <?= htmlspecialchars($item['order_date']) ?></span>
                                    <span><i class="fas fa-hashtag"></i> <?= htmlspecialchars($item['quantity']) ?> x</span>
                                </div>
                                <div class="info-group">
                                    <span>Harga Satuan:</span>
                                    <span>Rp<?= number_format($item['price'], 0, ',', '.') ?></span>
                                </div>
                                <p class="total-price">Total: Rp<?= number_format($item['total_price'], 0, ',', '.') ?></p>
                                <form method="POST" class="text-end">
                                    <input type="hidden" name="history_id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="clear_item" class="btn btn-sm btn-danger">Clear</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                Anda belum memiliki histori pemesanan.
            </div>
        <?php endif; ?>

        <div class="btn-back text-center">
            <a href="main.php"><i class="fas fa-home"></i> Kembali ke Beranda</a>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Noval Lias Ramadani. 2200018083.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

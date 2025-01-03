<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
$server = "localhost";
$username = "root";
$password = "";
$database = "wisata";

$conn = new mysqli($server, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil user_id dari sesi
$user_id = intval($_SESSION['user_id']);

// Ambil data pengguna dari tabel user dan detailuser
$sql = "SELECT u.Nama, u.Email, d.jenis_kelamin, d.nomor_hp, d.alamat 
        FROM user u 
        LEFT JOIN detailuser d ON u.ID = d.user_id 
        WHERE u.ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Perbarui data pengguna jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
    $nomor_hp = $_POST['nomor_hp'] ?? null;
    $alamat = $_POST['alamat'] ?? null;

    // Periksa apakah entri detailuser sudah ada
    $sql_check = "SELECT * FROM detailuser WHERE user_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $stmt_check->close();

    if ($result_check->num_rows > 0) {
        // Update data detailuser
        $sql_update = "UPDATE detailuser SET jenis_kelamin = ?, nomor_hp = ?, alamat = ? WHERE user_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $jenis_kelamin, $nomor_hp, $alamat, $user_id);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Tambahkan data baru ke detailuser
        $sql_insert = "INSERT INTO detailuser (user_id, jenis_kelamin, nomor_hp, alamat) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isss", $user_id, $jenis_kelamin, $nomor_hp, $alamat);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    // Refresh halaman untuk memperbarui data
    header("Location: profile.php");
    exit();
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Menambahkan Bootstrap dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- File CSS khusus -->
</head>
<body>
<header class="bg-primary text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <h1>Profile</h1>
        <a href="http://localhost/Responsipwd/login dan register/login.php" class="btn btn-danger">Logout</a>
    </div>
</header>
<main class="my-5">
    <div class="container">
        <div id="profile-card" class="card" style="display:none;">
            <div class="card-body">
                <h5 class="card-title">Profile Information</h5>
                <p><strong>Nama:</strong> <span id="profile-nama"><?= htmlspecialchars($user_data['Nama']); ?></span></p>
                <p><strong>Email:</strong> <span id="profile-email"><?= htmlspecialchars($user_data['Email']); ?></span></p>
                <p><strong>Jenis Kelamin:</strong> <span id="profile-jenis_kelamin"><?= $user_data['jenis_kelamin']; ?></span></p>
                <p><strong>Nomor HP:</strong> <span id="profile-nomor_hp"><?= htmlspecialchars($user_data['nomor_hp'] ?? ''); ?></span></p>
                <p><strong>Alamat:</strong> <span id="profile-alamat"><?= htmlspecialchars($user_data['alamat'] ?? ''); ?></span></p>
                <button id="update-btn" class="btn btn-warning" onclick="editProfile()">Update</button>
            </div>
        </div>

        <form id="profile-form" method="POST" action="" class="profile-form row g-3">
            <div class="col-md-6">
                <label for="nama" class="form-label">Nama:</label>
                <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($user_data['Nama']); ?>" disabled>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user_data['Email']); ?>" disabled>
            </div>
            <div class="col-md-6">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
                <select id="jenis_kelamin" name="jenis_kelamin" class="form-select">
                    <option value="Laki-laki" <?= $user_data['jenis_kelamin'] === 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="Perempuan" <?= $user_data['jenis_kelamin'] === 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="nomor_hp" class="form-label">Nomor HP:</label>
                <input type="text" id="nomor_hp" name="nomor_hp" class="form-control" value="<?= htmlspecialchars($user_data['nomor_hp'] ?? ''); ?>">
            </div>
            <div class="col-md-12">
                <label for="alamat" class="form-label">Alamat:</label>
                <textarea id="alamat" name="alamat" class="form-control"><?= htmlspecialchars($user_data['alamat'] ?? ''); ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success w-100">Simpan</button>
            </div>
        </form>
    </div>
</main>
<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2024. Sistem Wisata.</p>
</footer>

<!-- Menambahkan JavaScript untuk Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

<script>
    // Menyembunyikan form dan menampilkan card setelah submit
    const form = document.getElementById('profile-form');
    const profileCard = document.getElementById('profile-card');
    const profileForm = document.getElementById('profile-form');
    const profileData = {
        nama: document.getElementById('profile-nama'),
        email: document.getElementById('profile-email'),
        jenis_kelamin: document.getElementById('profile-jenis_kelamin'),
        nomor_hp: document.getElementById('profile-nomor_hp'),
        alamat: document.getElementById('profile-alamat')
    };

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Perbarui data profil dengan nilai dari form
        profileData.nama.innerText = document.getElementById('nama').value;
        profileData.email.innerText = document.getElementById('email').value;
        profileData.jenis_kelamin.innerText = document.getElementById('jenis_kelamin').value;
        profileData.nomor_hp.innerText = document.getElementById('nomor_hp').value;
        profileData.alamat.innerText = document.getElementById('alamat').value;

        // Sembunyikan form dan tampilkan card
        profileForm.style.display = 'none';
        profileCard.style.display = 'block';
    });

    // Fungsi untuk mengubah tampilan form menjadi mode edit
    function editProfile() {
        document.getElementById('nama').value = profileData.nama.innerText;
        document.getElementById('email').value = profileData.email.innerText;
        document.getElementById('jenis_kelamin').value = profileData.jenis_kelamin.innerText;
        document.getElementById('nomor_hp').value = profileData.nomor_hp.innerText;
        document.getElementById('alamat').value = profileData.alamat.innerText;

        profileForm.style.display = 'block';
        profileCard.style.display = 'none';
    }
</script>
</body>
</html>

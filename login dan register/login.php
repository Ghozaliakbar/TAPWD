<?php

session_start();

// Koneksi ke database MySQL
$server = "localhost";
$username = "root";
$password = "";
$database = "wisata";

$koneksi = new mysqli($server, $username, $password, $database);

// Memeriksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$status = "";

// Verifikasi CAPTCHA hanya jika ada data POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['g-recaptcha-response'])) {
    $captcha = $_POST['g-recaptcha-response']; // Ambil respons CAPTCHA

    // Periksa apakah CAPTCHA diisi
    if (empty($captcha)) {
        $status = "Please complete the CAPTCHA!";
    } else {
        // Verifikasi CAPTCHA ke server Google
        $secret_key = '6LcFb6YqAAAAAJTTMngI5R2P1OHXwyzhHFOFqByI'; // Pastikan ini adalah kunci rahasia yang valid
        $verify_response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$captcha");
        $response_data = json_decode($verify_response);

        // Periksa hasil verifikasi CAPTCHA
        if (!$response_data->success) {
            $status = "CAPTCHA verification failed!";
        }
    }
} else {
    $status = "CAPTCHA response is missing!";
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    if ($status === "") { // Lanjutkan hanya jika CAPTCHA lolos
        $nama = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];

        // Validasi server-side
        if (empty($nama) || empty($email) || empty($password)) {
            $status = "Semua field harus diisi!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $status = "Email harus sesuai format email!";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
            $status = "Password harus minimal 6 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter khusus!";
        } else {
            // Cek apakah email sudah terdaftar
            $query = "SELECT * FROM user WHERE Email = ?";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $status = "Email sudah terdaftar!";
            } else {
                // Hash password untuk keamanan
                $password_hashed = password_hash($password, PASSWORD_DEFAULT);

                // Menyimpan data pengguna baru
                $query = "INSERT INTO user (Nama, Email, Password) VALUES (?, ?, ?)";
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("sss", $nama, $email, $password_hashed);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $status = "Berhasil registrasi!";
                }

                $stmt->close();
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    if ($status === "") { // Hanya lanjutkan jika CAPTCHA lolos
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];

        // Validasi input login
        if (empty($email) || empty($password)) {
            $status = "Semua field harus diisi!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $status = "Email tidak valid!";
        } else {
            // Cek email dan password di database
            $query = "SELECT * FROM user WHERE Email = ?";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $akun = $result->fetch_assoc();
                if (password_verify($password, $akun['Password'])) {
                    // Login berhasil, simpan informasi pengguna dalam sesi
                    $_SESSION['user_id'] = $akun['ID'];
                    $_SESSION['user_name'] = $akun['Nama'];
                    $_SESSION['user_email'] = $akun['Email'];
                    $_SESSION['user_role'] = $akun['role'];  // Role pengguna (admin/user)

                    // Redirect sesuai dengan peran pengguna
                    if ($akun["role"] == "admin") {
                        header("Location: /////index.//php");
                        exit();
                    } elseif ($akun["role"] == "user") {
                        header("Location: /Responsipwd/booking/main.php");
                        exit();
                    }
                } else {
                    $status = "Password salah!";
                }
            } else {
                $status = "Akun tidak ditemukan!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="login.css">
    <title>Login & Registration</title>
</head>

<body>
    <div class="wrapper">
        <!-- Navigasi -->
        <nav class="nav">
            <div class="logo">
                <a href=""><img src="/Responsipwd/login dan register/asset/wfi putih.png" class="putih" /></a>
            </div>
            <div class="nav-button">
                <button class="btn white-btn" id="loginBtn" onclick="login()">Sign In</button>
                <button class="btn" id="registerBtn" onclick="register()">Sign Up</button>
            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>

    <div class="form-box">
    <!-- Login Form -->
    <div class="login-container" id="login">
        <div class="top">
            <span>Don't have an account? <a href="#" onclick="register()">Sign Up</a></span>
            <header>Login</header>
            <div class="echo <?php echo $status; ?>">
                <?php
                if ($status == "Berhasil registrasi!") {
                    echo "Berhasil registrasi!";
                } elseif ($status == "Akun tidak ditemukan!" || $status == "Password salah!") {
                    echo "Gagal Login!";
                } elseif ($status == "Email sudah terdaftar!") {
                    echo "Email sudah terdaftar!";
                }
                ?>
            </div>
        </div>
        <form action="" method="post" onsubmit="return validateLogin()">
            <div class="input-box">
                <input type="text" name="email" id="emailLogin" class="input-field" placeholder="Email" required>
                <i class="bx bx-envelope"></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" id="passwordLogin" class="input-field" placeholder="Password" required>
                <i class="bx bx-lock-alt"></i>
            </div>
            <div class="captcha-submit-container">
    <div class="captcha">
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="g-recaptcha" data-sitekey="6LcFb6YqAAAAAFJKLkM6q6UBgBhwtZBq1f26ZPr8"></div>
    </div>
    <input type="submit" class="submit" name="login" value="Sign In">
</div>

        </form>
    </div>

    <!-- Register Form -->
    <div class="register-container" id="register">
        <div class="top">
            <span>Have an account? <a href="#" onclick="login()">Login</a></span>
            <header>Sign Up</header>
        </div>
        <form action="" method="post" onsubmit="return validateRegister()">
            <div class="input-box">
                <input type="text" name="name" id="nameRegister" class="input-field" placeholder="Name" required>
                <i class="bx bx-user"></i>
            </div>
            <div class="input-box">
                <input type="text" name="email" id="emailRegister" class="input-field" placeholder="Email" required>
                <i class="bx bx-envelope"></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" id="passwordRegister" class="input-field" placeholder="Password" required>
                <i class="bx bx-lock-alt"></i>
            </div>
            <div class="captcha-submit-container">
                <div class="captcha">
                    <div class="g-recaptcha" data-sitekey="6LcFb6YqAAAAAFJKLkM6q6UBgBhwtZBq1f26ZPr8"></div>
                </div>
                <input type="submit" class="submit" name="register" value="Register">
            </div>
        </form>
    </div>
</div>


    <!-- JavaScript -->
    <script>
        var loginBtn = document.getElementById("loginBtn");
        var registerBtn = document.getElementById("registerBtn");
        var loginForm = document.getElementById("login");
        var registerForm = document.getElementById("register");

        function login() {
            loginForm.style.left = "4px";
            registerForm.style.right = "-520px";
            loginBtn.classList.add("white-btn");
            registerBtn.classList.remove("white-btn");
        }

        function register() {
            loginForm.style.left = "-510px";
            registerForm.style.right = "5px";
            loginBtn.classList.remove("white-btn");
            registerBtn.classList.add("white-btn");
        }

        function validateLogin() {
            var email = document.getElementById("emailLogin").value;
            var password = document.getElementById("passwordLogin").value;
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/;

            
            // if (email && password) {
            //     // Disable tombol submit agar tidak bisa di-submit dua kali
            //     document.querySelector('input[type="submit"]').disabled = true;
            // }
            
            // Validasi email
            if (!emailPattern.test(email)) {
                alert("Email Tidak Sesuai");
                return false
            }

            // Validasi password
            if (!passwordPattern.test(password)) {
                alert("Password Salah!");
                return false;
            }

            return true;
        }

function validateRegister() {
var name = document.getElementById("nameRegister").value;
var email = document.getElementById("emailRegister").value;
var password = document.getElementById("passwordRegister").value;

var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/;

// Validasi nama
if (name.trim() === "") {
    alert("Nama tidak boleh kosong!");
    return false;
}

// Validasi email
if (!emailPattern.test(email)) {
    alert("Email harus sesuai format email!");
    return false;
}

// Validasi password
if (!passwordPattern.test(password)) {
    alert("Password harus minimal 6 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter khusus!");
    return false;
}

return true;
}
</script>
</body>

</html>


<!-- Server-side Validation: -->
<!-- Validasi Input Kosong: Kode empty($nama) dan empty($email) memeriksa apakah pengguna mengisi semua kolom.
Validasi Format Email: filter_var($email, FILTER_VALIDATE_EMAIL) digunakan untuk memastikan bahwa email yang dimasukkan sesuai dengan format yang valid.
Validasi Password: preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password) memastikan bahwa password mengandung huruf kecil, huruf besar, angka, dan karakter khusus.
Validasi Keunikan Email: Kode ini memeriksa apakah email yang dimasukkan sudah ada dalam database menggunakan query SQL (SELECT * FROM user WHERE Email = ?).
Penyimpanan Data Pengguna Baru: Jika semua validasi berhasil, data akan disimpan di database. -->

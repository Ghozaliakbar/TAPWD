<?php
// Melakukan koneksi ke database MySQL menggunakan MySQLi
$server = "localhost"; // Ganti dengan nama server MySQL Anda
$username = "root"; // Ganti dengan nama pengguna MySQL Anda
$password = ""; // Ganti dengan kata sandi MySQL Anda
$database = "wisata"; // Ganti dengan nama database yang digunakan

$koneksi = new mysqli($server, $username, $password, $database);

// Memeriksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>
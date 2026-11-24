
<?php

// Pengaturan database
$host = "localhost";
$user = "root";
$password = "";
$database = "sipesma";

// Menghubungkan PHP ke MySQL
$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

// Memeriksa koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mengatur karakter database
mysqli_set_charset($conn, "utf8mb4");

?>

<?php
session_start();
$host = "localhost"; // Sesuaikan dengan server database
$user = "root"; // Username database
$password = ""; // Password database
$database = "imamku_db"; // Nama database

// Koneksi ke database
$conn = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tangkap data dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cegah SQL Injection
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Cek apakah username ada di database
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password dengan hash dari database
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            echo "<script>alert('Login berhasil!'); window.location='dashboard.php';</script>";
        } else {
            echo "<script>alert('Password salah!'); window.location='login.html';</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!'); window.location='login.html';</script>";
    }
}

$conn->close();
?>

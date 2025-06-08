<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "imamku_db";

    $koneksi = new mysqli($host, $user, $pass, $dbname);

    if ($koneksi->connect_error) {
        die("Koneksi gagal: " . $koneksi->connect_error);
    }

    $first = isset($_POST['first-name']) ? $_POST['first-name'] : '';
    $last = isset($_POST['last-name']) ? $_POST['last-name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';

    $stmt = $koneksi->prepare("INSERT INTO pemesanan (first_name, last_name, email, alamat) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first, $last, $email, $alamat);

    if ($stmt->execute()) {
        header("Location: guspeceng.html");
        exit();
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }

    $stmt->close();
    $koneksi->close();
} else {
    echo "Form tidak dikirim melalui POST.";
}
?>

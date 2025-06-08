<?php
$koneksi = new mysqli("localhost", "root", "", "imamku_db");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}


$query = "SELECT first_name, last_name, email, alamat FROM pemesanan ORDER BY id DESC LIMIT 1";
$result = $koneksi->query($query);

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo $data['alamat'];
} else {
    echo "Belum ada data.";
}

$koneksi->close();
?>

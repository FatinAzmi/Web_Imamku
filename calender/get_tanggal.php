<?php
include 'config.php';

$username = $_SESSION['username'];

$sql = "SELECT tanggal FROM jadwal_pemesanan WHERE username = '$username' ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row['tanggal'];
} else {
    echo "Belum ada tanggal";
}

$conn->close();
?>

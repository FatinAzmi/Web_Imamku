<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $username = $_SESSION['username'];

    // Cek 
    $checkQuery = "SELECT * FROM detail_pemesanan WHERE username = '$username'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        // Update tanggal lama
        $sql = "UPDATE _pemesanan SET tanggal = '$tanggal' WHERE username = '$username'";
    } else {
        $sql = "INSERT INTO jadwal_pemesanan (username, tanggal) VALUES ('$username', '$tanggal')";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Tanggal berhasil disimpan!";
    } else {
        echo "Gagal menyimpan: " . $conn->error;
    }
}
?>

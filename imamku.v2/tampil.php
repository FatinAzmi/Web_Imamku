<?php
include 'config.php';

$sql = "SELECT * FROM detailpemesanan";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID Pemesanan: " . $row["id"] . "<br>";
        echo "User ID: " . $row["user_id"] . "<br>";
        echo "Imam ID: " . $row["imam_id"] . "<br>";
        echo "Tanggal Mulai: " . $row["tanggal_mulai"] . "<br>";
        echo "Tanggal Selesai: " . $row["tanggal_selesai"] . "<br>";
        echo "Alamat: " . $row["alamat"] . "<br>";
        echo "Subtotal: Rp " . number_format($row["sub_total"], 2, ',', '.') . "<br>";
        echo "Diskon: Rp " . number_format($row["diskon"], 2, ',', '.') . "<br>";
        echo "Total Akhir: Rp " . number_format($row["total_akhir"], 2, ',', '.') . "<br>";
        echo "Metode Pembayaran: " . $row["metode_pembayaran"] . "<br>";
        echo "Waktu Pemesanan: " . $row["created_at"] . "<br>";
        echo "<hr>";
    }
} else {
    echo "Tidak ada data pemesanan.";
}

$conn->close();
?>
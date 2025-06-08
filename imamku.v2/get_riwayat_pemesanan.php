<?php
// get_riwayat_pemesanan.php
// Script untuk mengambil data riwayat pemesanan berdasarkan username
session_start();
header('Content-Type: application/json');

// Konfigurasi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "imamku_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ambil username dari session (asumsi user sudah login)
    // Untuk testing, bisa gunakan username default
    $current_username = isset($_SESSION['username']) ? $_SESSION['username'] : 'jiji';
    
    // Query untuk mengambil riwayat pemesanan berdasarkan username
    $sql = "SELECT * FROM riwayat_pemesanan WHERE username = :username ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $current_username);
    $stmt->execute();
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update status otomatis berdasarkan tanggal
    foreach ($orders as &$order) {
        $tanggal_acara = new DateTime($order['tanggal_acara']);
        $sekarang = new DateTime();
        $selisih_hari = $sekarang->diff($tanggal_acara)->days;
        
        // Logika update status otomatis
        if ($order['status'] !== 'dibatalkan') {
            $new_status = $order['status'];
            
            // Jika tanggal acara sudah lewat lebih dari 1 hari, set status selesai
            if ($tanggal_acara < $sekarang && $selisih_hari > 1) {
                $new_status = 'selesai';
            }
            // Jika tanggal acara hari ini atau besok, set dalam perjalanan
            elseif ($selisih_hari <= 1 && $tanggal_acara >= $sekarang && $order['status'] === 'dikonfirmasi') {
                $new_status = 'dalam_perjalanan';
            }
            // Jika pemesanan sudah 2 hari dan masih menunggu konfirmasi, konfirmasi otomatis
            elseif ($order['status'] === 'menunggu_konfirmasi') {
                $created_date = new DateTime($order['created_at']);
                $selisih_created = $sekarang->diff($created_date)->days;
                if ($selisih_created >= 2) {
                    $new_status = 'dikonfirmasi';
                }
            }
            
            // Update status jika berubah
            if ($new_status !== $order['status']) {
                $update_sql = "UPDATE riwayat_pemesanan SET status = :status, updated_at = NOW() WHERE id = :id";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bindParam(':status', $new_status);
                $update_stmt->bindParam(':id', $order['id']);
                $update_stmt->execute();
                
                $order['status'] = $new_status;
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'username' => $current_username
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn = null;
?>
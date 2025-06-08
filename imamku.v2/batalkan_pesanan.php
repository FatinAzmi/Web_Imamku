<?php
// batalkan_pesanan.php
// Script untuk membatalkan pesanan berdasarkan order ID
session_start();
header('Content-Type: application/json');

// Konfigurasi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "imamku_db";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

if (!isset($_POST['order_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Order ID diperlukan'
    ]);
    exit;
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $order_id = $_POST['order_id'];
    $current_username = isset($_SESSION['username']) ? $_SESSION['username'] : 'jiji';
    
    // Cek apakah pesanan milik user yang sedang login dan bisa dibatalkan
    $check_sql = "SELECT * FROM riwayat_pemesanan WHERE id = :order_id AND username = :username";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':order_id', $order_id);
    $check_stmt->bindParam(':username', $current_username);
    $check_stmt->execute();
    
    $order = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode([
            'success' => false,
            'message' => 'Pesanan tidak ditemukan atau bukan milik Anda'
        ]);
        exit;
    }
    
    // Cek apakah pesanan bisa dibatalkan
    if (!in_array($order['status'], ['menunggu_konfirmasi', 'dikonfirmasi'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Pesanan tidak dapat dibatalkan karena statusnya: ' . $order['status']
        ]);
        exit;
    }
    
    // Cek apakah masih bisa dibatalkan berdasarkan tanggal
    $tanggal_acara = new DateTime($order['tanggal_acara']);
    $sekarang = new DateTime();
    $selisih_hari = $sekarang->diff($tanggal_acara)->days;
    
    // Tidak bisa dibatalkan jika tinggal 1 hari atau kurang
    if ($tanggal_acara <= $sekarang || $selisih_hari < 1) {
        echo json_encode([
            'success' => false,
            'message' => 'Pesanan tidak dapat dibatalkan karena acara sudah terlalu dekat atau sudah berlalu'
        ]);
        exit;
    }
    
    // Begin transaction
    $conn->beginTransaction();
    
    try {
        // Update status pesanan menjadi dibatalkan
        $cancel_sql = "UPDATE riwayat_pemesanan SET status = 'dibatalkan', updated_at = NOW() WHERE id = :order_id";
        $cancel_stmt = $conn->prepare($cancel_sql);
        $cancel_stmt->bindParam(':order_id', $order_id);
        $cancel_stmt->execute();
        
        // Update jadwal menjadi available lagi jika ada
        $update_jadwal_sql = "UPDATE jadwal_pemesanan SET status = 'available', riwayat_id = NULL 
                             WHERE tanggal = :tanggal_acara AND username = :username";
        $jadwal_stmt = $conn->prepare($update_jadwal_sql);
        $jadwal_stmt->bindParam(':tanggal_acara', $order['tanggal_acara']);
        $jadwal_stmt->bindParam(':username', $current_username);
        $jadwal_stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan'
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction jika terjadi error
        $conn->rollback();
        throw $e;
    }
    
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
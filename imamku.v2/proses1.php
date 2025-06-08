<?php
include 'config.php';

// Ambil data dari form
$nama = $_POST['nama'];
$email = $_POST['email'];
$message = $_POST['pesan'];

// Simpan ke database
$sql = "INSERT INTO feedback (nama, email, pesan) VALUES ('$nama', '$email', '$message')";
if ($conn->query($sql) === TRUE) {
    $popupmassage = "Pesan berhasil dikirim!" ;
    $popuptype = "succes";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>feedback</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<?php if ($popupmassage): ?>
    <div id="popupOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full">
            <h3 class="text-lg font-bold text-black-600 mb-4 text-center">Terima kasih</h3>
            <p class="text-gray-700 text-center"><?= $popupmassage ?></p><br>
            <button onclick= "window.location.href = 'home.html'"
                    class="mx-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md block">
                    OK
            </button>
        </div>
    </div>
<?php endif; ?>
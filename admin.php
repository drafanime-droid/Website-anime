<?php
session_start(); // Pastikan session tersimpan
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = trim($_POST['code'] ?? '');
    // Debug (opsional, hapus setelah testing): $message = "Kode yang dimasukkan: '$code'<br>";
    if ($code === '083850253184') {
        header('Location: adminpanel.php');
        exit();
    } else {
        $message = "<p style='color: #ff0000;'>Kode admin salah! Pastikan dimasukkan dengan benar (case-sensitive).</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SalioNime</title>
    <style> 
        body { background-color: #000; color: #fff; font-family: Arial, sans-serif; } 
        form { max-width: 300px; margin: 100px auto; } 
        input { display: block; margin: 10px 0; padding: 10px; width: 100%; background-color: #333; color: #fff; border: 1px solid #00ffff; } 
        button { background-color: #000; color: #fff; padding: 10px; width: 100%; box-shadow: 0 0 10px #000; } 
        .message { text-align: center; }
    </style>
</head>
<body>
    <form method="POST">
        <input type="text" name="code" placeholder="Kode Admin" required>
        <button type="submit">Masuk</button>
        <div class="message"><?php echo $message; ?></div>
    </form>
</body>
</html>
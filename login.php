<?php
session_start(); // Tambahkan ini agar session tersimpan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $users = json_decode(file_get_contents('user.json'), true) ?? [];
    $nama = trim($_POST['nama'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (isset($users[$nama]) && $users[$nama]['katasandi'] == $password) {
        $_SESSION['user'] = $nama; // Pastikan session diset
        header('Location: index.php');
        exit();
    } else {
        $error = "Nama atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SalioNime</title>
    <style> body { background-color: #000; color: #fff; font-family: Arial, sans-serif; } form { max-width: 300px; margin: 100px auto; } input { display: block; margin: 10px 0; padding: 10px; width: 100%; background-color: #333; color: #fff; border: 1px solid #00ffff; } button { background-color: #0000ff; color: #fff; padding: 10px; width: 100%; box-shadow: 0 0 10px #0000ff; } .error { color: #ff0000; text-align: center; } </style>
</head>
<body>
    <form method="POST">
        <input type="text" name="nama" placeholder="Nama" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    </form>
</body>
</html>
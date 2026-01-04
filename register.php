<?php
session_start(); // Tambahkan ini agar session tersimpan jika perlu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $users = json_decode(file_get_contents('user.json'), true) ?? [];
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nomor_whatsapp = trim($_POST['nomor_whatsapp'] ?? '');
    $url_foto = trim($_POST['url_foto'] ?? '');
    $katasandi = trim($_POST['katasandi'] ?? '');
    $ulang_katasandi = trim($_POST['ulang_katasandi'] ?? '');
    
    if (isset($users[$nama])) {
        $error = "Nama sudah ada!";
    } elseif (empty($nama) || empty($email) || empty($nomor_whatsapp) || empty($katasandi) || $katasandi != $ulang_katasandi) {
        $error = "Semua kolom wajib diisi dan password harus sama!";
    } else {
        $users[$nama] = [
            'nama' => $nama,
            'email' => $email,
            'nomor_whatsapp' => $nomor_whatsapp,
            'url_foto' => $url_foto ?: 'default.jpg',
            'katasandi' => $katasandi,
            'title' => 'member'
        ];
        file_put_contents('user.json', json_encode($users, JSON_PRETTY_PRINT));
        $_SESSION['user'] = $nama; // Auto-login setelah register
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SalioNime</title>
    <style> body { background-color: #000; color: #fff; font-family: Arial, sans-serif; } form { max-width: 300px; margin: 100px auto; } input { display: block; margin: 10px 0; padding: 10px; width: 100%; background-color: #333; color: #fff; border: 1px solid #00ffff; } button { background-color: #ff0000; color: #fff; padding: 10px; width: 100%; box-shadow: 0 0 10px #ff0000; } .error { color: #ff0000; text-align: center; } </style>
</head>
<body>
    <form method="POST">
        <input type="text" name="nama" placeholder="Nama Panggilan" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="nomor_whatsapp" placeholder="Nomor WhatsApp" required>
        <input type="url" name="url_foto" placeholder="URL Foto Profil (opsional)">
        <input type="password" name="katasandi" placeholder="Katasandi" required>
        <input type="password" name="ulang_katasandi" placeholder="Ulang Katasandi" required>
        <button type="submit">Register</button>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    </form>
</body>
</html>
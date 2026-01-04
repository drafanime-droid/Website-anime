<?php
session_start(); // Pastikan session tersimpan
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $animes = json_decode(file_get_contents('daftaranime.json'), true) ?? [];
        $judul = trim($_POST['judul'] ?? '');
        $genres = $_POST['genres'] ?? [];
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $banner_url = trim($_POST['banner_url'] ?? '');
        
        if (empty($judul) || empty($deskripsi) || empty($url) || empty($banner_url)) {
            $message = "<p style='color: #ff0000;'>Semua kolom wajib diisi!</p>";
        } elseif (!filter_var($url, FILTER_VALIDATE_URL) || !filter_var($banner_url, FILTER_VALIDATE_URL)) {
            $message = "<p style='color: #ff0000;'>URL video dan banner harus valid!</p>";
        } elseif (empty($genres)) {
            $message = "<p style='color: #ff0000;'>Pilih minimal satu genre!</p>";
        } else {
            // Perbaikan: Tambahkan anime baru di AWAL array agar muncul di depan list
            array_unshift($animes, [
                'id' => uniqid(),
                'judul' => $judul,
                'genres' => $genres,
                'deskripsi' => $deskripsi,
                'url' => $url,
                'banner_url' => $banner_url
            ]);
            if (file_put_contents('daftaranime.json', json_encode($animes, JSON_PRETTY_PRINT)) === false) {
                $message = "<p style='color: #ff0000;'>Gagal menyimpan data! Periksa izin file atau folder.</p>";
            } else {
                $message = "<p style='color: #00ff00;'>Anime berhasil ditambahkan dan muncul di depan list!</p>";
            }
        }
    } catch (Exception $e) {
        $message = "<p style='color: #ff0000;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SalioNime</title>
    <style>
        body { background-color: #000; color: #fff; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        header { background-color: #000; padding: 10px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #00ffff; }
        .site-name { font-style: italic; font-weight: bold; color: #00ffff; text-shadow: 0 0 10px #00ffff; }
        .buttons { display: flex; gap: 10px; }
        button { padding: 10px; border: none; cursor: pointer; border-radius: 5px; }
        .logout { background-color: #ff0000; color: #fff; box-shadow: 0 0 10px #ff0000; }
        .admin { background-color: #000; color: #fff; box-shadow: 0 0 10px #000; }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .profile-pic { width: 40px; height: 40px; border-radius: 50%; }
        .title { font-weight: bold; }
        .title-member { color: #ffff00; }
        .title-premium { color: #0000ff; animation: flash 1s infinite; }
        .title-admin, .title-owner { color: #ff0000; animation: glow 3s infinite; }
        .verified { color: #add8e6; }
        @keyframes flash { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
        @keyframes glow { 0%, 100% { text-shadow: 0 0 10px #ff0000; } 50% { text-shadow: 0 0 20px #ff0000; } }
        form { max-width: 800px; margin: 50px auto; padding: 20px; background-color: #111; border-radius: 10px; }
        label { display: block; margin: 10px 0 5px; font-weight: bold; }
        input[type="text"], input[type="url"], textarea { width: 100%; padding: 10px; margin-bottom: 10px; background-color: #333; color: #fff; border: 1px solid #00ffff; border-radius: 5px; }
        .genres { display: flex; overflow-x: auto; white-space: nowrap; gap: 10px; margin-bottom: 10px; padding: 10px 0; scrollbar-width: thin; scrollbar-color: #00ffff #333; }
        .genres::-webkit-scrollbar { height: 8px; }
        .genres::-webkit-scrollbar-track { background: #333; }
        .genres::-webkit-scrollbar-thumb { background: #00ffff; border-radius: 4px; }
        .genre { background-color: #333; padding: 5px 10px; border-radius: 5px; min-width: 100px; text-align: center; }
        .genre input { margin-right: 5px; }
        button[type="submit"] { background-color: #000; color: #fff; box-shadow: 0 0 10px #000; width: 100%; }
        .message { text-align: center; margin-bottom: 20px; }
        footer { border-top: 2px solid #00ffff; padding: 20px; text-align: center; margin-top: 20px; }
        @media (max-width: 768px) { form { margin: 20px; } .genre { min-width: 80px; } }
    </style>
</head>
<body>
    <header>
        <div class="site-name">SalioNime®</div>
        <div class="buttons">
            <button class="admin" onclick="window.location='admin.php'">Kembali ke Admin</button>
            <button class="logout" onclick="window.location='logout.php'">Logout</button>
        </div>
    </header>

    <h1 style="text-align: center; color: #00ffff;">Admin Panel - Tambah Anime</h1>
    <div class="message"><?php echo $message; ?></div>
    <form method="POST">
        <label for="judul">Judul Anime:</label>
        <input type="text" id="judul" name="judul" placeholder="Masukkan judul anime" required>

        <label>Genres (scroll ke kanan untuk lebih banyak, pilih minimal satu):</label>
        <div class="genres">
            <?php 
            $genres = ['Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror', 'Mystery', 'Romance', 'Sci-Fi', 'Slice of Life', 'Sports', 'Supernatural', 'Thriller', 'Mecha', 'Shoujo', 'Shounen', 'Yuri', 'Yaoi', 'Ecchi', 'Harem', 'Isekai', 'Reincarnation', 'School', 'Military', 'Historical', 'Psychological', 'Music', 'Game', 'Martial Arts', 'Vampire', 'Zombie', 'Police', 'Super Power', 'Magic', 'Demons', 'Angels', 'Samurai', 'Ninja', 'Cooking', 'Travel', 'Work Life', 'Kids', 'Adult', 'OVA', 'Movie', 'Special'];
            foreach ($genres as $genre): ?>
                <div class="genre">
                    <input type="checkbox" name="genres[]" value="<?php echo $genre; ?>" id="genre-<?php echo $genre; ?>">
                    <label for="genre-<?php echo $genre; ?>"><?php echo $genre; ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <label for="deskripsi">Deskripsi:</label>
        <textarea id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi anime" rows="4" required></textarea>

        <label for="url">URL Video (Google Drive embed, ganti /view dengan /preview):</label>
        <input type="url" id="url" name="url" placeholder="https://drive.google.com/file/d/ID/preview" required>

        <label for="banner_url">URL Banner:</label>
        <input type="url" id="banner_url" name="banner_url" placeholder="https://example.com/banner.jpg" required>

        <button type="submit">Tambah Anime</button>
    </form>

    <footer>
        <p>Discord: <a href="https://discord.gg/jfedYg5tnM" style="color: #00ffff;">https://discord.gg/jfedYg5tnM</a></p>
        <p>WhatsApp: 083856699680</p>
        <p>Tentang Kami: Website ini dibangun oleh komunitas Discord, komunitas game, komunitas streamer, dan juga komunitas Minecraft Tanoa. Ada satupun dari komunitas itu atau pun komunitas pengembang jadi mohon dimaklumi bila masih cacat dan kami selaku warga komunitas mengucapkan terima kasih yang sebesar-besarnya kepada komunitas karena telah sepenuhnya mendukung proyek ini. Sekian dari kami, terima kasih.</p>
        <p>Copyright by: allinonestudio©</p>
    </footer>
</body>
</html>
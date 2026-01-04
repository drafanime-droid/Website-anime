<?php
$query = strtolower(trim($_GET['q'] ?? ''));
$animes = json_decode(file_get_contents('daftaranime.json'), true) ?? [];
$totalWatch = json_decode(file_get_contents('totalwatch.json'), true) ?? [];
$results = [];

try {
    if (!empty($query)) {
        $results = array_filter($animes, function($a) use ($query) {
            return strpos(strtolower($a['judul'] ?? ''), $query) !== false;
        });
    } else {
        $results = $animes; // Jika query kosong, tampilkan semua
    }
} catch (Exception $e) {
    $results = []; // Fallback jika error
    $error = "Error saat memproses pencarian: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - SalioNime</title>
    <style>
        body { background-color: #000; color: #fff; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        header { background-color: #000; padding: 10px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #00ffff; }
        .site-name { font-style: italic; font-weight: bold; color: #00ffff; text-shadow: 0 0 10px #00ffff; }
        .buttons { display: flex; gap: 10px; }
        button { padding: 10px; border: none; cursor: pointer; border-radius: 5px; }
        .logout { background-color: #ff0000; color: #fff; box-shadow: 0 0 10px #ff0000; }
        .admin { background-color: #000; color: #fff; box-shadow: 0 0 10px #000; }
        .anime-list { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin: 20px; }
        .anime-item { background-color: #333; padding: 10px; text-align: center; border-radius: 5px; }
        .anime-item img { width: 100%; max-width: 100%; height: 200px; max-height: 200px; object-fit: contain; border-radius: 5px; }
        .no-results { text-align: center; margin: 50px; color: #ff0000; }
        footer { border-top: 2px solid #00ffff; padding: 20px; text-align: center; margin-top: 20px; }
        @media (max-width: 768px) { .anime-list { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>
<body>
    <header>
        <div class="site-name">SalioNime®</div>
        <div class="buttons">
            <button onclick="window.location='index.php'">Kembali ke Home</button>
            <button class="admin" onclick="window.location='admin.php'">Admin</button>
        </div>
    </header>

    <h2 style="text-align: center;">Hasil Pencarian: "<?php echo htmlspecialchars($query); ?>"</h2>
    <?php if (isset($error)): ?>
        <p class="no-results"><?php echo $error; ?></p>
    <?php elseif (empty($results)): ?>
        <p class="no-results">Tidak ada anime ditemukan untuk "<?php echo htmlspecialchars($query); ?>". Coba kata kunci lain.</p>
    <?php else: ?>
        <div class="anime-list">
            <?php foreach ($results as $anime): ?>
                <div class="anime-item" onclick="window.location='watch.php?id=<?php echo $anime['id']; ?>'">
                    <img src="<?php echo $anime['banner_url']; ?>" alt="<?php echo $anime['judul']; ?>">
                    <h3><?php echo $anime['judul']; ?></h3>
                    <p>👁 <?php echo $totalWatch[$anime['id']] ?? 0; ?> views</p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <footer>
        <p>Discord: <a href="https://discord.gg/jfedYg5tnM" style="color: #00ffff;">https://discord.gg/jfedYg5tnM</a></p>
        <p>WhatsApp: 083856699680</p>
        <p>Tentang Kami: Website ini dibangun oleh komunitas Discord, komunitas game, komunitas streamer, dan juga komunitas Minecraft Tanoa. Ada satupun dari komunitas itu atau pun komunitas pengembang jadi mohon dimaklumi bila masih cacat dan kami selaku warga komunitas mengucapkan terima kasih yang sebesar-besarnya kepada komunitas karena telah sepenuhnya mendukung proyek ini. Sekian dari kami, terima kasih.</p>
        <p>Copyright by: allinonestudio©</p>
    </footer>
</body>
</html>
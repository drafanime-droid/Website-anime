<?php
$genre = $_GET['genre'] ?? '';
$animes = json_decode(file_get_contents('daftaranime.json'), true) ?? [];
$filteredAnimes = array_filter($animes, function($a) use ($genre) {
    return in_array($genre, $a['genres'] ?? []);
});
$totalWatch = json_decode(file_get_contents('totalwatch.json'), true) ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter <?php echo $genre; ?> - SalioNime</title>
    <style>
        body { background-color: #000; color: #fff; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        header { background-color: #000; padding: 10px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #00ffff; }
        .site-name { font-style: italic; font-weight: bold; color: #00ffff; text-shadow: 0 0 10px #00ffff; }
        .anime-list { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin: 20px; }
        .anime-item { background-color: #333; padding: 10px; text-align: center; border-radius: 5px; }
        .anime-item img { width: 100%; height: 200px; object-fit: cover; }
        footer { border-top: 2px solid #00ffff; padding: 20px; text-align: center; margin-top: 20px; }
        @media (max-width: 768px) { .anime-list { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>
<body>
    <header>
        <div class="site-name">SalioNime®</div>
    </header>

    <h2>Anime dengan Genre: <?php echo $genre; ?></h2>
    <div class="anime-list">
        <?php foreach ($filteredAnimes as $anime): ?>
            <div class="anime-item" onclick="window.location='watch.php?id=<?php echo $anime['id']; ?>'">
                <img src="<?php echo $anime['banner_url']; ?>" alt="<?php echo $anime['judul']; ?>">
                <h3><?php echo $anime['judul']; ?></h3>
                <p>👁 <?php echo $totalWatch[$anime['id']] ?? 0; ?> views</p>
            </div>
        <?php endforeach; ?>
    </div>

    <footer>
        <p>Discord: <a href="https://discord.gg/jfedYg5tnM" style="color: #00ffff;">https://discord.gg/jfedYg5tnM</a></p>
        <p>WhatsApp: 083856699680</p>
        <p>Tentang Kami: Website ini dibangun oleh komunitas Discord, komunitas game, komunitas streamer, dan juga komunitas Minecraft Tanoa. Ada satupun dari komunitas itu atau pun komunitas pengembang jadi mohon dimaklumi bila masih cacat dan kami selaku warga komunitas mengucapkan terima kasih yang sebesar-besarnya kepada komunitas karena telah sepenuhnya mendukung proyek ini. Sekian dari kami, terima kasih.</p>
        <p>Copyright by: allinonestudio©</p>
    </footer>
</body>
</html>
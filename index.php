<?php
session_start();
$users = json_decode(file_get_contents('user.json'), true) ?? [];
$animes = json_decode(file_get_contents('daftaranime.json'), true) ?? [];
$totalWatch = json_decode(file_get_contents('totalwatch.json'), true) ?? [];
$loggedIn = isset($_SESSION['user']);
$user = $loggedIn ? $users[$_SESSION['user']] : null;
// Debug session (opsional, hapus setelah testing): echo "Session user: " . ($_SESSION['user'] ?? 'none') . "<br>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalioNime</title>
    <style>
        body { background-color: #000; color: #fff; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        header { background-color: #000; padding: 10px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #00ffff; }
        .site-name { font-style: italic; font-weight: bold; color: #00ffff; text-shadow: 0 0 10px #00ffff; }
        .buttons { display: flex; gap: 10px; }
        button { padding: 10px; border: none; cursor: pointer; border-radius: 5px; }
        .login { background-color: #0000ff; color: #fff; box-shadow: 0 0 10px #0000ff; }
        .register { background-color: #ff0000; color: #fff; box-shadow: 0 0 10px #ff0000; }
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
        .search { margin: 20px; display: flex; gap: 10px; }
        .search input { padding: 10px; background-color: #333; color: #fff; border: 1px solid #00ffff; }
        .search button { background-color: #0000ff; color: #fff; box-shadow: 0 0 10px #0000ff; }
        .filters { margin: 20px; display: flex; overflow-x: auto; white-space: nowrap; gap: 10px; padding: 10px 0; scrollbar-width: thin; scrollbar-color: #00ffff #333; }
        .filters::-webkit-scrollbar { height: 8px; }
        .filters::-webkit-scrollbar-track { background: #333; }
        .filters::-webkit-scrollbar-thumb { background: #00ffff; border-radius: 4px; }
        .filter-btn { background-color: #0000ff; color: #fff; box-shadow: 0 0 10px #0000ff; padding: 10px; border-radius: 5px; min-width: 100px; text-align: center; }
        .anime-list { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin: 20px; }
        .anime-item { background-color: #333; padding: 10px; text-align: center; border-radius: 5px; }
        .anime-item img { width: 100%; max-width: 100%; height: 200px; max-height: 200px; object-fit: contain; /* Diubah dari cover ke contain agar tidak terpotong */ border-radius: 5px; }
        footer { border-top: 2px solid #00ffff; padding: 20px; text-align: center; margin-top: 20px; }
        @media (max-width: 768px) { .anime-list { grid-template-columns: repeat(2, 1fr); } .filter-btn { min-width: 80px; } }
    </style>
</head>
<body>
    <header>
        <div class="site-name">SalioNime®</div>
        <div class="buttons">
            <?php if (!$loggedIn): ?>
                <button class="login" onclick="window.location='login.php'">Login</button>
                <button class="register" onclick="window.location='register.php'">Register</button>
            <?php else: ?>
                <div class="user-info">
                    <img src="<?php echo $user['url_foto'] ?? 'default.jpg'; ?>" class="profile-pic" alt="Profile">
                    <span class="title title-<?php echo strtolower($user['title']); ?>"><?php echo $user['title']; ?></span>
                    <span><?php echo $user['nama']; ?><?php if (in_array($user['title'], ['admin', 'owner'])) echo '<span class="verified">✓</span>'; ?></span>
                </div>
                <button class="logout" onclick="window.location='logout.php'">Logout</button>
            <?php endif; ?>
            <button class="admin" onclick="window.location='admin.php'">Admin</button>
        </div>
    </header>

    <div class="search">
        <input type="text" id="search-input" placeholder="Cari judul anime...">
        <button onclick="searchAnime()">🔍</button>
    </div>

    <div class="filters">
        <?php $genres = ['Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror', 'Mystery', 'Romance', 'Sci-Fi', 'Slice of Life', 'Sports', 'Supernatural', 'Thriller', 'Mecha', 'Shoujo', 'Shounen', 'Yuri', 'Yaoi', 'Ecchi', 'Harem', 'Isekai', 'Reincarnation', 'School', 'Military', 'Historical', 'Psychological', 'Music', 'Game', 'Martial Arts', 'Vampire', 'Zombie', 'Police', 'Super Power', 'Magic', 'Demons', 'Angels', 'Samurai', 'Ninja', 'Cooking', 'Travel', 'Work Life', 'Kids', 'Adult', 'OVA', 'Movie', 'Special']; ?>
        <?php foreach ($genres as $genre): ?>
            <button class="filter-btn" onclick="filterGenre('<?php echo $genre; ?>')"><?php echo $genre; ?></button>
        <?php endforeach; ?>
    </div>

    <h2>SEMUA ANIME</h2>
    <div class="anime-list">
        <?php foreach ($animes as $anime): ?>
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

    <script>
        function searchAnime() {
            const query = document.getElementById('search-input').value;
            window.location = `hasilserc.php?q=${encodeURIComponent(query)}`;
        }
        function filterGenre(genre) {
            window.location = `filter.php?genre=${encodeURIComponent(genre)}`;
        }
    </script>
</body>
</html>
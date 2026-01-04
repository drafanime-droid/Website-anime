<?php
session_start();
$id = $_GET['id'] ?? '';
$animes = json_decode(file_get_contents('daftaranime.json'), true) ?? [];
$anime = null;
foreach ($animes as $a) {
    if ($a['id'] == $id) {
        $anime = $a;
        break;
    }
}
if (!$anime) {
    die("Anime tidak ditemukan.");
}
// Load/update total watch
$totalWatch = json_decode(file_get_contents('totalwatch.json'), true) ?? [];
$totalWatch[$id] = ($totalWatch[$id] ?? 0) + 1;
file_put_contents('totalwatch.json', json_encode($totalWatch));
// Load likes/dislikes
$likesData = json_decode(file_get_contents('likes.json'), true) ?? [];
if (!isset($likesData[$id])) {
    $likesData[$id] = ['likes' => 0, 'dislikes' => 0, 'voted_users' => []];
}
// Handle like/dislike
$loggedIn = isset($_SESSION['user']);
$message = '';
$userVoted = in_array($_SESSION['user'] ?? '', $likesData[$id]['voted_users']);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $loggedIn && !$userVoted) {
    $action = $_POST['action'] ?? '';
    if ($action == 'like') {
        $likesData[$id]['likes']++;
        $likesData[$id]['voted_users'][] = $_SESSION['user'];
        $message = "Liked!";
    } elseif ($action == 'dislike') {
        $likesData[$id]['dislikes']++;
        $likesData[$id]['voted_users'][] = $_SESSION['user'];
        $message = "Disliked!";
    }
    file_put_contents('likes.json', json_encode($likesData, JSON_PRETTY_PRINT));
    header("Location: watch.php?id=$id");
    exit();
}
// Load comments with error handling
$comments = [];
$commentError = '';
try {
    $comments = json_decode(file_get_contents('allcoment.json'), true) ?? [];
} catch (Exception $e) {
    $commentError = "Error loading comments: " . $e->getMessage();
}
$animeComments = $comments[$id] ?? [];
$users = json_decode(file_get_contents('user.json'), true) ?? [];
$user = $loggedIn ? $users[$_SESSION['user']] : null;
// Handle new comment with error handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) && $loggedIn) {
    $comment = trim($_POST['comment'] ?? '');
    if (!empty($comment)) {
        try {
            $animeComments[] = [
                'user' => $_SESSION['user'],
                'title' => $user['title'],
                'comment' => $comment,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            $comments[$id] = $animeComments;
            file_put_contents('allcoment.json', json_encode($comments, JSON_PRETTY_PRINT));
            header("Location: watch.php?id=$id");
            exit();
        } catch (Exception $e) {
            $commentError = "Error saving comment: " . $e->getMessage();
        }
    } else {
        $commentError = "Komentar tidak boleh kosong.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $anime['judul']; ?> - SalioNime</title>
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
        .video { margin: 20px; text-align: center; }
        iframe { width: 100%; height: 400px; border: none; }
        .details { margin: 20px; }
        .actions { display: flex; gap: 10px; margin: 20px; }
        .like, .dislike { padding: 10px 20px; border-radius: 5px; font-size: 16px; }
        .like { background-color: #00ff00; color: #000; }
        .dislike { background-color: #ff0000; color: #fff; }
        .disabled { opacity: 0.5; cursor: not-allowed; }
        .comments { margin: 20px; }
        .comment { border: 1px solid #333; padding: 10px; margin: 10px 0; background-color: #111; }
        .comment-form { margin: 20px; }
        textarea { width: 100%; padding: 10px; background-color: #333; color: #fff; border: 1px solid #00ffff; }
        .message { text-align: center; color: #00ff00; margin: 10px; }
        .error { text-align: center; color: #ff0000; margin: 10px; }
        footer { border-top: 2px solid #00ffff; padding: 20px; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <header>
        <div class="site-name">SalioNime®</div>
        <div class="buttons">
            <?php if ($loggedIn): ?>
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

    <div class="video">
        <h1><?php echo $anime['judul']; ?></h1>
        <iframe src="<?php echo str_replace('/view', '/preview', $anime['url']); ?>" allowfullscreen></iframe>
    </div>

    <div class="details">
        <h2>Genre: <?php echo implode(', ', $anime['genres']); ?></h2>
        <p><?php echo $anime['deskripsi']; ?></p>
    </div>

    <div class="actions">
        <?php if ($loggedIn): ?>
            <?php if ($userVoted): ?>
                <p>Sudah vote: <?php echo in_array($_SESSION['user'], $likesData[$id]['voted_users']) && $likesData[$id]['likes'] > 0 ? 'Liked' : 'Disliked'; ?> (<?php echo $likesData[$id]['likes']; ?> likes, <?php echo $likesData[$id]['dislikes']; ?> dislikes)</p>
            <?php else: ?>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="action" value="like" class="like">❤️ Like (<?php echo $likesData[$id]['likes']; ?>)</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="action" value="dislike" class="dislike">👎 Dislike (<?php echo $likesData[$id]['dislikes']; ?>)</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <p>Login untuk like/dislike.</p>
        <?php endif; ?>
    </div>
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <div class="comments">
        <h2>Komentar</h2>
        <?php if ($commentError): ?>
            <p class="error"><?php echo $commentError; ?></p>
        <?php endif; ?>
        <?php foreach ($animeComments as $c): ?>
            <div class="comment" style="border-color: <?php echo $c['title'] == 'member' ? '#ffff00' : ($c['title'] == 'premium' ? '#0000ff' : '#ff0000'); ?>;">
                <strong><?php echo $c['title']; ?> <?php echo $c['user']; ?><?php if (in_array($c['title'], ['admin', 'owner'])) echo ' <span class="verified">✓</span>'; ?></strong><br>
                <?php echo htmlspecialchars($c['comment']); ?><br>
                <small><?php echo $c['timestamp']; ?></small>
            </div>
        <?php endforeach; ?>
        <?php if ($loggedIn): ?>
            <form class="comment-form" method="POST">
                <textarea name="comment" placeholder="Tulis komentar..." required></textarea>
                <button type="submit">Kirim</button>
            </form>
        <?php endif; ?>
    </div>

    <footer>
        <p>Discord: <a href="https://discord.gg/jfedYg5tnM" style="color: #00ffff;">https://discord.gg/jfedYg5tnM</a></p>
        <p>WhatsApp: 083856699680</p>
        <p>Tentang Kami: Website ini dibangun oleh komunitas Discord, komunitas game, komunitas streamer, dan juga komunitas Minecraft Tanoa. Ada satupun dari komunitas itu atau pun komunitas pengembang jadi mohon dimaklumi bila masih cacat dan kami selaku warga komunitas mengucapkan terima kasih yang sebesar-besarnya kepada komunitas karena telah sepenuhnya mendukung proyek ini. Sekian dari kami, terima kasih.</p>
        <p>Copyright by: allinonestudio©</p>
    </footer>
</body>
</html>
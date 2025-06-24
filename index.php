<?php
require_once 'database.php';
include 'header.php';

$pdo = getPDO();
$stmt = $pdo->query("SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();

$grouped = [];
foreach ($posts as $post) {
    $date = date('Y-m-d', strtotime($post['created_at']));
    $grouped[$date][] = $post;
}
?>

<h2>Witamy na Blogu!</h2>

<?php if (empty($grouped)): ?>
    <p>Brak postów do wyświetlenia.</p>
<?php else: ?>
    <?php foreach ($grouped as $date => $datePosts): ?>
        <h3><?= $date ?></h3>
        <?php foreach ($datePosts as $post): ?>
            <div class="post-preview">
                <h4><a href="post.php?id=<?= $post['id'] ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a></h4>
                <small>Dodano: <?= $post['created_at'] ?></small>
                <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...</p>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'footer.php'; ?>

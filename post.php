<?php
session_start();
require_once 'database.php';
include 'header.php';

$pdo = getPDO();
$error = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='error'>Nieprawidłowe ID posta.</p>";
    include 'footer.php';
    exit;
}

$post_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    echo "<p class='error'>Post nie został znaleziony.</p>";
    include 'footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at ASC");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();
?>

<main>
    <article class="post">
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <p class="meta">Dodano: <?= $post['created_at'] ?> | Autor: <?= htmlspecialchars($post['username']) ?></p>

        <?php if ($post['image']): ?>
            <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Obrazek" class="post-image">
        <?php endif; ?>

        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
    </article>

    <section class="comments">
        <h3>Komentarze</h3>

        <?php if (empty($comments)): ?>
            <p class="info">Brak komentarzy. Bądź pierwszy!</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <p class="meta"><?= htmlspecialchars($comment['author_name']) ?> napisał(a) <?= $comment['created_at'] ?>:</p>
                    <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <section class="add-comment">
        <h3>Dodaj komentarz</h3>

        <form method="post" action="comment.php">
            <input type="hidden" name="post_id" value="<?= $post_id ?>">

            <label for="content">Treść komentarza:</label><br>
            <textarea name="content" id="content" rows="5" required></textarea><br><br>

            <button type="submit">Wyślij komentarz</button>
        </form>
    </section>

    <p><a href="index.php">← Wróć do bloga</a></p>
</main>

<?php
$prevStmt = $pdo->prepare("SELECT id, title FROM posts WHERE id < ? ORDER BY id DESC LIMIT 1");
$prevStmt->execute([$post['id']]);
$prevPost = $prevStmt->fetch();

$nextStmt = $pdo->prepare("SELECT id, title FROM posts WHERE id > ? ORDER BY id ASC LIMIT 1");
$nextStmt->execute([$post['id']]);
$nextPost = $nextStmt->fetch();
?>

<div class="navigation-links">
    <?php if ($prevPost): ?>
        <a href="post.php?id=<?= $prevPost['id'] ?>">← <?= htmlspecialchars($prevPost['title']) ?></a>
    <?php endif; ?>

    <?php if ($nextPost): ?>
        <span style="margin-left: 20px;">
            <a href="post.php?id=<?= $nextPost['id'] ?>"><?= htmlspecialchars($nextPost['title']) ?> →</a>
        </span>
    <?php endif; ?>
</div>
<?php if (
    isset($_SESSION['user_id']) &&
    (
        $_SESSION['role'] === 'admin' ||
        $_SESSION['user_id'] == $post['user_id']
    )
): ?>
    <p>
        <a href="edit_post.php?id=<?= $post['id'] ?>">Edytuj</a> |
        <a href="delete_post.php?id=<?= $post['id'] ?>" onclick="return confirm('Czy na pewno chcesz usunąć ten wpis?');">Usuń</a>
    </p>
<?php endif; ?>

<?php include 'footer.php'; ?>

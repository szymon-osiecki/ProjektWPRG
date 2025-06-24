<?php
session_start();
require_once 'database.php';
include 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<p class='error'>Dostęp zabroniony. Tylko administratorzy mogą korzystać z tego panelu.</p>";
    include 'footer.php';
    exit;
}

$pdo = getPDO();

$usersStmt = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id ASC");
$users = $usersStmt->fetchAll();

$postsStmt = $pdo->query("
    SELECT posts.id, posts.title, posts.created_at, users.username
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
");
$posts = $postsStmt->fetchAll();

?>


<h2>Panel administratora</h2>

<h3>Użytkownicy</h3>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th><th>Login</th><th>Email</th><th>Rola</th><th>Rejestracja</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td><?= $user['created_at'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Wpisy</h3>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th><th>Tytuł</th><th>Data</th><th>Autor</th>
    </tr>
    <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= $post['id'] ?></td>
            <td><?= htmlspecialchars($post['title']) ?></td>
            <td><?= $post['created_at'] ?></td>
            <td><?= htmlspecialchars($post['username']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include 'footer.php'; ?>

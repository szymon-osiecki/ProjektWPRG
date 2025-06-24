<?php
session_start();
require_once 'database.php';
require_once 'auth.php';

redirectIfNotLoggedIn();
requireRole(['admin', 'author']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$post_id = (int) $_GET['id'];
$pdo = getPDO();

$stmt = $pdo->prepare("SELECT user_id, image FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    include 'header.php';
    echo "<p class='error'>Post nie istnieje.</p>";
    include 'footer.php';
    exit;
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $post['user_id']) {
    include 'header.php';
    echo "<p class='error'>Nie masz uprawnień do usunięcia tego wpisu.</p>";
    include 'footer.php';
    exit;
}

if (!empty($post['image']) && file_exists('uploads/' . $post['image'])) {
    unlink('uploads/' . $post['image']);
}

$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$post_id]);

header('Location: index.php');
exit;

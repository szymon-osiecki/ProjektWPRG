<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getPDO();

    $post_id = intval($_POST['post_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');

    if ($post_id <= 0 || empty($content)) {
        header("Location: post.php?id=$post_id&error=1");
        exit;
    }

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $author_name = $_SESSION['username'];
    } else {
        $user_id = null;
        $author_name = 'Gość';
    }

    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, author_name, content) VALUES (?, ?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $author_name, $content]);

    header("Location: post.php?id=$post_id");
    exit;
} else {
    header("Location: index.php");
    exit;
}

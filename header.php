<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1><a href="index.php">Mój Blog</a></h1>
    <nav>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php">Logowanie</a>
            <a href="register.php">Rejestracja</a>
        <?php else: ?>
            Witaj, <?= htmlspecialchars($_SESSION['username']) ?> |
            <a href="add_post.php">Dodaj wpis</a>
            <a href="reset_password.php">Resetuj hasło</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin.php">Panel administratora</a>
            <?php endif; ?>
            <a href="logout.php">Wyloguj się</a>
        <?php endif; ?>
        <a href="contact.php">Kontakt</a>
    </nav>
    <hr>
</header>

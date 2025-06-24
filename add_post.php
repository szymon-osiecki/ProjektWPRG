<?php
session_start();
require_once 'database.php';
require_once 'auth.php';

redirectIfNotLoggedIn();
requireRole(['admin', 'author']);

$pdo = getPDO();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'author'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $userId = $_SESSION['user_id'];
    $imageName = null;

    if (empty($title) || empty($content)) {
        $error = 'Tytuł i treść są wymagane.';
    } else {
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = 'uploads/';
            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $uploadPath = $uploadDir . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $error = 'Nie udało się przesłać obrazka.';
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, image, user_id, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$title, $content, $imageName, $userId]);
            $success = 'Wpis został dodany!';
        }
    }
}

include 'header.php';
?>

<h2>Dodaj nowy wpis</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php elseif ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <label for="title">Tytuł:</label><br>
    <input type="text" name="title" id="title" required><br><br>

    <label for="content">Treść:</label><br>
    <textarea name="content" id="content" rows="8" required></textarea><br><br>

    <label for="image">Obrazek:</label><br>
    <input type="file" name="image" id="image"><br><br>

    <button type="submit">Dodaj wpis</button>
</form>

<p><a href="index.php">← Powrót do strony głównej</a></p>

<?php include 'footer.php'; ?>

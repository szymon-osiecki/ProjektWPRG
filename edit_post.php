<?php
session_start();
require_once 'database.php';
require_once 'auth.php';

redirectIfNotLoggedIn();
requireRole(['admin', 'author']);

$pdo = getPDO();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$post_id = (int)$_GET['id'];
$error = '';
$success = '';

// Pobranie posta
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post || ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $post['user_id'])) {
    include 'header.php';
    echo "<p class='error'>Brak dostępu do edycji tego posta.</p>";
    include 'footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image = $post['image'];

    if (empty($title) || empty($content)) {
        $error = "Tytuł i treść są wymagane.";
    } else {
        if (!empty($_FILES['image']['name'])) {
            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $imagePath = 'uploads/' . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                if (!empty($post['image']) && file_exists('uploads/' . $post['image'])) {
                    unlink('uploads/' . $post['image']);
                }
                $image = $imageName;
            } else {
                $error = "Nie udało się przesłać nowego obrazka.";
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ?");
            $stmt->execute([$title, $content, $image, $post_id]);
            $success = "Post został zaktualizowany!";
            // Przeładuj dane
            $post['title'] = $title;
            $post['content'] = $content;
            $post['image'] = $image;
        }
    }
}

include 'header.php';
?>

<h2>Edytuj post</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php elseif ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <label for="title">Tytuł:</label><br>
    <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title']) ?>" required><br><br>

    <label for="content">Treść:</label><br>
    <textarea name="content" id="content" rows="10" required><?= htmlspecialchars($post['content']) ?></textarea><br><br>

    <label for="image">Obrazek:</label><br>
    <?php if ($post['image']): ?>
        <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Miniatura" width="150"><br>
    <?php endif; ?>
    <input type="file" name="image" id="image"><br><br>

    <button type="submit">Zapisz zmiany</button>
</form>

<p><a href="index.php">← Powrót do strony głównej</a></p>

<?php include 'footer.php'; ?>

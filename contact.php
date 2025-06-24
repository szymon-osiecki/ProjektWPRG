<?php
session_start();

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Wszystkie pola są wymagane.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Nieprawidłowy adres e-mail.';
    } else {
        $log = "Imię: $name\nE-mail: $email\nWiadomość:\n$message\n-------------\n";
        file_put_contents('private/contact_messages.txt', $log, FILE_APPEND | LOCK_EX);
        $success = true;
    }
}

include 'header.php';
?>

<h2>Kontakt z autorem</h2>

<?php if ($success): ?>
    <p class="success">Dziękujemy za wiadomość! Skontaktujemy się wkrótce.</p>
<?php elseif ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="contact.php">
    <label for="name">Imię:</label><br>
    <input type="text" name="name" id="name" required><br><br>

    <label for="email">E-mail:</label><br>
    <input type="email" name="email" id="email" required><br><br>

    <label for="message">Wiadomość:</label><br>
    <textarea name="message" id="message" rows="6" required></textarea><br><br>

    <button type="submit">Wyślij</button>
</form>

<p><a href="index.php">← Powrót do strony głównej</a></p>

<?php include 'footer.php'; ?>

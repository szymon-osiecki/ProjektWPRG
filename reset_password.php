<?php
session_start();
require_once 'database.php';
include 'header.php';

$pdo = getPDO();
$message = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
        $message = 'Wszystkie pola są wymagane.';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Hasła nie są takie same.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateStmt->execute([$hashedPassword, $user['id']]);
                $message = 'Hasło zostało zresetowane.';
                $isSuccess = true;
            } else {
                $message = 'Użytkownik o tym adresie e-mail nie istnieje.';
            }
        } catch (PDOException $e) {
            $message = 'Błąd: ' . $e->getMessage();
        }
    }
}
?>

<main>
    <h2>Resetowanie hasła</h2>

    <?php if ($message): ?>
        <p class="<?= $isSuccess ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="post" class="form">
        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="new_password">Nowe hasło:</label><br>
        <input type="password" name="new_password" id="new_password" required><br><br>

        <label for="confirm_password">Potwierdź hasło:</label><br>
        <input type="password" name="confirm_password" id="confirm_password" required><br><br>

        <button type="submit">Zresetuj hasło</button>
    </form>
</main>

<?php include 'footer.php'; ?>

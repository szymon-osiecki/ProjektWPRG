<?php
session_start();
require_once 'database.php';
include 'header.php';

$pdo = getPDO();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Wprowadź e-mail i hasło.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit;
        } else {
            $error = 'Nieprawidłowy e-mail lub hasło.';
        }
    }

}
?>

<main>
    <h2>Logowanie</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" class="form">
        <label for="email">E-mail:</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="password">Hasło:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Zaloguj się</button>
    </form>

    <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
</main>

<?php include 'footer.php'; ?>

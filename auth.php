<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirectIfNotLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function requireRole($allowedRoles) {
    if (!isset($_SESSION['role'])) {
        header('Location: login.php');
        exit;
    }

    $userRole = $_SESSION['role'];

    $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];

    if (!in_array($userRole, $roles)) {
        header('HTTP/1.1 403 Forbidden');
        echo "<p style='color:red; font-weight:bold;'>Brak dostępu: wymagane role: " . implode(', ', $roles) . "</p>";
        exit;
    }
}

function isRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

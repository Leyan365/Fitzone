<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const FITZONE_ROLES = ['customer', 'management', 'admin'];

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header("Location: {$path}");
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): bool
{
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $postedToken = $_POST['csrf_token'] ?? '';

    return is_string($postedToken) && hash_equals($sessionToken, $postedToken);
}

function require_csrf(): void
{
    if (!verify_csrf()) {
        http_response_code(400);
        exit('Invalid request token.');
    }
}

function is_allowed_role(string $role): bool
{
    return in_array($role, FITZONE_ROLES, true);
}

function current_user(mysqli $conn): ?array
{
    if (empty($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        return null;
    }

    $userId = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare('SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        return null;
    }

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];

    return $user;
}

function require_login(mysqli $conn): array
{
    $user = current_user($conn);

    if (!$user) {
        session_unset();
        session_destroy();
        redirect('login.php');
    }

    return $user;
}

function require_role(array $user, array $roles): void
{
    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('Access denied.');
    }
}

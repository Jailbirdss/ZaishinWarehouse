<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';

$page   = $_GET['page']   ?? 'login';
$action = $_GET['action'] ?? 'index';

if ($page === 'logout') {
    session_destroy();
    setcookie('remember_me', '', time() - 3600, '/');
    header('Location: index.php?page=login');
    exit;
}

if ($page === 'login' && !empty($_SESSION['user_id'])) {
    header('Location: index.php?page=dashboard');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db   = getDB();
        $user = User::getByUsername($db, $username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']     = $user['id'];
            $_SESSION['user_name']   = $user['name'];
            $_SESSION['user_role']   = $user['role'];
            $_SESSION['user_avatar'] = $user['avatar'];

            if (!empty($_POST['remember'])) {
                $secret = getenv('APP_SECRET') ?: 'zaishin-wms-secure-key-189f36f9a0c';
                $signature = hash_hmac('sha256', $user['id'], $secret);
                $cookieValue = $user['id'] . '|' . $signature;
                setcookie('remember_me', $cookieValue, time() + 2592000, '/', '', false, true);
            }

            header('Location: index.php?page=dashboard');
            exit;
        }
        $error = 'Username atau password salah.';
    } else {
        $error = 'Silakan isi username dan password.';
    }
}

require __DIR__ . '/../Views/auth/login.php';

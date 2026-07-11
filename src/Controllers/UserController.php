<?php
requirePermission('users.view');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';
$db = getDB();

$action = $_GET['action'] ?? 'index';

if ($action === 'delete' && isset($_GET['id'])) {
    requirePermission('users.delete');
    User::delete($db, (int)$_GET['id']);
    header('Location: index.php?page=users&deleted=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        requirePermission('users.edit');
    } else {
        requirePermission('users.create');
    }
    
    $name     = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $role     = $_POST['role'] ?? '';
    $pw       = $_POST['password'] ?? '';

    User::save($db, $name, $username, $role, $pw, $id);
    header('Location: index.php?page=users&saved=1'); exit;
}

if (isset($_GET['toggle']) && isset($_GET['id'])) {
    requirePermission('users.toggle');
    User::toggleActive($db, (int)$_GET['id']);
    header('Location: index.php?page=users'); exit;
}

$users = User::getAll($db);
$roles = $db->query("SELECT * FROM roles ORDER BY display_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'Manajemen User';
$pageSubtitle = 'Pengaturan akun staf gudang, peran (role), hak akses, dan status keaktifan.';
ob_start();
require __DIR__ . '/../Views/users/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';


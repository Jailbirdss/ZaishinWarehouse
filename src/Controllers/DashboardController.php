<?php
requireLogin();

$role = $_SESSION['user_role'] ?? '';
if ($role === 'divisi_penjualan') {
    header('Location: index.php?page=sales-orders'); exit;
}
if ($role === 'divisi_pembelian') {
    header('Location: index.php?page=restock'); exit;
}

$pageTitle    = 'Dashboard';
$pageSubtitle = date('l, d F Y');
ob_start();
require __DIR__ . '/../Views/dashboard/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

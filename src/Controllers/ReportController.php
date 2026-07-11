<?php
requirePermission('reports.view');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Transaction.php';
$db    = getDB();
$from  = $_GET['from']  ?? date('Y-m-01');
$to    = $_GET['to']    ?? date('Y-m-d');
$type  = $_GET['type']  ?? 'all';

if (($_SESSION['user_role'] ?? '') === 'divisi_pembelian') {
    $type = 'inbound';
}


$transactions = Transaction::getReport($db, $from, $to, $type);
$totIn  = array_sum(array_map(fn($t) => $t['type'] === 'inbound' ? $t['quantity'] : 0, $transactions));
$totOut = array_sum(array_map(fn($t) => $t['type'] === 'outbound' ? $t['quantity'] : 0, $transactions));

$pageTitle = 'Laporan Transaksi';
$pageSubtitle = 'Unduh dan pantau log inbound, outbound, dan mutasi internal secara periodik.';
ob_start();
require __DIR__ . '/../Views/reports/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

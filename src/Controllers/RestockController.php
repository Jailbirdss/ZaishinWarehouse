<?php
requireRole(['admin_gudang','divisi_pembelian']);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Item.php';
$db    = getDB();
$items = Item::getSimpleList($db);

$pageTitle = 'Permintaan Restock';
$pageSubtitle = 'Pengajuan restock bahan cetak kritis dan persetujuan pengadaan barang.';
ob_start();
require __DIR__ . '/../Views/restock/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/SalesOrder.php';
require_once __DIR__ . '/../Models/Item.php';
require_once __DIR__ . '/../Models/Notification.php';

requirePermission('sales_orders.view');

if (empty($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$db = getDB();
$userId = (int)$_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? '';
$action = $_GET['action'] ?? 'index';

$error = '';
$success = '';

if ($action === 'create') {
    requirePermission('sales_orders.create');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customer = trim($_POST['customer'] ?? '');
        $postItems = $_POST['items'] ?? [];
        
        if (empty($customer)) {
            $error = 'Nama pelanggan tidak boleh kosong.';
        } elseif (empty($postItems)) {
            $error = 'Pilih minimal satu barang/bahan.';
        } else {
            $items = [];
            foreach ($postItems as $pItem) {
                $itemId = (int)($pItem['item_id'] ?? 0);
                $qty = (int)($pItem['quantity'] ?? 0);
                if ($itemId > 0 && $qty > 0) {
                    $items[] = [
                        'item_id' => $itemId,
                        'quantity' => $qty
                    ];
                }
            }
            
            if (empty($items)) {
                $error = 'Kuantitas barang/bahan cetak harus lebih besar dari 0.';
            } else {
                try {
                    $soNumber = SalesOrder::create($db, $customer, $userId, $items);
                    
                    $notifTitle = "Sales Order Baru: " . $soNumber;
                    $notifMsg = "Sales Order " . $soNumber . " untuk pelanggan " . $customer . " baru saja dibuat oleh " . $_SESSION['user_name'] . " dan siap untuk diproses.";
                    Notification::send($db, null, 'admin_gudang', 'so_created', $notifTitle, $notifMsg);
                    Notification::send($db, null, 'petugas_gudang', 'so_created', $notifTitle, $notifMsg);
                    
                    header("Location: index.php?page=sales-orders&success=1&so=" . urlencode($soNumber));
                    exit;
                } catch (Exception $e) {
                    $error = 'Gagal menyimpan Sales Order: ' . $e->getMessage();
                }
            }
        }
    }
    
    $itemsList = Item::getSimpleList($db);
    $pageTitle = 'Buat Sales Order Baru';
    $pageSubtitle = 'Masukkan pesanan bahan cetak pelanggan ke dalam sistem.';
    ob_start();
    require __DIR__ . '/../Views/sales_orders/create.php';
    $content = ob_get_clean();
    require __DIR__ . '/../Views/layouts/main.php';
    exit;
}

$salesOrders = SalesOrder::getAll($db);

if (!empty($_GET['success']) && !empty($_GET['so'])) {
    $success = "Sales Order <strong>" . htmlspecialchars($_GET['so']) . "</strong> berhasil dibuat.";
}

$pageTitle = 'Daftar Sales Order';
$pageSubtitle = 'Log seluruh pesanan penjualan bahan cetak dari pelanggan.';
ob_start();
require __DIR__ . '/../Views/sales_orders/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

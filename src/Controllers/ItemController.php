<?php
requirePermission('items.view');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Item.php';
$db     = getDB();
$action = $_GET['action'] ?? 'index';

if ($action === 'delete' && isset($_GET['id'])) {
    requirePermission('items.delete');
    Item::delete($db, (int)$_GET['id']);
    header('Location: index.php?page=items&deleted=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        requirePermission('items.edit');
    } else {
        requirePermission('items.create');
    }

    $sku         = $_POST['sku'] ?? '';
    $name        = $_POST['name'] ?? '';
    $categoryId  = (int)($_POST['category_id'] ?? 0);
    $unit        = $_POST['unit'] ?? 'pcs';
    $minStock    = (int)($_POST['min_stock'] ?? 10);
    $description = $_POST['description'] ?? '';

    Item::save($db, $sku, $name, $categoryId, $unit, $minStock, $description, $id);

    $checkId = $id > 0 ? $id : (int)$db->lastInsertId();

    if ($id === 0 && $checkId > 0) {
        $initStock  = (int)($_POST['initial_stock'] ?? 0);
        $initSlotId = (int)($_POST['initial_slot_id'] ?? 0);
        if ($initStock > 0 && $initSlotId > 0) {
            require_once __DIR__ . '/../Models/Transaction.php';
            $refNo = 'INIT-' . $sku . '-' . strtoupper(substr(uniqid(), -5));
            $userId = (int)$_SESSION['user_id'];
            $notes = 'Inisialisasi stok awal barang baru.';
            Transaction::inbound($db, $checkId, $initSlotId, $initStock, $refNo, $userId, $notes);
        }
    }

    if ($checkId > 0) {
        require_once __DIR__ . '/../Models/Notification.php';
        Notification::checkAndNotifyLowStock($db, $checkId);
    }

    header('Location: index.php?page=items&saved=1'); exit;
}

$search     = trim($_GET['q'] ?? '');
$catFilter  = (int)($_GET['cat'] ?? 0);
$items      = Item::search($db, $search, $catFilter);
$categories = Item::getCategories($db);
$editItem   = null;

if (($action === 'edit') && isset($_GET['id'])) {
    $editItem = Item::getById($db, (int)$_GET['id']);
}

require_once __DIR__ . '/../Models/Transaction.php';
$freeSlots = Transaction::getFreeSlots($db);

$pageTitle = 'Master Barang';
$pageSubtitle = 'Manajemen daftar bahan cetak, SKU, unit kemasan, dan kategori.';
ob_start();
require __DIR__ . '/../Views/items/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';


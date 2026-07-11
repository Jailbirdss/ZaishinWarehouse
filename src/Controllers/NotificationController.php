<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Notification.php';

requirePermission('notifications.view');

if (empty($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$db = getDB();
$userId = (int)$_SESSION['user_id'];
$role = $_SESSION['user_role'] ?? '';
$action = $_GET['action'] ?? 'index';

if ($action === 'mark_read') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        Notification::markAsRead($db, $id);
    }
    header('Location: index.php?page=notifications');
    exit;
}

if ($action === 'mark_all_read') {
    Notification::markAllAsRead($db, $userId, $role);
    header('Location: index.php?page=notifications');
    exit;
}

$notifications = Notification::getByUser($db, $userId, $role, 100);

$stmtPending = $db->query("SELECT DISTINCT item_id FROM restock_requests WHERE status = 'pending'");
$pendingRestockItemIds = array_map('intval', $stmtPending->fetchAll(PDO::FETCH_COLUMN));

$lowStockItemIds = [];
foreach ($notifications as $n) {
    if ($n['type'] === 'low_stock' && !empty($n['related_id'])) {
        $lowStockItemIds[] = (int)$n['related_id'];
    }
}
$restockRequestsByItemId = [];
if (!empty($lowStockItemIds)) {
    $lowStockItemIds = array_unique($lowStockItemIds);
    $placeholders = implode(',', array_fill(0, count($lowStockItemIds), '?'));
    $stmt = $db->prepare("SELECT item_id, status, created_at FROM restock_requests WHERE item_id IN ($placeholders) ORDER BY created_at ASC");
    $stmt->execute($lowStockItemIds);
    foreach ($stmt->fetchAll() as $row) {
        $restockRequestsByItemId[(int)$row['item_id']][] = [
            'status' => $row['status'],
            'created_at' => $row['created_at']
        ];
    }
}

$stmtReqStatus = $db->query("SELECT id, status FROM restock_requests");
$restockStatusByReqId = [];
foreach ($stmtReqStatus->fetchAll() as $row) {
    $restockStatusByReqId[(int)$row['id']] = $row['status'];
}

$stmtOpnameStatus = $db->query("SELECT id, status FROM stock_opnames");
$opnameStatusBySessionId = [];
foreach ($stmtOpnameStatus->fetchAll() as $row) {
    $opnameStatusBySessionId[(int)$row['id']] = $row['status'];
}

$pageTitle = 'Pusat Notifikasi';
$pageSubtitle = 'Pantau peringatan reorder point, selisih hasil opname, serta status pengadaan barang secara detail.';
ob_start();
require __DIR__ . '/../Views/notifications/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

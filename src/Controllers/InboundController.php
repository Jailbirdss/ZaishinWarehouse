<?php
requirePermission('inbound.view');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Item.php';
require_once __DIR__ . '/../Models/Transaction.php';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requirePermission('inbound.create');
    $action = $_POST['action'] ?? '';
    if ($action === 'record_arrival') {
        $poNumber = trim($_POST['po_number'] ?? '');
        $expedition = trim($_POST['expedition'] ?? '');
        $arrivalDate = trim($_POST['arrival_date'] ?? date('Y-m-d H:i:s'));
        if ($poNumber) {
            $stmt = $db->prepare("UPDATE restock_requests SET arrival_status = 'arrived', expedition = ?, arrival_date = ? WHERE po_number = ? OR (po_number IS NULL AND CONCAT('PO-RESTOCK-', id) = ?)");
            $stmt->execute([$expedition, $arrivalDate, $poNumber, $poNumber]);
        }
        header("Location: index.php?page=inbound&arrival_success=1");
        exit;
    }
    if ($action === 'complete_po') {
        $poNumber = trim($_POST['po_number'] ?? '');
        if ($poNumber) {
            $stmt = $db->prepare("UPDATE restock_requests SET status = 'completed', approved_by = ? WHERE po_number = ? OR (po_number IS NULL AND CONCAT('PO-RESTOCK-', id) = ?)");
            $stmt->execute([(int)$_SESSION['user_id'], $poNumber, $poNumber]);
        }
        header("Location: index.php?page=inbound&complete_success=1");
        exit;
    }

    $itemId           = (int)($_POST['item_id'] ?? 0);
    $slotId           = (int)($_POST['slot_id'] ?? 0);
    $qty              = (int)($_POST['quantity'] ?? 0);
    $notes            = trim($_POST['notes'] ?? '');
    $poNumber         = trim($_POST['po_number'] ?? '');
    $condition        = trim($_POST['condition'] ?? 'baik');
    $isDiscrepancy    = (int)($_POST['is_discrepancy'] ?? 0);
    $restockRequestId = (int)($_POST['restock_request_id'] ?? 0);
    $autoComplete     = (int)($_POST['auto_complete_po'] ?? 0);
    $refNo            = 'IN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

    require_once __DIR__ . '/../Models/Notification.php';

    if ($isDiscrepancy) {
        if ($itemId) {
            Transaction::recordDiscrepancy($db, $itemId, $qty, $refNo, (int)$_SESSION['user_id'], $notes, $poNumber ? $poNumber : null, $condition);
            if ($restockRequestId > 0) {
                $stmtUpdateQty = $db->prepare("UPDATE restock_requests SET received_qty = received_qty + ? WHERE id = ?");
                $stmtUpdateQty->execute([$qty, $restockRequestId]);
            }
            if ($autoComplete && $poNumber) {
                $stmtComplete = $db->prepare("UPDATE restock_requests SET status = 'completed', approved_by = ? WHERE po_number = ? OR (po_number IS NULL AND CONCAT('PO-RESTOCK-', id) = ?)");
                $stmtComplete->execute([(int)$_SESSION['user_id'], $poNumber, $poNumber]);
                Notification::checkAndNotifyLowStock($db, $itemId);
                header("Location: index.php?page=inbound&complete_success=1"); exit;
            }
            $stmtItem = $db->prepare("SELECT name, sku, unit FROM items WHERE id = ?");
            $stmtItem->execute([$itemId]);
            $itemInfo = $stmtItem->fetch();
            $itemName = $itemInfo ? $itemInfo['name'] : 'Barang';
            $itemSku = $itemInfo ? $itemInfo['sku'] : '';
            $itemUnit = $itemInfo ? $itemInfo['unit'] : '';
            $notifTitle = "Laporan Ketidaksesuaian Inbound: " . $itemName;
            $notifMsg = "Terdapat ketidaksesuaian pada penerimaan barang " . $itemName . " (" . $itemSku . "). Jumlah fisik: " . $qty . " " . $itemUnit . ". Kondisi: " . ucfirst(str_replace('_', ' ', $condition)) . ". Catatan: " . $notes . ". Nomor Ref: " . $refNo;
            Notification::send($db, null, 'admin_gudang', 'inbound_discrepancy', $notifTitle, $notifMsg, $itemId);
            Notification::send($db, null, 'divisi_pembelian', 'inbound_discrepancy', $notifTitle, $notifMsg, $itemId);
            Notification::send($db, null, 'manajemen', 'inbound_discrepancy', $notifTitle, $notifMsg, $itemId);
            Notification::checkAndNotifyLowStock($db, $itemId);
            header("Location: index.php?page=inbound&success=1&ref=$refNo&discrepancy=1"); exit;
        }
    } else {
        if ($itemId && $slotId && $qty > 0) {
            Transaction::inbound($db, $itemId, $slotId, $qty, $refNo, (int)$_SESSION['user_id'], $notes, $poNumber ? $poNumber : null, $condition);
            if ($restockRequestId > 0) {
                $stmtUpdateQty = $db->prepare("UPDATE restock_requests SET received_qty = received_qty + ? WHERE id = ?");
                $stmtUpdateQty->execute([$qty, $restockRequestId]);
            }
            if ($autoComplete && $poNumber) {
                $stmtComplete = $db->prepare("UPDATE restock_requests SET status = 'completed', approved_by = ? WHERE po_number = ? OR (po_number IS NULL AND CONCAT('PO-RESTOCK-', id) = ?)");
                $stmtComplete->execute([(int)$_SESSION['user_id'], $poNumber, $poNumber]);
                Notification::checkAndNotifyLowStock($db, $itemId);
                header("Location: index.php?page=inbound&complete_success=1"); exit;
            }
            Notification::checkAndNotifyLowStock($db, $itemId);
            header("Location: index.php?page=inbound&success=1&ref=$refNo"); exit;
        }
    }
    $error = 'Data tidak lengkap.';
}

$rawPOs = $db->query("
    SELECT rr.*, i.name AS item_name, i.sku AS item_sku, i.unit AS item_unit, i.qr_code AS item_qr,
           c.name AS category_name, u1.name AS requester_name, u2.name AS approver_name
    FROM restock_requests rr
    JOIN items i ON rr.item_id = i.id
    JOIN categories c ON i.category_id = c.id
    JOIN users u1 ON rr.requested_by = u1.id
    LEFT JOIN users u2 ON rr.approved_by = u2.id
    WHERE rr.status IN ('approved', 'completed')
    ORDER BY rr.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$activePOs = [];
foreach ($rawPOs as $row) {
    $poNum = $row['po_number'] ?: 'PO-RESTOCK-' . $row['id'];
    if (!isset($activePOs[$poNum])) {
        $activePOs[$poNum] = [
            'po_number' => $poNum,
            'supplier_name' => $row['supplier_name'] ?: 'CV Mitra Kertas Indo',
            'supplier_address' => $row['supplier_address'] ?: 'Jl. Industri No. 45, Cikupa, Tangerang',
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'requester_name' => $row['requester_name'],
            'approver_name' => $row['approver_name'] ?: 'Etmin Datang',
            'arrival_status' => $row['arrival_status'] ?: 'pending',
            'arrival_date' => $row['arrival_date'],
            'expedition' => $row['expedition'],
            'items' => []
        ];
    }
    $activePOs[$poNum]['items'][] = [
        'id' => $row['id'],
        'item_id' => $row['item_id'],
        'item_name' => $row['item_name'],
        'item_sku' => $row['item_sku'],
        'item_unit' => $row['item_unit'],
        'item_qr' => $row['item_qr'],
        'category_name' => $row['category_name'],
        'requested_qty' => (int)$row['requested_qty'],
        'received_qty' => (int)$row['received_qty'],
        'item_price' => (int)$row['item_price'],
        'status' => $row['status']
    ];
}

$items     = Item::getAll($db);
$freeSlots = Transaction::getFreeSlots($db);

$pageTitle = 'Barang Masuk (Inbound)';
$pageSubtitle = 'Penerimaan barang masuk dari Purchase Order (PO) restock atau input manual.';
ob_start();
require __DIR__ . '/../Views/inbound/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

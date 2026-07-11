<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Restock.php';
require_once __DIR__ . '/../src/Models/Transaction.php';
require_once __DIR__ . '/../src/Models/Notification.php';

header('Content-Type: application/json');
if (empty($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (!hasPermission('restock.view')) { http_response_code(403); echo json_encode(['error' => 'Tidak punya izin']); exit; }
    $status = $_GET['status'] ?? 'all';
    $itemId = (int)($_GET['item_id'] ?? 0);

    if ($itemId > 0) {
        $stmt = $db->prepare("SELECT rr.*, u.name AS requester_name
                              FROM restock_requests rr
                              JOIN users u ON rr.requested_by = u.id
                              WHERE rr.item_id = ? AND rr.status = 'approved'
                              ORDER BY rr.created_at DESC");
        $stmt->execute([$itemId]);
        echo json_encode($stmt->fetchAll());
        exit;
    }

    $requests = Restock::getRequests($db, $status);
    echo json_encode($requests);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? 'create';

    if ($action === 'create') {
        if (!hasPermission('restock.create')) { http_response_code(403); echo json_encode(['error' => 'Tidak punya izin']); exit; }

        $itemId = (int)($data['item_id'] ?? 0);
        $qty    = (int)($data['requested_qty'] ?? 0);
        $notes  = trim($data['notes'] ?? '');

        if (!$itemId || $qty <= 0) { echo json_encode(['error' => 'Data tidak valid']); exit; }

        $currStock = Transaction::getTotalStock($db, $itemId);

        $requestId = Restock::createRequest($db, $itemId, $qty, $currStock, (int)$_SESSION['user_id'], $notes);

        $stmtItem = $db->prepare("SELECT name FROM items WHERE id = ?");
        $stmtItem->execute([$itemId]);
        $itemName = $stmtItem->fetchColumn();

        $notifTitle = "Permintaan Restock Baru";
        $notifMsg = $_SESSION['user_name'] . " mengajukan restock untuk barang " . $itemName . " sebanyak " . $qty . " unit.";
        Notification::send($db, null, 'admin_gudang', 'restock_submitted', $notifTitle, $notifMsg, $requestId);

        echo json_encode(['success' => true, 'id' => $requestId]);

    } elseif ($action === 'approve' || $action === 'reject') {
        if (!hasPermission('restock.approve')) {
            http_response_code(403); echo json_encode(['error' => 'Tidak punya izin']); exit;
        }
        $id     = (int)($data['id'] ?? 0);
        $status = $action === 'approve' ? 'approved' : 'rejected';

        $stmtReq = $db->prepare("SELECT rr.requested_by, rr.requested_qty, i.name AS item_name
                                 FROM restock_requests rr
                                 JOIN items i ON rr.item_id = i.id
                                 WHERE rr.id = ?");
        $stmtReq->execute([$id]);
        $reqDetails = $stmtReq->fetch();

        Restock::updateStatus($db, $id, $status, (int)$_SESSION['user_id']);

        if ($reqDetails) {
            $requesterId = (int)$reqDetails['requested_by'];
            $itemName = $reqDetails['item_name'];
            $reqQty = $reqDetails['requested_qty'];

            $statusText = $status === 'approved' ? 'Disetujui' : 'Ditolak';
            $type = $status === 'approved' ? 'restock_approved' : 'restock_rejected';
            $notifTitle = "Permintaan Restock " . $statusText;
            $notifMsg = "Permintaan restock Anda untuk barang " . $itemName . " sebanyak " . $reqQty . " unit telah " . strtolower($statusText) . " oleh " . $_SESSION['user_name'] . ".";
            Notification::send($db, $requesterId, null, $type, $notifTitle, $notifMsg, $id);
        }

        echo json_encode(['success' => true]);
    }
    exit;
}

echo json_encode(['error' => 'Method not allowed']);

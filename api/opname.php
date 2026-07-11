<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Opname.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $opnameId = (int)($_GET['opname_id'] ?? 0);
    $type = $_GET['type'] ?? '';
    $scannedCode = trim($_GET['scanned_code'] ?? '');

    if ($opnameId <= 0 || !$type || !$scannedCode) {
        echo json_encode(['error' => 'Parameter tidak lengkap']);
        exit;
    }

    if ($type === 'verify_rack') {
        if (preg_match('/^SLOT-([A-Z0-9\-]+)-S([1-8])$/i', $scannedCode, $matches)) {
            $rackCode = $matches[1];
            $slotNumber = (int)$matches[2];

            $stmt = $db->prepare("SELECT rs.id, rs.slot_number, r.rack_code, r.zone_id, z.name AS zone_name
                                  FROM rack_slots rs
                                  JOIN racks r ON rs.rack_id = r.id
                                  JOIN zones z ON r.zone_id = z.id
                                  WHERE r.rack_code = ? AND rs.slot_number = ?");
            $stmt->execute([$rackCode, $slotNumber]);
            $slot = $stmt->fetch();

            if (!$slot) {
                echo json_encode(['error' => 'Slot rak tidak terdaftar di database.']);
                exit;
            }

            $stmt = $db->prepare("SELECT COUNT(*) FROM stock_opname_details od
                                  JOIN rack_slots rs ON od.rack_slot_id = rs.id
                                  JOIN racks r ON rs.rack_id = r.id
                                  WHERE od.stock_opname_id = ? AND r.zone_id = ?");
            $stmt->execute([$opnameId, $slot['zone_id']]);
            $inScope = (int)$stmt->fetchColumn() > 0;

            $stmt = $db->prepare("SELECT COUNT(*) FROM stock_opname_details WHERE stock_opname_id = ?");
            $stmt->execute([$opnameId]);
            $totalDetails = (int)$stmt->fetchColumn();

            if ($totalDetails > 0 && !$inScope) {
                echo json_encode(['error' => 'Slot rak ini di luar jangkauan area audit opname aktif.']);
                exit;
            }

            echo json_encode([
                'success' => true,
                'slot_id' => (int)$slot['id'],
                'rack_code' => $slot['rack_code'],
                'slot_number' => (int)$slot['slot_number'],
                'zone_name' => $slot['zone_name']
            ]);
            exit;
        } else {
            echo json_encode(['error' => 'Format QR Rak salah. Gunakan format: SLOT-[RACK_CODE]-S[SLOT_NUM]']);
            exit;
        }
    }

    elseif ($type === 'verify_item') {
        $slotId = (int)($_GET['slot_id'] ?? 0);
        if ($slotId <= 0) {
            echo json_encode(['error' => 'Slot ID diperlukan']);
            exit;
        }

        $stmt = $db->prepare("SELECT id, name, sku FROM items WHERE qr_code = ?");
        $stmt->execute([$scannedCode]);
        $item = $stmt->fetch();

        if (!$item) {
            echo json_encode(['error' => 'Barang tidak ditemukan. Pastikan QR Code merupakan barcode SKU barang terdaftar.']);
            exit;
        }

        $stmt = $db->prepare("SELECT quantity FROM stock WHERE item_id = ? AND rack_slot_id = ?");
        $stmt->execute([$item['id'], $slotId]);
        $stockRow = $stmt->fetch();
        $systemQty = $stockRow ? (int)$stockRow['quantity'] : 0;

        echo json_encode([
            'success' => true,
            'item_id' => (int)$item['id'],
            'name' => $item['name'],
            'sku' => $item['sku'],
            'system_qty' => $systemQty
        ]);
        exit;
    }
}

elseif ($method === 'POST') {
    $opnameId = (int)($_POST['opname_id'] ?? 0);
    $type = $_POST['type'] ?? '';
    $slotId = (int)($_POST['slot_id'] ?? 0);
    $itemId = (int)($_POST['item_id'] ?? 0);
    $physicalQty = isset($_POST['physical_qty']) ? (int)$_POST['physical_qty'] : -1;

    if ($opnameId <= 0 || $type !== 'submit_qty' || $slotId <= 0 || $itemId <= 0 || $physicalQty < 0) {
        echo json_encode(['error' => 'Parameter data submit tidak lengkap / tidak valid']);
        exit;
    }

    try {
        Opname::updatePhysicalQuantity($db, $opnameId, $itemId, $slotId, $physicalQty);
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['error' => 'Gagal menyimpan hasil: ' . $e->getMessage()]);
        exit;
    }
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);

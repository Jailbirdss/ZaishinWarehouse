<?php
requirePermission('outbound.view');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Item.php';
require_once __DIR__ . '/../Models/Transaction.php';

$db = getDB();
$error = '';

require_once __DIR__ . '/../Models/SalesOrder.php';
$pendingSOs = SalesOrder::getPending($db);

function processSalesOrder(PDO $db, array $so) {
    $enrichedItems = [];
    $isStockSufficient = true;
    $pickingList = [];

    foreach ($so['items'] as $soItem) {
        $stmt = $db->prepare("SELECT i.id, i.name, i.sku, i.unit, i.qr_code, i.min_stock,
                              (SELECT COALESCE(SUM(quantity),0) FROM stock WHERE item_id = i.id) as total_stock
                              FROM items i WHERE i.sku = ?");
        $stmt->execute([$soItem['sku']]);
        $item = $stmt->fetch();

        if (!$item) {
            $isStockSufficient = false;
            $enrichedItems[] = [
                'sku' => $soItem['sku'],
                'name' => 'Barang Tidak Terdaftar',
                'qty_ordered' => $soItem['qty'],
                'total_stock' => 0,
                'sufficient' => false
            ];
            continue;
        }

        $sufficient = (int)$item['total_stock'] >= $soItem['qty'];
        if (!$sufficient) {
            $isStockSufficient = false;
        }

        $enrichedItems[] = [
            'id' => $item['id'],
            'sku' => $item['sku'],
            'name' => $item['name'],
            'unit' => $item['unit'],
            'qr_code' => $item['qr_code'],
            'qty_ordered' => $soItem['qty'],
            'total_stock' => (int)$item['total_stock'],
            'min_stock' => (int)$item['min_stock'],
            'sufficient' => $sufficient
        ];

        if ($sufficient) {
            $needed = $soItem['qty'];
            $slotStmt = $db->prepare("SELECT s.rack_slot_id as slot_id, s.quantity, r.rack_code, rs.slot_number, z.name AS zone_name
                                      FROM stock s
                                      JOIN rack_slots rs ON s.rack_slot_id = rs.id
                                      JOIN racks r ON rs.rack_id = r.id
                                      JOIN zones z ON r.zone_id = z.id
                                      WHERE s.item_id = ? AND s.quantity > 0
                                      ORDER BY s.received_at ASC");
            $slotStmt->execute([$item['id']]);
            $slots = $slotStmt->fetchAll();

            foreach ($slots as $slot) {
                if ($needed <= 0) break;

                $pickQty = min($needed, (int)$slot['quantity']);
                $pickingList[] = [
                    'item_id' => $item['id'],
                    'item_name' => $item['name'],
                    'sku' => $item['sku'],
                    'unit' => $item['unit'],
                    'item_qr_code' => $item['qr_code'],
                    'slot_id' => $slot['slot_id'],
                    'rack_code' => $slot['rack_code'],
                    'slot_number' => $slot['slot_number'],
                    'zone_name' => $slot['zone_name'],
                    'rack_qr_code' => "SLOT-{$slot['rack_code']}-S{$slot['slot_number']}",
                    'qty_to_pick' => $pickQty,
                    'available_in_slot' => (int)$slot['quantity']
                ];

                $needed -= $pickQty;
            }
        }
    }

    return [
        'so_number' => $so['so_number'],
        'customer' => $so['customer'],
        'date' => $so['date'],
        'items' => $enrichedItems,
        'picking_list' => $pickingList,
        'is_stock_sufficient' => $isStockSufficient
    ];
}

$processedSOs = [];
foreach ($pendingSOs as $so) {
    $processedSOs[] = processSalesOrder($db, $so);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requirePermission('outbound.create');
    $soNumber = trim($_POST['so_number'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $userId = (int)$_SESSION['user_id'];

    $targetSO = null;
    foreach ($processedSOs as $pSO) {
        if ($pSO['so_number'] === $soNumber) {
            $targetSO = $pSO;
            break;
        }
    }

    if ($targetSO) {
        if (!$targetSO['is_stock_sufficient']) {
            $error = "Gagal memproses outbound: Stok tidak mencukupi untuk Sales Order ini.";
        } else {
            try {
                $baseRefNo = 'OUT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
                $lowStockAlerts = [];
                $itemIndex = 1;
                $pickCount = count($targetSO['picking_list']);

                foreach ($targetSO['picking_list'] as $pick) {

                    $refNo = $pickCount > 1 ? "{$baseRefNo}-{$itemIndex}" : $baseRefNo;
                    $itemIndex++;

                    Transaction::outbound($db, $pick['item_id'], $pick['slot_id'], $pick['qty_to_pick'], $refNo, $userId, $notes, $soNumber);

                    $totalStock = Transaction::getTotalStock($db, $pick['item_id']);
                    $itemInfo = Item::getById($db, $pick['item_id']);
                    $min = (int)($itemInfo['min_stock'] ?? 10);

                    if ($totalStock <= $min) {
                        $lowStockAlerts[] = [
                            'name' => $itemInfo['name'],
                            'sku' => $itemInfo['sku'],
                            'stock' => $totalStock,
                            'min' => $min
                        ];

                        $stmtCheck = $db->prepare("SELECT COUNT(*) FROM notifications WHERE type = 'low_stock' AND related_id = ? AND is_read = 0");
                        $stmtCheck->execute([$pick['item_id']]);
                        $hasUnreadNotif = (int)$stmtCheck->fetchColumn() > 0;

                        if (!$hasUnreadNotif) {
                            require_once __DIR__ . '/../Models/Notification.php';
                            $notifTitle = "Alarm Stok Rendah: " . $itemInfo['name'];
                            $notifMsg = "Stok barang " . $itemInfo['name'] . " (" . $itemInfo['sku'] . ") saat ini menipis menjadi " . $totalStock . " " . $itemInfo['unit'] . " (Batas minimum: " . $min . " " . $itemInfo['unit'] . "). Segera ajukan restock.";
                            Notification::send($db, null, 'admin_gudang', 'low_stock', $notifTitle, $notifMsg, $pick['item_id']);
                            Notification::send($db, null, 'divisi_pembelian', 'low_stock', $notifTitle, $notifMsg, $pick['item_id']);
                        }
                    }
                }

                $stmtSO = $db->prepare("SELECT created_by, customer FROM sales_orders WHERE so_number = ?");
                $stmtSO->execute([$soNumber]);
                $soInfo = $stmtSO->fetch();

                SalesOrder::complete($db, $soNumber);

                if ($soInfo) {
                    $creatorId = (int)$soInfo['created_by'];
                    $customerName = $soInfo['customer'];
                    require_once __DIR__ . '/../Models/Notification.php';
                    $notifTitle = "Sales Order Selesai: " . $soNumber;
                    $notifMsg = "Sales Order " . $soNumber . " untuk pelanggan " . $customerName . " telah selesai diproses (picking list selesai).";
                    Notification::send($db, $creatorId, null, 'so_completed', $notifTitle, $notifMsg);
                }

                $lowItemsStr = '';
                if (!empty($lowStockAlerts)) {
                    $lowItemsStr = '&low_items=' . urlencode(json_encode($lowStockAlerts));
                }

                header("Location: index.php?page=outbound&success=1&ref=$baseRefNo&so=$soNumber" . $lowItemsStr);
                exit;
            } catch (Exception $e) {
                $error = "Terjadi kesalahan database: " . $e->getMessage();
            }
        }
    } else {
        $error = "Sales Order tidak valid atau tidak ditemukan.";
    }
}

$pageTitle = 'Barang Keluar (Outbound)';
$pageSubtitle = 'Pemrosesan pengeluaran barang dari gudang berdasarkan Sales Order (SO).';
ob_start();
require __DIR__ . '/../Views/outbound/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

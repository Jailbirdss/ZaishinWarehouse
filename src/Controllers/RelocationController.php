<?php

requirePermission('relocation.view');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Item.php';
require_once __DIR__ . '/../Models/Transaction.php';

$db = getDB();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requirePermission('relocation.create');
    $itemId    = (int)($_POST['item_id'] ?? 0);
    $srcSlotId = (int)($_POST['src_slot_id'] ?? 0);
    $dstSlotId = (int)($_POST['dst_slot_id'] ?? 0);
    $qty       = (int)($_POST['quantity'] ?? 0);
    $notes     = trim($_POST['notes'] ?? '');

    if (!$itemId || !$srcSlotId || !$dstSlotId || $qty <= 0) {
        $error = 'Data tidak lengkap.';
    } elseif ($srcSlotId === $dstSlotId) {
        $error = 'Slot asal dan slot tujuan tidak boleh sama.';
    } else {
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT quantity FROM stock WHERE item_id = ? AND rack_slot_id = ?");
            $stmt->execute([$itemId, $srcSlotId]);
            $srcStock = (int)$stmt->fetchColumn();

            if ($srcStock < $qty) {
                $error = "Stok di slot asal tidak mencukupi (Tersedia: $srcStock).";
                $db->rollBack();
            } else {
                $newSrcQty = $srcStock - $qty;
                if ($newSrcQty <= 0) {

                    $db->prepare("DELETE FROM stock WHERE item_id = ? AND rack_slot_id = ?")->execute([$itemId, $srcSlotId]);
                    $db->prepare("UPDATE rack_slots SET status = 'free' WHERE id = ?")->execute([$srcSlotId]);
                } else {
                    $db->prepare("UPDATE stock SET quantity = ? WHERE item_id = ? AND rack_slot_id = ?")->execute([$newSrcQty, $itemId, $srcSlotId]);
                }

                $db->prepare("UPDATE rack_slots SET status = 'loaded' WHERE id = ?")->execute([$dstSlotId]);

                $db->prepare("INSERT INTO stock (item_id, rack_slot_id, quantity)
                              VALUES (?, ?, ?)
                              ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)")
                   ->execute([$itemId, $dstSlotId, $qty]);

                $getSlotCode = function($slotId) use ($db) {
                    $stmt = $db->prepare("SELECT CONCAT(z.code, '-', r.rack_code, '-S', rs.slot_number) FROM rack_slots rs JOIN racks r ON rs.rack_id = r.id JOIN zones z ON r.zone_id = z.id WHERE rs.id = ?");
                    $stmt->execute([$slotId]);
                    return $stmt->fetchColumn();
                };
                $srcCode = $getSlotCode($srcSlotId);
                $dstCode = $getSlotCode($dstSlotId);

                $userId = (int)$_SESSION['user_id'];
                $uniq = strtoupper(substr(uniqid(), -5));
                $refOut = 'MUT-' . date('Ymd') . '-' . $uniq . '-OUT';
                $refIn  = 'MUT-' . date('Ymd') . '-' . $uniq . '-IN';

                $outNotes = "Mutasi ke slot $dstCode. " . ($notes ? "Catatan: $notes" : "");
                $inNotes  = "Mutasi dari slot $srcCode. " . ($notes ? "Catatan: $notes" : "");

                $db->prepare("INSERT INTO transactions (reference_no, item_id, type, quantity, rack_slot_id, user_id, notes) VALUES (?, ?, 'outbound', ?, ?, ?, ?)")
                   ->execute([$refOut, $itemId, $qty, $srcSlotId, $userId, $outNotes]);

                $db->prepare("INSERT INTO transactions (reference_no, item_id, type, quantity, rack_slot_id, user_id, notes) VALUES (?, ?, 'inbound', ?, ?, ?, ?)")
                   ->execute([$refIn, $itemId, $qty, $dstSlotId, $userId, $inNotes]);

                $db->commit();

                header("Location: index.php?page=relocation&success=1&ref=" . urlencode($refOut));
                exit;
            }
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $error = 'Terjadi kesalahan database: ' . $e->getMessage();
        }
    }
}

$items = $db->query("SELECT DISTINCT i.id, i.name, i.sku, i.unit
                     FROM items i
                     JOIN stock s ON i.id = s.item_id
                     WHERE s.quantity > 0
                     ORDER BY i.name")->fetchAll();

$freeSlots = Transaction::getFreeSlots($db);

$stocks = $db->query("SELECT s.item_id, s.rack_slot_id AS slot_id, s.quantity,
                             r.rack_code, rs.slot_number, z.name AS zone_name
                      FROM stock s
                      JOIN rack_slots rs ON s.rack_slot_id = rs.id
                      JOIN racks r ON rs.rack_id = r.id
                      JOIN zones z ON r.zone_id = z.id
                      WHERE s.quantity > 0
                      ORDER BY r.rack_code, rs.slot_number")->fetchAll(PDO::FETCH_ASSOC);

$successMsg = '';
if (!empty($_GET['success'])) {
    $successMsg = "Mutasi stok berhasil dilakukan. Log mutasi dicatat dengan referensi <strong>" . htmlspecialchars($_GET['ref'] ?? '') . "</strong>.";
}

$pageTitle = 'Mutasi Stok Internal';
$pageSubtitle = 'Pindahkan barang cetak antar-slot untuk konsolidasi atau pembersihan rak.';
ob_start();
require __DIR__ . '/../Views/relocation/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

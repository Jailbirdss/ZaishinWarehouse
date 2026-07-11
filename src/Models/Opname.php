<?php

class Opname {

    public static function getActive(PDO $db) {
        $stmt = $db->query("SELECT o.*, u.name AS creator_name
                            FROM stock_opnames o
                            JOIN users u ON o.created_by = u.id
                            WHERE o.status = 'initiated'
                            LIMIT 1");
        return $stmt->fetch();
    }

    public static function getHistory(PDO $db) {
        return $db->query("SELECT o.*, u.name AS creator_name,
                            (SELECT COUNT(*) FROM stock_opname_details WHERE stock_opname_id=o.id) as total_items,
                            (SELECT COUNT(*) FROM stock_opname_details WHERE stock_opname_id=o.id AND discrepancy != 0) as discrepancy_items
                            FROM stock_opnames o
                            JOIN users u ON o.created_by = u.id
                            WHERE o.status = 'completed'
                            ORDER BY o.completed_at DESC")->fetchAll();
    }

    public static function create(PDO $db, string $opnameNo, int $userId, array $slotIds): int {
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("INSERT INTO stock_opnames (opname_no, status, created_by) VALUES (?, 'initiated', ?)");
            $stmt->execute([$opnameNo, $userId]);
            $opnameId = (int)$db->lastInsertId();

            if (!empty($slotIds)) {
                $placeholders = implode(',', array_fill(0, count($slotIds), '?'));
                $sql = "INSERT INTO stock_opname_details (stock_opname_id, item_id, rack_slot_id, system_quantity)
                        SELECT ?, item_id, rack_slot_id, quantity
                        FROM stock
                        WHERE rack_slot_id IN ($placeholders)";

                $stmt = $db->prepare($sql);
                $params = array_merge([$opnameId], $slotIds);
                $stmt->execute($params);
            }

            require_once __DIR__ . '/Notification.php';
            $notifTitle = "Sesi Stock Opname Dimulai";
            $notifMsg = "Sesi Stock Opname " . $opnameNo . " baru saja diinisiasi. Petugas lapangan diharap segera melakukan pemeriksaan fisik menggunakan pemindai QR.";
            Notification::send($db, null, 'petugas_gudang', 'opname_initiated', $notifTitle, $notifMsg, $opnameId);

            $db->commit();
            return $opnameId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getDetails(PDO $db, int $opnameId) {
        $sql = "SELECT od.*, i.name AS item_name, i.sku, i.unit, r.rack_code, z.name AS zone_name, rs.slot_number
                FROM stock_opname_details od
                JOIN items i ON od.item_id = i.id
                JOIN rack_slots rs ON od.rack_slot_id = rs.id
                JOIN racks r ON rs.rack_id = r.id
                JOIN zones z ON r.zone_id = z.id
                WHERE od.stock_opname_id = ?
                ORDER BY z.name, r.rack_code, rs.slot_number";
        $stmt = $db->prepare($sql);
        $stmt->execute([$opnameId]);
        return $stmt->fetchAll();
    }

    public static function updatePhysicalQuantity(PDO $db, int $opnameId, int $itemId, int $slotId, int $physicalQty) {
        $stmt = $db->prepare("SELECT id, system_quantity FROM stock_opname_details
                              WHERE stock_opname_id = ? AND item_id = ? AND rack_slot_id = ?");
        $stmt->execute([$opnameId, $itemId, $slotId]);
        $row = $stmt->fetch();

        if ($row) {
            $detailId = $row['id'];
            $systemQty = (int)$row['system_quantity'];
            $discrepancy = $physicalQty - $systemQty;

            $stmt = $db->prepare("UPDATE stock_opname_details
                                  SET physical_quantity = ?, discrepancy = ?, status = 'verified', verified_at = NOW()
                                  WHERE id = ?");
            $stmt->execute([$physicalQty, $discrepancy, $detailId]);
        } else {
            $stmt = $db->prepare("SELECT quantity FROM stock WHERE item_id = ? AND rack_slot_id = ?");
            $stmt->execute([$itemId, $slotId]);
            $stockRow = $stmt->fetch();
            $systemQty = $stockRow ? (int)$stockRow['quantity'] : 0;

            $discrepancy = $physicalQty - $systemQty;

            $stmt = $db->prepare("INSERT INTO stock_opname_details
                                  (stock_opname_id, item_id, rack_slot_id, system_quantity, physical_quantity, discrepancy, status, verified_at)
                                  VALUES (?, ?, ?, ?, ?, ?, 'verified', NOW())");
            $stmt->execute([$opnameId, $itemId, $slotId, $systemQty, $physicalQty, $discrepancy]);
        }
        return true;
    }

    public static function finalize(PDO $db, int $opnameId, int $userId) {
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("SELECT opname_no FROM stock_opnames WHERE id = ?");
            $stmt->execute([$opnameId]);
            $opname = $stmt->fetch();
            if (!$opname) {
                throw new Exception("Sesi opname tidak ditemukan.");
            }
            $opnameNo = $opname['opname_no'];

            $stmt = $db->prepare("SELECT * FROM stock_opname_details WHERE stock_opname_id = ? AND status = 'verified'");
            $stmt->execute([$opnameId]);
            $details = $stmt->fetchAll();

            foreach ($details as $det) {
                $itemId = (int)$det['item_id'];
                $slotId = (int)$det['rack_slot_id'];
                $physicalQty = (int)$det['physical_quantity'];
                $systemQty = (int)$det['system_quantity'];
                $discrepancy = (int)$det['discrepancy'];

                if ($discrepancy === 0) continue;

                if ($physicalQty > 0) {
                    $stmtStock = $db->prepare("INSERT INTO stock (item_id, rack_slot_id, quantity)
                                               VALUES (?, ?, ?)
                                               ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)");
                    $stmtStock->execute([$itemId, $slotId, $physicalQty]);

                    $stmtSlot = $db->prepare("UPDATE rack_slots SET status = 'loaded' WHERE id = ?");
                    $stmtSlot->execute([$slotId]);
                } else {
                    $stmtStock = $db->prepare("DELETE FROM stock WHERE item_id = ? AND rack_slot_id = ?");
                    $stmtStock->execute([$itemId, $slotId]);

                    $stmtSlot = $db->prepare("UPDATE rack_slots SET status = 'free' WHERE id = ?");
                    $stmtSlot->execute([$slotId]);
                }

                $txType = $discrepancy > 0 ? 'inbound' : 'outbound';
                $txQty = abs($discrepancy);
                $notes = "[STOCK OPNAME ADJ] Penyesuaian dari Opname $opnameNo. Sistem: $systemQty, Fisik: $physicalQty.";

                $txRef = 'ADJ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

                $stmtTx = $db->prepare("INSERT INTO transactions (reference_no, item_id, type, quantity, rack_slot_id, user_id, notes)
                                        VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmtTx->execute([$txRef, $itemId, $txType, $txQty, $slotId, $userId, $notes]);
            }

            $stmtFinal = $db->prepare("UPDATE stock_opnames SET status = 'completed', completed_at = NOW() WHERE id = ?");
            $stmtFinal->execute([$opnameId]);

            require_once __DIR__ . '/Notification.php';
            $notifTitle = "Sesi Stock Opname Selesai";
            $notifMsg = "Sesi Stock Opname " . $opnameNo . " telah selesai difinalisasi. Data kuantitas stok di sistem telah disinkronkan sesuai hasil perhitungan fisik lapangan.";
            Notification::send($db, null, 'admin_gudang', 'opname_completed', $notifTitle, $notifMsg, $opnameId);
            Notification::send($db, null, 'manajemen', 'opname_completed', $notifTitle, $notifMsg, $opnameId);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function cancel(PDO $db, int $opnameId) {
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("DELETE FROM stock_opname_details WHERE stock_opname_id = ?");
            $stmt->execute([$opnameId]);

            $stmt = $db->prepare("DELETE FROM stock_opnames WHERE id = ?");
            $stmt->execute([$opnameId]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}

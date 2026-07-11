<?php

class Zone {

    public static function getAllWithCapacity(PDO $db) {
        return $db->query("SELECT * FROM v_zone_capacity ORDER BY zone_id")->fetchAll();
    }

    public static function getRacksWithSlotsByZone(PDO $db, int $zoneId) {
        $stmt = $db->prepare("SELECT r.id, r.rack_code, r.row_num, r.col_num, r.total_slots
            FROM racks r WHERE r.zone_id=? ORDER BY r.rack_code");
        $stmt->execute([$zoneId]);
        $racks = $stmt->fetchAll();

        foreach ($racks as &$rack) {
            $sStmt = $db->prepare("SELECT rs.id, rs.slot_number, rs.status,
                s.quantity, i.name AS item_name, i.sku, i.unit, c.name AS category
                FROM rack_slots rs
                LEFT JOIN stock s ON rs.id = s.rack_slot_id
                LEFT JOIN items i ON s.item_id = i.id
                LEFT JOIN categories c ON i.category_id = c.id
                WHERE rs.rack_id = ?
                ORDER BY rs.slot_number");
            $sStmt->execute([$rack['id']]);
            $rack['slots'] = $sStmt->fetchAll();
        }
        return $racks;
    }

    public static function addRack(PDO $db, int $zoneId, string $rackCode, int $rowNum, int $colNum, int $totalSlots = 8): bool {
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO racks (zone_id, rack_code, row_num, col_num, total_slots) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$zoneId, $rackCode, $rowNum, $colNum, $totalSlots]);

            $rackId = (int)$db->lastInsertId();

            $slotStmt = $db->prepare("INSERT INTO rack_slots (rack_id, slot_number, status) VALUES (?, ?, 'free')");
            for ($i = 1; $i <= $totalSlots; $i++) {
                $slotStmt->execute([$rackId, $i]);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function updateDescription(PDO $db, int $zoneId, string $description): bool {
        $stmt = $db->prepare("UPDATE zones SET description = ? WHERE id = ?");
        return $stmt->execute([$description, $zoneId]);
    }

    public static function addZone(PDO $db, string $name, string $code, string $description): bool {
        $stmt = $db->prepare("INSERT INTO zones (name, code, description, total_racks) VALUES (?, ?, ?, 0)");
        return $stmt->execute([$name, $code, $description]);
    }

    public static function deleteZone(PDO $db, int $zoneId): bool {
        $stmt = $db->prepare("SELECT COUNT(*) FROM racks WHERE zone_id = ?");
        $stmt->execute([$zoneId]);
        if ((int)$stmt->fetchColumn() > 0) {
            throw new Exception("Seksi tidak dapat dihapus karena masih terdapat rak di dalamnya.");
        }
        $stmt = $db->prepare("DELETE FROM zones WHERE id = ?");
        return $stmt->execute([$zoneId]);
    }

    public static function deleteRack(PDO $db, int $rackId): bool {
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("
                SELECT COUNT(*) FROM stock 
                WHERE rack_slot_id IN (SELECT id FROM rack_slots WHERE rack_id = ?)
                AND quantity > 0
            ");
            $stmt->execute([$rackId]);
            if ((int)$stmt->fetchColumn() > 0) {
                throw new Exception("Rak tidak dapat dihapus karena masih terdapat stok barang aktif di dalamnya. Silakan pindahkan barang-barang tersebut ke rak lain terlebih dahulu menggunakan menu Mutasi Stok.");
            }

            $stmt = $db->prepare("
                DELETE FROM stock 
                WHERE rack_slot_id IN (SELECT id FROM rack_slots WHERE rack_id = ?)
            ");
            $stmt->execute([$rackId]);

            $stmt = $db->prepare("DELETE FROM rack_slots WHERE rack_id = ?");
            $stmt->execute([$rackId]);

            $stmt = $db->prepare("DELETE FROM racks WHERE id = ?");
            $stmt->execute([$rackId]);

            $db->commit();
            return true;
		} catch (PDOException $pe) {
			$db->rollBack();
			if ($pe->getCode() === '23000' || strpos($pe->getMessage(), '1217') !== false || strpos($pe->getMessage(), '1451') !== false) {
				throw new Exception("Rak tidak dapat dihapus karena memiliki riwayat transaksi atau sesi opname aktif. Silakan kosongkan slot rak secara manual sebelum mencoba menghapus kembali.");
			}
			throw $pe;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function deleteSlot(PDO $db, int $slotId): bool {
        try {
            $db->beginTransaction();

            // Get slot details
            $stmt = $db->prepare("SELECT rack_id, status FROM rack_slots WHERE id = ?");
            $stmt->execute([$slotId]);
            $slot = $stmt->fetch();
            if (!$slot) {
                throw new Exception("Slot tidak ditemukan.");
            }

            if ($slot['status'] === 'loaded') {
                throw new Exception("Slot tidak dapat dihapus karena masih terisi barang.");
            }

            // Check if there is any stock referencing this slot (just in case)
            $stmt = $db->prepare("SELECT COUNT(*) FROM stock WHERE rack_slot_id = ? AND quantity > 0");
            $stmt->execute([$slotId]);
            if ((int)$stmt->fetchColumn() > 0) {
                throw new Exception("Slot tidak dapat dihapus karena masih terdapat stok barang aktif di dalamnya. Silakan pindahkan barang-barang tersebut ke rak/slot lain terlebih dahulu menggunakan menu Mutasi Stok.");
            }

            // Try to delete from stock table first (if quantity is 0)
            $stmt = $db->prepare("DELETE FROM stock WHERE rack_slot_id = ?");
            $stmt->execute([$slotId]);

            // Delete slot
            $stmt = $db->prepare("DELETE FROM rack_slots WHERE id = ?");
            $stmt->execute([$slotId]);

            // Decrement total_slots in racks
            $stmt = $db->prepare("UPDATE racks SET total_slots = total_slots - 1 WHERE id = ?");
            $stmt->execute([$slot['rack_id']]);

            $db->commit();
            return true;
        } catch (PDOException $pe) {
            $db->rollBack();
            if ($pe->getCode() === '23000' || strpos($pe->getMessage(), '1217') !== false || strpos($pe->getMessage(), '1451') !== false) {
                throw new Exception("Slot tidak dapat dihapus karena memiliki riwayat transaksi atau sesi opname aktif.");
            }
            throw $pe;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function adjustSlots(PDO $db, int $rackId, int $newTotalSlots): bool {
        try {
            $db->beginTransaction();

            // Get current rack details
            $stmt = $db->prepare("SELECT total_slots, rack_code FROM racks WHERE id = ?");
            $stmt->execute([$rackId]);
            $rack = $stmt->fetch();
            if (!$rack) {
                throw new Exception("Rak tidak ditemukan.");
            }

            $currentTotal = (int)$rack['total_slots'];
            if ($newTotalSlots === $currentTotal) {
                $db->commit();
                return true;
            }

            if ($newTotalSlots > $currentTotal) {
                // Add slots
                $slotStmt = $db->prepare("INSERT INTO rack_slots (rack_id, slot_number, status) VALUES (?, ?, 'free')");
                for ($i = $currentTotal + 1; $i <= $newTotalSlots; $i++) {
                    $slotStmt->execute([$rackId, $i]);
                }
            } else {
                // Delete slots (from currentTotal down to newTotalSlots + 1)
                for ($i = $currentTotal; $i > $newTotalSlots; $i--) {
                    // Get slot ID for slot_number = $i
                    $stmtId = $db->prepare("SELECT id, status FROM rack_slots WHERE rack_id = ? AND slot_number = ?");
                    $stmtId->execute([$rackId, $i]);
                    $slot = $stmtId->fetch();
                    if ($slot) {
                        if ($slot['status'] === 'loaded') {
                            throw new Exception("Gagal mengurangi slot: Slot {$i} pada rak {$rack['rack_code']} masih terisi barang. Silakan pindahkan barang di slot {$i} terlebih dahulu menggunakan menu Mutasi Stok.");
                        }
                        
                        // Check stock (just in case)
                        $checkStock = $db->prepare("SELECT COUNT(*) FROM stock WHERE rack_slot_id = ? AND quantity > 0");
                        $checkStock->execute([$slot['id']]);
                        if ((int)$checkStock->fetchColumn() > 0) {
                            throw new Exception("Gagal mengurangi slot: Slot {$i} pada rak {$rack['rack_code']} masih memiliki stok aktif. Silakan pindahkan barang di slot {$i} terlebih dahulu menggunakan menu Mutasi Stok.");
                        }

                        // Delete stock penampung (0 quantity)
                        $deleteStock = $db->prepare("DELETE FROM stock WHERE rack_slot_id = ?");
                        $deleteStock->execute([$slot['id']]);

                        // Delete slot
                        $deleteSlot = $db->prepare("DELETE FROM rack_slots WHERE id = ?");
                        $deleteSlot->execute([$slot['id']]);
                    }
                }
            }

            // Update total_slots in racks table
            $stmtUpdate = $db->prepare("UPDATE racks SET total_slots = ? WHERE id = ?");
            $stmtUpdate->execute([$newTotalSlots, $rackId]);

            $db->commit();
            return true;
        } catch (PDOException $pe) {
            $db->rollBack();
            if ($pe->getCode() === '23000' || strpos($pe->getMessage(), '1217') !== false || strpos($pe->getMessage(), '1451') !== false) {
                throw new Exception("Slot tidak dapat dikurangi karena beberapa slot terakhir memiliki riwayat transaksi atau sesi opname aktif.");
            }
            throw $pe;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}

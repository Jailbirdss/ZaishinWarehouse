<?php

class Transaction {

    public static function getReport(PDO $db, string $from, string $to, string $type = 'all') {
        $sql = "SELECT t.reference_no, t.po_number, t.type, t.condition, t.quantity, t.created_at, t.notes,
                 i.name AS item_name, i.sku, i.unit, c.name AS category_name,
                 u.name AS user_name, r.rack_code, z.name AS zone_name
                FROM transactions t
                JOIN items i ON t.item_id=i.id
                JOIN categories c ON i.category_id=c.id
                JOIN users u ON t.user_id=u.id
                LEFT JOIN rack_slots rs ON t.rack_slot_id=rs.id
                LEFT JOIN racks r ON rs.rack_id=r.id
                LEFT JOIN zones z ON r.zone_id=z.id
                WHERE DATE(t.created_at) BETWEEN ? AND ?";
        $params = [$from, $to];

        if ($type !== 'all') {
            $sql .= " AND t.type=?";
            $params[] = $type;
        }

        $sql .= " ORDER BY t.created_at DESC LIMIT 200";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function inbound(PDO $db, int $itemId, int $slotId, int $qty, string $refNo, int $userId, string $notes, string $poNumber = null, string $condition = 'baik'): bool {
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("UPDATE rack_slots SET status='loaded' WHERE id=?");
            $stmt->execute([$slotId]);

            $stmt = $db->prepare("INSERT INTO stock (item_id, rack_slot_id, quantity)
                VALUES (?,?,?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
            $stmt->execute([$itemId, $slotId, $qty]);

            $stmt = $db->prepare("INSERT INTO transactions (reference_no, po_number, item_id, type, `condition`, quantity, rack_slot_id, user_id, notes)
                VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$refNo, $poNumber, $itemId, 'inbound', $condition, $qty, $slotId, $userId, $notes]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function recordDiscrepancy(PDO $db, int $itemId, int $qty, string $refNo, int $userId, string $notes, string $poNumber = null, string $condition = 'rusak'): bool {
        $stmt = $db->prepare("INSERT INTO transactions (reference_no, po_number, item_id, type, `condition`, quantity, rack_slot_id, user_id, notes)
            VALUES (?,?,?,?,?,?,?,?,?)");
        return $stmt->execute([$refNo, $poNumber, $itemId, 'inbound', $condition, $qty, null, $userId, $notes]);
    }

    public static function outbound(PDO $db, int $itemId, int $slotId, int $qty, string $refNo, int $userId, string $notes, string $soNumber = null): bool {
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT quantity FROM stock WHERE item_id=? AND rack_slot_id=?");
            $stmt->execute([$itemId, $slotId]);
            $row = $stmt->fetch();

            if (!$row || (int)$row['quantity'] < $qty) {
                $db->rollBack();
                return false;
            }

            $newQty = (int)$row['quantity'] - $qty;
            $stmt = $db->prepare("UPDATE stock SET quantity=? WHERE item_id=? AND rack_slot_id=?");
            $stmt->execute([$newQty, $itemId, $slotId]);

            if ($newQty <= 0) {
                $stmt = $db->prepare("UPDATE rack_slots SET status='free' WHERE id=?");
                $stmt->execute([$slotId]);
            }

            $stmt = $db->prepare("INSERT INTO transactions (reference_no, po_number, item_id, type, quantity, rack_slot_id, user_id, notes)
                VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$refNo, $soNumber, $itemId, 'outbound', $qty, $slotId, $userId, $notes]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getStockQuantityBySlot(PDO $db, int $itemId, int $slotId): int {
        $stmt = $db->prepare("SELECT quantity FROM stock WHERE item_id=? AND rack_slot_id=?");
        $stmt->execute([$itemId, $slotId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['quantity'] : 0;
    }

    public static function getTotalStock(PDO $db, int $itemId): int {
        $stmt = $db->prepare("SELECT COALESCE(SUM(quantity),0) FROM stock WHERE item_id=?");
        $stmt->execute([$itemId]);
        return (int)$stmt->fetchColumn();
    }

    public static function getOutboundItems(PDO $db) {
        return $db->query("SELECT i.id, i.name, i.sku, i.unit, s.rack_slot_id,
            SUM(s.quantity) as total_stock, r.rack_code, z.name AS zone_name, rs.slot_number, rs.id AS slot_id
            FROM items i JOIN stock s ON i.id=s.item_id
            JOIN rack_slots rs ON s.rack_slot_id=rs.id
            JOIN racks r ON rs.rack_id=r.id JOIN zones z ON r.zone_id=z.id
            WHERE s.quantity > 0
            GROUP BY i.id, s.rack_slot_id
            ORDER BY i.name, r.rack_code")->fetchAll();
    }

    public static function getFreeSlots(PDO $db) {
        return $db->query("SELECT rs.id, rs.slot_number, r.rack_code, z.name AS zone_name
            FROM rack_slots rs JOIN racks r ON rs.rack_id=r.id JOIN zones z ON r.zone_id=z.id
            WHERE rs.status='free' ORDER BY z.name, r.rack_code, rs.slot_number")->fetchAll();
    }

    public static function getRecommendedSlots(PDO $db, int $categoryId) {
        $zoneMapping = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        ];
        $zoneId = isset($zoneMapping[$categoryId]) ? $zoneMapping[$categoryId] : 0;

        $sql = "SELECT rs.id, rs.slot_number, r.rack_code, z.name AS zone_name, z.id AS zone_id,
                       (CASE WHEN z.id = :zone_id THEN 1 ELSE 0 END) AS is_recommended
                FROM rack_slots rs
                JOIN racks r ON rs.rack_id = r.id
                JOIN zones z ON r.zone_id = z.id
                WHERE rs.status = 'free'
                ORDER BY is_recommended DESC, z.name, r.rack_code, rs.slot_number
                LIMIT 3";

        $stmt = $db->prepare($sql);
        $stmt->execute(['zone_id' => $zoneId]);
        return $stmt->fetchAll();
    }

    public static function getRecent(PDO $db, int $limit = 5) {
        $stmt = $db->prepare("SELECT t.reference_no, t.po_number, t.type, t.condition, t.quantity, t.created_at, t.notes,
            i.name AS item_name, i.unit, u.name AS user_name,
            r.rack_code, z.name AS zone_name
            FROM transactions t
            JOIN items i ON t.item_id = i.id
            JOIN users u ON t.user_id = u.id
            LEFT JOIN rack_slots rs ON t.rack_slot_id = rs.id
            LEFT JOIN racks r ON rs.rack_id = r.id
            LEFT JOIN zones z ON r.zone_id = z.id
            ORDER BY t.created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getFastMoving(PDO $db, int $limit = 10) {
        $stmt = $db->prepare("SELECT i.name, SUM(t.quantity) as total_out
            FROM transactions t
            JOIN items i ON t.item_id = i.id
            WHERE t.type = 'outbound'
            GROUP BY i.id
            ORDER BY total_out DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getKPI(PDO $db) {
        $totalStok = $db->query("SELECT COALESCE(SUM(quantity),0) FROM stock")->fetchColumn();
        $totalSlots  = $db->query("SELECT COUNT(*) FROM rack_slots")->fetchColumn();
        $loadedSlots = $db->query("SELECT COUNT(*) FROM rack_slots WHERE status='loaded'")->fetchColumn();
        $kapasitas = $totalSlots > 0 ? round(($loadedSlots / $totalSlots) * 100, 1) : 0;
        $txHariIni = $db->query("SELECT COUNT(*) FROM transactions WHERE DATE(created_at) = CURDATE()")->fetchColumn();
        $alertCount = $db->query("SELECT COUNT(*) FROM v_stock_summary WHERE stock_status IN ('low','empty')")->fetchColumn();

        return [
            'total_stock'  => (int)$totalStok,
            'capacity_pct' => (float)$kapasitas,
            'tx_today'     => (int)$txHariIni,
            'alerts_count' => (int)$alertCount
        ];
    }

    public static function getFlowChartData(PDO $db) {
        $sql = "SELECT
                    d.date,
                    COALESCE(SUM(CASE WHEN t.type = 'inbound' THEN t.quantity ELSE 0 END), 0) as inbound,
                    COALESCE(SUM(CASE WHEN t.type = 'outbound' THEN t.quantity ELSE 0 END), 0) as outbound
                FROM (
                    SELECT CURDATE() as date UNION ALL
                    SELECT DATE_SUB(CURDATE(), INTERVAL 1 DAY) UNION ALL
                    SELECT DATE_SUB(CURDATE(), INTERVAL 2 DAY) UNION ALL
                    SELECT DATE_SUB(CURDATE(), INTERVAL 3 DAY) UNION ALL
                    SELECT DATE_SUB(CURDATE(), INTERVAL 4 DAY) UNION ALL
                    SELECT DATE_SUB(CURDATE(), INTERVAL 5 DAY) UNION ALL
                    SELECT DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                ) d
                LEFT JOIN transactions t ON DATE(t.created_at) = d.date
                GROUP BY d.date
                ORDER BY d.date ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function getLowStockAlerts(PDO $db) {
        return $db->query("SELECT item_name, category_name, category_color, unit, min_stock, total_stock, stock_status
            FROM v_stock_summary WHERE stock_status IN ('low','empty') ORDER BY total_stock ASC")->fetchAll();
    }

    public static function getStockTrend(PDO $db, int $days = 7) {
        $currentStock = (int)$db->query("SELECT COALESCE(SUM(quantity),0) FROM stock")->fetchColumn();

        $sql = "SELECT DATE(created_at) as date,
                      SUM(CASE WHEN type='inbound' THEN quantity ELSE 0 END) as inbound,
                      SUM(CASE WHEN type='outbound' THEN quantity ELSE 0 END) as outbound
               FROM transactions
               WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
               GROUP BY DATE(created_at)
               ORDER BY DATE(created_at) DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([$days]);
        $rows = $stmt->fetchAll();

        $txByDate = [];
        foreach ($rows as $row) {
            $txByDate[$row['date']] = $row;
        }

        $trend = [];
        $runningStock = $currentStock;

        for ($i = 0; $i < $days; $i++) {
            $dateStr = date('Y-m-d', strtotime("-$i days"));
            $labelStr = date('d M', strtotime("-$i days"));

            $trend[] = [
                'label' => $labelStr,
                'stock' => $runningStock
            ];

            if (isset($txByDate[$dateStr])) {
                $in = (int)$txByDate[$dateStr]['inbound'];
                $out = (int)$txByDate[$dateStr]['outbound'];
                $runningStock = $runningStock - $in + $out;
            }
        }

        return array_reverse($trend);
    }
}

<?php

class Restock {
    public static function getRequests(PDO $db, string $status = 'all') {
        $sql = "SELECT rr.*, i.name AS item_name, i.unit, c.name AS category_name,
                    u1.name AS requester_name, u2.name AS approver_name
                FROM restock_requests rr
                JOIN items i ON rr.item_id = i.id
                JOIN categories c ON i.category_id = c.id
                JOIN users u1 ON rr.requested_by = u1.id
                LEFT JOIN users u2 ON rr.approved_by = u2.id";

        if ($status !== 'all') {
            $sql .= " WHERE rr.status = " . $db->quote($status);
        }

        $sql .= " ORDER BY rr.created_at DESC LIMIT 50";
        return $db->query($sql)->fetchAll();
    }

    public static function createRequest(PDO $db, int $itemId, int $requestedQty, int $currentStock, int $userId, string $notes): int {
        $stmt = $db->prepare("INSERT INTO restock_requests (item_id, requested_qty, current_stock, requested_by, notes)
            VALUES (?,?,?,?,?)");
        $stmt->execute([$itemId, $requestedQty, $currentStock, $userId, $notes]);
        return (int)$db->lastInsertId();
    }

    public static function updateStatus(PDO $db, int $id, string $status, int $userId): bool {
        $stmt = $db->prepare("UPDATE restock_requests SET status=?, approved_by=? WHERE id=?");
        return $stmt->execute([$status, $userId, $id]);
    }

    public static function getPendingCount(PDO $db): int {
        return (int)$db->query("SELECT COUNT(*) FROM restock_requests WHERE status='pending'")->fetchColumn();
    }
}

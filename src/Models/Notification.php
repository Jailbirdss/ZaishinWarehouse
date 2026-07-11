<?php

class Notification {

    public static function send(PDO $db, ?int $userId, ?string $role, string $type, string $title, string $message, ?int $relatedId = null): bool {
        $stmt = $db->prepare("INSERT INTO notifications (user_id, role, type, title, message, related_id) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $role, $type, $title, $message, $relatedId]);
    }

    public static function getByUser(PDO $db, int $userId, string $role, int $limit = 50) {
        $stmt = $db->prepare("SELECT * FROM notifications
                              WHERE user_id = ? OR role = ? OR (user_id IS NULL AND role IS NULL)
                              ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $role, PDO::PARAM_STR);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getUnreadCount(PDO $db, int $userId, string $role): int {
        $stmt = $db->prepare("SELECT COUNT(*) FROM notifications
                              WHERE (user_id = ? OR role = ? OR (user_id IS NULL AND role IS NULL))
                              AND is_read = 0");
        $stmt->execute([$userId, $role]);
        return (int)$stmt->fetchColumn();
    }

    public static function markAsRead(PDO $db, int $id): bool {
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function markAllAsRead(PDO $db, int $userId, string $role): bool {
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1
                              WHERE (user_id = ? OR role = ? OR (user_id IS NULL AND role IS NULL))
                              AND is_read = 0");
        return $stmt->execute([$userId, $role]);
    }

    public static function checkAndNotifyLowStock(PDO $db, int $itemId): void {

        $stmt = $db->prepare("
            SELECT i.id, i.name, i.sku, i.unit, i.min_stock,
                   COALESCE((SELECT SUM(quantity) FROM stock WHERE item_id = i.id), 0) AS total_stock
            FROM items i WHERE i.id = ?
        ");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch();

        if (!$item) return;

        $totalStock = (int)$item['total_stock'];
        $minStock   = (int)$item['min_stock'];

        if ($totalStock > $minStock) return;

        $stmtCheck = $db->prepare("SELECT COUNT(*) FROM notifications WHERE type = 'low_stock' AND related_id = ? AND is_read = 0");
        $stmtCheck->execute([$itemId]);
        if ((int)$stmtCheck->fetchColumn() > 0) return;

        $notifTitle = "Alarm Stok Rendah: " . $item['name'];
        $notifMsg   = "Stok barang " . $item['name'] . " (" . $item['sku'] . ") saat ini menipis menjadi "
                    . $totalStock . " " . $item['unit']
                    . " (Batas minimum: " . $minStock . " " . $item['unit'] . "). Segera ajukan restock.";

        self::send($db, null, 'admin_gudang',    'low_stock', $notifTitle, $notifMsg, $itemId);
        self::send($db, null, 'divisi_pembelian', 'low_stock', $notifTitle, $notifMsg, $itemId);
    }
}


<?php

class Item {

    public static function search(PDO $db, string $search = '', int $catFilter = 0) {
        $sql = "SELECT i.*, c.name AS cat_name, c.color AS cat_color, COALESCE(SUM(s.quantity),0) AS total_stock
                FROM items i JOIN categories c ON i.category_id=c.id
                LEFT JOIN stock s ON i.id=s.item_id";
        $where = []; $params = [];

        if ($search) {
            $where[] = "(i.name LIKE ? OR i.sku LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($catFilter) {
            $where[] = "i.category_id=?";
            $params[] = $catFilter;
        }

        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " GROUP BY i.id ORDER BY i.name";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getById(PDO $db, int $id) {
        $stmt = $db->prepare("SELECT * FROM items WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getByQrCode(PDO $db, string $code) {
        $stmt = $db->prepare("SELECT i.*, c.name AS category_name, COALESCE(SUM(s.quantity),0) AS total_stock
            FROM items i JOIN categories c ON i.category_id=c.id
            LEFT JOIN stock s ON i.id=s.item_id
            WHERE i.qr_code=? GROUP BY i.id");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }

    public static function getAll(PDO $db) {
        return $db->query("SELECT i.id, i.name, i.sku, i.unit, i.qr_code, c.name AS cat_name
            FROM items i JOIN categories c ON i.category_id=c.id ORDER BY i.name")->fetchAll();
    }

    public static function getSimpleList(PDO $db) {
        return $db->query("SELECT id, name, sku, unit FROM items ORDER BY name")->fetchAll();
    }

    public static function getCategories(PDO $db) {
        return $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
    }

    public static function save(PDO $db, string $sku, string $name, int $categoryId, string $unit, int $minStock, string $description, int $id = 0): bool {
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE items SET sku=?, name=?, category_id=?, unit=?, min_stock=?, description=? WHERE id=?");
            return $stmt->execute([$sku, $name, $categoryId, $unit, $minStock, $description, $id]);
        } else {
            $qr = 'QR-' . strtoupper(uniqid());
            $stmt = $db->prepare("INSERT INTO items (sku, name, category_id, unit, min_stock, description, qr_code) VALUES (?,?,?,?,?,?,?)");
            return $stmt->execute([$sku, $name, $categoryId, $unit, $minStock, $description, $qr]);
        }
    }

    public static function delete(PDO $db, int $id): bool {
        $stmt = $db->prepare("DELETE FROM items WHERE id=?");
        return $stmt->execute([$id]);
    }

    public static function getStorageSlots(PDO $db, int $itemId) {
        $stmt = $db->prepare("SELECT r.rack_code, rs.slot_number, s.quantity, z.name AS zone_name
                              FROM stock s
                              JOIN rack_slots rs ON s.rack_slot_id = rs.id
                              JOIN racks r ON rs.rack_id = r.id
                              JOIN zones z ON r.zone_id = z.id
                              WHERE s.item_id = ? AND s.quantity > 0
                              ORDER BY z.name, r.rack_code, rs.slot_number");
        $stmt->execute([$itemId]);
        return $stmt->fetchAll();
    }
}

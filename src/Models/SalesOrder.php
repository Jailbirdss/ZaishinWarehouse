<?php

class SalesOrder {

    public static function create(PDO $db, string $customer, int $userId, array $items): string {
        $db->beginTransaction();
        try {

            $soNumber = 'SO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $stmt = $db->prepare("INSERT INTO sales_orders (so_number, customer, status, created_by) VALUES (?, ?, 'pending', ?)");
            $stmt->execute([$soNumber, $customer, $userId]);
            $soId = (int)$db->lastInsertId();

            $stmtItem = $db->prepare("INSERT INTO sales_order_items (sales_order_id, item_id, quantity) VALUES (?, ?, ?)");
            foreach ($items as $item) {
                $itemId = (int)$item['item_id'];
                $qty = (int)$item['quantity'];
                if ($itemId > 0 && $qty > 0) {
                    $stmtItem->execute([$soId, $itemId, $qty]);
                }
            }

            $db->commit();
            return $soNumber;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getPending(PDO $db): array {
        $stmt = $db->query("SELECT so.*, u.name AS creator_name FROM sales_orders so JOIN users u ON so.created_by = u.id WHERE so.status = 'pending' ORDER BY so.created_at DESC");
        $sos = $stmt->fetchAll();

        $result = [];
        foreach ($sos as $so) {
            $itemStmt = $db->prepare("SELECT i.sku, soi.quantity AS qty FROM sales_order_items soi JOIN items i ON soi.item_id = i.id WHERE soi.sales_order_id = ?");
            $itemStmt->execute([$so['id']]);
            $items = [];
            foreach ($itemStmt->fetchAll() as $row) {
                $items[] = [
                    'sku' => $row['sku'],
                    'qty' => (int)$row['qty']
                ];
            }

            $result[] = [
                'id' => (int)$so['id'],
                'so_number' => $so['so_number'],
                'customer' => $so['customer'],
                'date' => date('Y-m-d', strtotime($so['created_at'])),
                'items' => $items
            ];
        }
        return $result;
    }

    public static function getAll(PDO $db): array {
        $stmt = $db->query("SELECT so.*, u.name AS creator_name FROM sales_orders so JOIN users u ON so.created_by = u.id ORDER BY so.created_at DESC");
        $sos = $stmt->fetchAll();

        $result = [];
        foreach ($sos as $so) {
            $itemStmt = $db->prepare("SELECT i.sku, i.name, soi.quantity, i.unit,
                                      (SELECT COALESCE(SUM(quantity),0) FROM stock WHERE item_id = i.id) AS total_stock
                                      FROM sales_order_items soi
                                      JOIN items i ON soi.item_id = i.id
                                      WHERE soi.sales_order_id = ?");
            $itemStmt->execute([$so['id']]);
            $items = $itemStmt->fetchAll();

            $isStockSufficient = true;
            foreach ($items as $item) {
                if ((int)$item['total_stock'] < (int)$item['quantity']) {
                    $isStockSufficient = false;
                }
            }

            $so['items'] = $items;
            $so['is_stock_sufficient'] = $isStockSufficient;

            $completionInfo = null;
            if ($so['status'] === 'completed') {
                $compStmt = $db->prepare("SELECT t.created_at AS completed_at, u.name AS picker_name, t.reference_no
                                          FROM transactions t
                                          JOIN users u ON t.user_id = u.id
                                          WHERE t.po_number = ? AND t.type = 'outbound'
                                          ORDER BY t.created_at DESC LIMIT 1");
                $compStmt->execute([$so['so_number']]);
                $completionInfo = $compStmt->fetch();
            }
            $so['completion_info'] = $completionInfo;

            $result[] = $so;
        }
        return $result;
    }

    public static function complete(PDO $db, string $soNumber): bool {
        $stmt = $db->prepare("UPDATE sales_orders SET status = 'completed' WHERE so_number = ?");
        return $stmt->execute([$soNumber]);
    }
}

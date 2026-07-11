<?php
requirePermission('stock.view');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Item.php';

$db = getDB();

$stmt = $db->query("
    SELECT i.id, i.name, i.sku, i.unit, i.min_stock,
           c.name AS cat_name, c.color AS cat_color,
           COALESCE(SUM(s.quantity), 0) AS total_stock
    FROM items i
    JOIN categories c ON i.category_id = c.id
    LEFT JOIN stock s ON i.id = s.item_id
    GROUP BY i.id
    ORDER BY i.name
");
$stockItems = $stmt->fetchAll();

$pageTitle    = 'Ketersediaan Stok';
$pageSubtitle = 'Informasi stok terkini bahan cetak secara real-time.';
ob_start();
require __DIR__ . '/../Views/stock/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

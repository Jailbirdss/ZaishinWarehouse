<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Zone.php';
require_once __DIR__ . '/../src/Models/Transaction.php';
require_once __DIR__ . '/../src/Models/Restock.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db   = getDB();
$type = $_GET['type'] ?? 'kpi';

switch ($type) {

    case 'kpi':
        $kpi = Transaction::getKPI($db);
        echo json_encode([
            'total_stok'    => $kpi['total_stock'],
            'kapasitas_pct' => $kpi['capacity_pct'],
            'tx_hari_ini'   => $kpi['tx_today'],
            'alert_count'   => $kpi['alerts_count'],
        ]);
        break;

    case 'chart_flow':
        $rows = Transaction::getFlowChartData($db);
        $labels   = [];
        $inbound  = [];
        $outbound = [];

        foreach ($rows as $row) {
            $labels[]   = date('d M', strtotime($row['date']));
            $inbound[]  = (int)$row['inbound'];
            $outbound[] = (int)$row['outbound'];
        }

        echo json_encode([
            'labels'   => $labels,
            'inbound'  => $inbound,
            'outbound' => $outbound,
        ]);
        break;

    case 'chart_zone':
        $rows = $db->query("SELECT z.name AS zone_name, COALESCE(SUM(s.quantity),0) AS total_qty,
                                   COUNT(rs.id) AS total_slots,
                                   SUM(CASE WHEN rs.status='loaded' THEN 1 ELSE 0 END) AS loaded_slots,
                                   SUM(CASE WHEN rs.status='free' THEN 1 ELSE 0 END) AS free_slots
                            FROM zones z
                            JOIN racks r ON r.zone_id = z.id
                            JOIN rack_slots rs ON rs.rack_id = r.id
                            LEFT JOIN stock s ON s.rack_slot_id = rs.id
                            GROUP BY z.id, z.name ORDER BY z.id")->fetchAll();

        $totalQtyAll = array_sum(array_column($rows, 'total_qty'));
        $totalSlotsAll = array_sum(array_column($rows, 'total_slots'));

        $categoriesInfo = $db->query("
            SELECT c.name, COALESCE(SUM(s.quantity), 0) AS total_qty
            FROM categories c
            LEFT JOIN items i ON i.category_id = c.id
            LEFT JOIN stock s ON s.item_id = i.id
            GROUP BY c.id, c.name
            ORDER BY total_qty DESC
        ")->fetchAll();

        $topCategoryName = 'Tidak Ada';
        $topCategoryQty = 0;
        $bottomCategoryName = 'Tidak Ada';
        $bottomCategoryQty = 0;

        if (!empty($categoriesInfo)) {
            $topCategoryName = $categoriesInfo[0]['name'];
            $topCategoryQty = (int)$categoriesInfo[0]['total_qty'];

            $bottomCategory = end($categoriesInfo);
            $bottomCategoryName = $bottomCategory['name'];
            $bottomCategoryQty = (int)$bottomCategory['total_qty'];
        }

        echo json_encode([
            'labels'  => array_column($rows, 'zone_name'),
            'loaded'  => array_map(fn($r) => (int)$r['loaded_slots'], $rows),
            'free'    => array_map(fn($r) => (int)$r['free_slots'], $rows),
            'pct'     => array_map(fn($r) => $r['total_slots'] > 0 ? round(($r['loaded_slots'] / $r['total_slots']) * 100, 1) : 0, $rows),
            'qtys'    => array_map(fn($r) => (int)$r['total_qty'], $rows),
            'qty_pct' => array_map(fn($r) => $totalQtyAll > 0 ? round(($r['total_qty'] / $totalQtyAll) * 100, 1) : 0, $rows),
            'top_category_name'     => $topCategoryName,
            'top_category_qty'      => $topCategoryQty,
            'bottom_category_name'  => $bottomCategoryName,
            'bottom_category_qty'   => $bottomCategoryQty
        ]);
        break;

    case 'chart_fast':
        $rows = Transaction::getFastMoving($db, 8);
        echo json_encode([
            'labels' => array_column($rows, 'name'),
            'values' => array_map(fn($r) => (int)$r['total_out'], $rows),
        ]);
        break;

    case 'recent':
        $rows = Transaction::getRecent($db, 8);
        echo json_encode($rows);
        break;

    case 'low_stock':
        $rows = Transaction::getLowStockAlerts($db);
        echo json_encode($rows);
        break;

    case 'restock_pending':
        $count = Restock::getPendingCount($db);
        echo json_encode(['count' => $count]);
        break;

    case 'chart_trend':
        $trend = Transaction::getStockTrend($db, 7);
        echo json_encode([
            'labels' => array_column($trend, 'label'),
            'values' => array_column($trend, 'stock')
        ]);
        break;

    default:
        echo json_encode(['error' => 'Unknown type']);
}

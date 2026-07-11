<?php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

session_write_close();

if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY) || GEMINI_API_KEY === 'YOUR_API_KEY_HERE') {
    echo json_encode(['error' => 'API Key not configured', 'fallback' => true]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';
$history = $input['history'] ?? [];
$stats = $input['stats'] ?? null;

if (empty($message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Message is required']);
    exit;
}

$statsContext = "";
if ($stats) {
    $statsContext = "Berikut adalah data statistik gudang saat ini (real-time):\n" .
                    "- Total stok aktif: " . ($stats['totalStok'] ?? '---') . " unit.\n" .
                    "- Kapasitas gudang terpakai: " . ($stats['capacity'] ?? '---') . ".\n" .
                    "- Jumlah transaksi masuk & keluar hari ini: " . ($stats['txCount'] ?? '---') . " transaksi.\n" .
                    "- Zona terpadat: " . ($stats['densest'] ?? '---') . ".\n" .
                    "- Zona terlonggar: " . ($stats['emptiest'] ?? '---') . ".\n" .
                    "- Jumlah barang dengan stok kritis: " . ($stats['alertCount'] ?? 0) . " barang.\n\n";
}

$db = getDB();
$itemsListContext = "";
try {
    $stmt = $db->query("
        SELECT i.name, i.sku, c.name AS cat_name, i.unit, COALESCE(SUM(s.quantity), 0) AS total_stock
        FROM items i
        JOIN categories c ON i.category_id = c.id
        LEFT JOIN stock s ON i.id = s.item_id
        GROUP BY i.id
        ORDER BY i.name
    ");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($items)) {
        $itemsListContext = "Daftar inventaris barang lengkap beserta jumlah stok real-time saat ini:\n";
        foreach ($items as $item) {
            $itemsListContext .= "- " . $item['name'] . " (SKU: " . $item['sku'] . ") | Kategori: " . $item['cat_name'] . " | Stok: " . (int)$item['total_stock'] . " " . $item['unit'] . "\n";
        }
        $itemsListContext .= "\n";
    }
} catch (Exception $e) {

}

$operationalContext = "";
try {
    $notifStmt = $db->query("SELECT title, message, created_at FROM notifications ORDER BY created_at DESC LIMIT 5");
    $notifs = $notifStmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($notifs)) {
        $operationalContext .= "Daftar Notifikasi Operasional Terbaru:\n";
        foreach ($notifs as $n) {
            $operationalContext .= "- [" . date('d-M-Y H:i', strtotime($n['created_at'])) . "] " . $n['title'] . ": " . $n['message'] . "\n";
        }
        $operationalContext .= "\n";
    }

    $soStats = $db->query("SELECT status, COUNT(*) as count FROM sales_orders GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
    $soList = $db->query("SELECT so_number, customer, status, created_at FROM sales_orders ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    $operationalContext .= "Statistik Sales Order (SO):\n";
    $statusCounts = [];
    foreach ($soStats as $ss) {
        $statusCounts[] = ucfirst($ss['status']) . ": " . $ss['count'];
    }
    $operationalContext .= "- Jumlah: " . (empty($statusCounts) ? "0 SO" : implode(', ', $statusCounts)) . "\n";
    if (!empty($soList)) {
        $operationalContext .= "- 5 Sales Order Terakhir:\n";
        foreach ($soList as $so) {
            $operationalContext .= "  * " . $so['so_number'] . " | Pelanggan: " . $so['customer'] . " | Status: " . $so['status'] . " | Tanggal: " . date('d-M-Y', strtotime($so['created_at'])) . "\n";
        }
    }
    $operationalContext .= "\n";

    $txStmt = $db->query("
        SELECT t.reference_no, t.type, t.quantity, t.created_at, t.notes, i.name AS item_name, i.unit, u.name AS user_name
        FROM transactions t
        JOIN items i ON t.item_id = i.id
        JOIN users u ON t.user_id = u.id
        ORDER BY t.created_at DESC LIMIT 5
    ");
    $recentTxs = $txStmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($recentTxs)) {
        $operationalContext .= "Log Transaksi Masuk/Keluar Terbaru:\n";
        foreach ($recentTxs as $t) {
            $typeLabel = $t['type'] === 'inbound' ? 'Barang Masuk (IN)' : 'Barang Keluar (OUT)';
            $operationalContext .= "- [" . date('d-M-Y H:i', strtotime($t['created_at'])) . "] " . $typeLabel . " | " . $t['item_name'] . " sebanyak " . (int)$t['quantity'] . " " . $t['unit'] . " | Ref: " . $t['reference_no'] . " oleh " . $t['user_name'] . " (" . $t['notes'] . ")\n";
        }
        $operationalContext .= "\n";
    }

    $activeOp = $db->query("SELECT opname_no, created_at FROM stock_opnames WHERE status = 'initiated' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $histOp = $db->query("
        SELECT o.opname_no, o.completed_at,
               (SELECT COUNT(*) FROM stock_opname_details WHERE stock_opname_id=o.id) as total_items,
               (SELECT COUNT(*) FROM stock_opname_details WHERE stock_opname_id=o.id AND discrepancy != 0) as discrepancy_items
        FROM stock_opnames o
        WHERE o.status = 'completed'
        ORDER BY o.completed_at DESC LIMIT 3
    ")->fetchAll(PDO::FETCH_ASSOC);

    $operationalContext .= "Data Audit Stock Opname Gudang:\n";
    if ($activeOp) {
        $operationalContext .= "- Sesi Berjalan: " . $activeOp['opname_no'] . " (diinisiasi " . date('d-M-Y H:i', strtotime($activeOp['created_at'])) . ")\n";
    } else {
        $operationalContext .= "- Sesi Berjalan: Tidak ada sesi opname yang aktif.\n";
    }
    if (!empty($histOp)) {
        $operationalContext .= "- 3 Sesi Opname Terakhir (Selesai):\n";
        foreach ($histOp as $ho) {
            $operationalContext .= "  * " . $ho['opname_no'] . " | Selesai: " . date('d-M-Y H:i', strtotime($ho['completed_at'])) . " | Total Item: " . $ho['total_items'] . " | Item Selisih: " . $ho['discrepancy_items'] . "\n";
        }
    }
    $operationalContext .= "\n";

    $monthlyIn = $db->query("SELECT COALESCE(SUM(quantity), 0) FROM transactions WHERE type='inbound' AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')")->fetchColumn();
    $monthlyOut = $db->query("SELECT COALESCE(SUM(quantity), 0) FROM transactions WHERE type='outbound' AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')")->fetchColumn();
    $monthlyTxCount = $db->query("SELECT COUNT(*) FROM transactions WHERE created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')")->fetchColumn();

    $operationalContext .= "Akumulasi Statistik Transaksi Bulan Ini (" . date('01-M-Y') . " s/d " . date('d-M-Y') . "):\n" .
                           "- Total aktivitas transaksi bulan ini: " . (int)$monthlyTxCount . " transaksi.\n" .
                           "- Total kuantitas barang masuk (inbound) bulan ini: " . (int)$monthlyIn . " unit.\n" .
                           "- Total kuantitas barang keluar (outbound) bulan ini: " . (int)$monthlyOut . " unit.\n\n";

} catch (Exception $e) {

}

$appFeaturesContext = "Berikut adalah daftar menu/fitur lengkap yang terintegrasi di aplikasi Zaishin WMS ini:\n" .
                      "- **Dashboard**: Menampilkan ringkasan KPI (Total Stok, Kapasitas Gudang, Alert Kritis, Transaksi Hari Ini) dan grafik utilisasi zona.\n" .
                      "- **Peta Gudang**: Visualisasi denah/tata letak gudang interaktif per zona (Zona A s/d E), isi detail slot rak, dan penambahan rak baru.\n" .
                      "- **Ketersediaan Stok**: Tabel daftar stok seluruh barang, detail kuantitas, status, dan lokasi rak penyimpanannya.\n" .
                      "- **Barang Masuk (Inbound)**: Penerimaan barang dari Purchase Order (PO), scanning QR code barang, dan saran rekomendasi slot rak.\n" .
                      "- **Barang Keluar (Outbound)**: Pengeluaran barang untuk memenuhi Sales Order (SO) menggunakan metode pengambilan FIFO (First-In, First-Out).\n" .
                      "- **Mutasi Stok (Relocation)**: Pemindahan barang internal antar-slot rak secara dinamis.\n" .
                      "- **Permintaan Restock**: Pengajuan & persetujuan pengadaan stok barang dari supplier (terutama untuk item dengan stok kritis).\n" .
                      "- **Stock Opname**: Audit perhitungan fisik barang di rak menggunakan metode Blind Count (petugas tidak diperlihatkan kuantitas sistem untuk keakuratan audit).\n" .
                      "- **Sales Order (SO)**: Pengelolaan pesanan penjualan barang.\n" .
                      "- **Master Barang**: Katalog barang (tambah/edit/hapus barang, detail SKU, kategori, minimal stok, stok awal, slot rak, dan deskripsi).\n" .
                      "- **Laporan**: Laporan penerimaan dan ringkasan arus barang masuk/keluar.\n" .
                      "- **Manajemen User**: Pengelolaan akun staf (Admin, Petugas, Divisi Penjualan, Divisi Pembelian, Manajemen).\n\n";

$systemInstruction = "Anda adalah Zaishin AI, asisten pintar manajemen pergudangan untuk Zaishin WMS.\n" .
                     "Tugas Anda adalah membantu pengguna (seperti admin gudang, divisi penjualan/pembelian, manajemen) untuk memantau, menganalisis, dan memberikan rekomendasi logistik pergudangan berdasarkan data pergudangan.\n" .
                     $statsContext .
                     $itemsListContext .
                     $operationalContext .
                     $appFeaturesContext .
                     "ATURAN RUANG LINGKUP (GUARDRAILS):\n" .
                     "1. Anda hanya diperbolehkan menjawab pertanyaan dan memberikan bantuan yang berhubungan dengan manajemen pergudangan, ketersediaan stok, transaksi logistik gudang, inventaris, audit stock opname, mutasi barang, analisis kapasitas zona, dan menu-menu fungsional yang ada pada aplikasi Zaishin WMS.\n" .
                     "2. Jika pengguna menanyakan hal-hal di luar topik di atas (seperti meminta contoh kode pemrograman Node.js/PHP/Python, resep masakan, tips kehidupan umum, matematika murni non-gudang, atau hal di luar konteks lainnya), Anda WAJIB MENOLAK secara halus, sopan, dan profesional.\n" .
                     "3. Sampaikan secara ramah bahwa tugas Anda dibatasi khusus untuk membantu pengelolaan pergudangan di Zaishin WMS.\n\n" .
                     "Jawablah dengan sopan, ramah, profesional, ringkas, dan jelas dalam Bahasa Indonesia. Gunakan formatting Markdown tebal (**teks**) jika diperlukan agar mudah dibaca.";

$contents = [];
foreach ($history as $msg) {

    if (empty($msg['text'])) continue;
    $role = ($msg['sender'] === 'user') ? 'user' : 'model';
    $contents[] = [
        'role' => $role,
        'parts' => [
            ['text' => $msg['text']]
        ]
    ];
}

if (empty($contents)) {
    $contents[] = [
        'role' => 'user',
        'parts' => [
            ['text' => $message]
        ]
    ];
}

$tools = [
    [
        'functionDeclarations' => [
            [
                'name' => 'execute_read_only_query',
                'description' => 'Menjalankan kueri SQL SELECT di database Zaishin WMS untuk mengambil data dinamis apa pun (stok, transaksi, users, opname, logs, dll.). Gunakan kueri SQL MySQL yang valid.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'sql_query' => [
                            'type' => 'STRING',
                            'description' => 'Query SQL SELECT MySQL yang lengkap, aman, dan valid. Hanya perintah SELECT yang diperbolehkan.'
                        ]
                    ],
                    'required' => ['sql_query']
                ]
            ],
            [
                'name' => 'get_item_stock',
                'description' => 'Mencari detail ketersediaan stok barang di gudang berdasarkan nama barang atau SKU secara spesifik.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'search_query' => [
                            'type' => 'STRING',
                            'description' => 'Kata kunci nama barang atau SKU yang ingin dicari.'
                        ]
                    ],
                    'required' => ['search_query']
                ]
            ],
            [
                'name' => 'get_recent_transactions',
                'description' => 'Mengambil log mutasi/transaksi barang masuk (inbound) atau keluar (outbound) terbaru di gudang.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'type' => [
                            'type' => 'STRING',
                            'description' => 'Filter tipe transaksi: "inbound", "outbound", atau "all".'
                        ],
                        'limit' => [
                            'type' => 'INTEGER',
                            'description' => 'Jumlah baris data transaksi yang ingin diambil (default 5, maksimal 20).'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'get_sales_orders',
                'description' => 'Mendapatkan daftar dokumen Sales Order (SO) di sistem berdasarkan status.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'status' => [
                            'type' => 'STRING',
                            'description' => 'Filter status SO: "pending", "completed", atau "all".'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'get_stock_opnames',
                'description' => 'Mendapatkan daftar riwayat sesi audit stock opname yang pernah dilakukan di gudang.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'status' => [
                            'type' => 'STRING',
                            'description' => 'Filter status opname: "completed", "initiated", atau "all".'
                        ]
                    ]
                ]
            ]
        ]
    ]
];

function executeLocalTool(PDO $db, string $name, array $args): array {
    switch ($name) {
        case 'execute_read_only_query':
            $sql = trim($args['sql_query'] ?? '');

            $lowerSql = strtolower($sql);

            if (strpos($lowerSql, 'select') !== 0) {
                return ['error' => 'Hanya kueri SELECT yang diizinkan untuk alasan keamanan database.'];
            }

            $forbiddenKeywords = ['insert', 'update', 'delete', 'drop', 'alter', 'truncate', 'replace', 'create', 'rename'];
            foreach ($forbiddenKeywords as $word) {
                if (preg_match('/\b' . $word . '\b/i', $sql)) {
                    return ['error' => 'Kueri mengandung kata kunci terlarang "' . $word . '" untuk keamanan database.'];
                }
            }

            try {
                $stmt = $db->query($sql);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                return ['error' => 'Gagal menjalankan query: ' . $e->getMessage()];
            }

        case 'get_item_stock':
            $q = '%' . ($args['search_query'] ?? '') . '%';
            $stmt = $db->prepare("
                SELECT i.name, i.sku, c.name AS category, i.unit, COALESCE(SUM(s.quantity), 0) AS total_stock
                FROM items i
                JOIN categories c ON i.category_id = c.id
                LEFT JOIN stock s ON i.id = s.item_id
                WHERE i.name LIKE ? OR i.sku LIKE ?
                GROUP BY i.id
                ORDER BY i.name
            ");
            $stmt->execute([$q, $q]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        case 'get_recent_transactions':
            $limit = isset($args['limit']) ? (int)$args['limit'] : 5;
            $type = $args['type'] ?? 'all';

            $sql = "
                SELECT t.reference_no, t.type, t.quantity, t.created_at, t.notes, i.name AS item_name, i.unit, u.name AS user_name
                FROM transactions t
                JOIN items i ON t.item_id = i.id
                JOIN users u ON t.user_id = u.id
            ";
            $params = [];
            if ($type !== 'all' && ($type === 'inbound' || $type === 'outbound')) {
                $sql .= " WHERE t.type = ?";
                $params[] = $type;
            }
            $sql .= " ORDER BY t.created_at DESC LIMIT " . $limit;
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        case 'get_sales_orders':
            $status = $args['status'] ?? 'all';
            $sql = "SELECT so.so_number, so.customer, so.status, so.created_at, u.name AS creator_name
                    FROM sales_orders so
                    JOIN users u ON so.created_by = u.id";
            $params = [];
            if ($status !== 'all' && ($status === 'pending' || $status === 'completed')) {
                $sql .= " WHERE so.status = ?";
                $params[] = $status;
            }
            $sql .= " ORDER BY so.created_at DESC LIMIT 15";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        case 'get_stock_opnames':
            $status = $args['status'] ?? 'all';
            $sql = "SELECT o.opname_no, o.status, o.created_at, o.completed_at, u.name AS creator_name
                    FROM stock_opnames o
                    JOIN users u ON o.created_by = u.id";
            $params = [];
            if ($status !== 'all' && ($status === 'completed' || $status === 'initiated')) {
                $sql .= " WHERE o.status = ?";
                $params[] = $status;
            }
            $sql .= " ORDER BY o.created_at DESC LIMIT 15";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        default:
            return ['error' => 'Fungsi tidak ditemukan'];
    }
}

function callGeminiAPI(string $url, array $payload): string {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . GEMINI_API_KEY;

$payload = [
    'contents' => $contents,
    'tools' => $tools,
    'systemInstruction' => [
        'parts' => [
            ['text' => $systemInstruction]
        ]
    ]
];

$response = callGeminiAPI($url, $payload);
$resData = json_decode($response, true);

$functionCall = null;
if (isset($resData['candidates'][0]['content']['parts'])) {
    foreach ($resData['candidates'][0]['content']['parts'] as $part) {
        if (isset($part['functionCall'])) {
            $functionCall = $part['functionCall'];
            break;
        }
    }
}

if ($functionCall) {
    $functionName = $functionCall['name'];
    $functionArgs = $functionCall['args'] ?? [];

    $toolResult = executeLocalTool($db, $functionName, $functionArgs);

    $contents[] = $resData['candidates'][0]['content'];

    $contents[] = [
        'role' => 'user',
        'parts' => [
            [
                'functionResponse' => [
                    'name' => $functionName,
                    'response' => [
                        'output' => $toolResult
                    ]
                ]
            ]
        ]
    ];

    $finalPayload = [
        'contents' => $contents,
        'tools' => $tools,
        'systemInstruction' => [
            'parts' => [
                ['text' => $systemInstruction]
            ]
        ]
    ];

    $finalResponse = callGeminiAPI($url, $finalPayload);
    $finalResData = json_decode($finalResponse, true);

    if (isset($finalResData['candidates'][0]['content']['parts'][0]['text'])) {
        $reply = $finalResData['candidates'][0]['content']['parts'][0]['text'];
        echo json_encode(['reply' => $reply]);
    } else {
        echo json_encode([
            'error' => 'Gemini API Final Error',
            'response' => $finalResData ?: $finalResponse,
            'fallback' => true
        ]);
    }
} else {
    if (isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
        $reply = $resData['candidates'][0]['content']['parts'][0]['text'];
        echo json_encode(['reply' => $reply]);
    } else {
        echo json_encode([
            'error' => 'Gemini API Error',
            'response' => $resData ?: $response,
            'fallback' => true
        ]);
    }
}

<?php
requirePermission('zones.view');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Zone.php';
$db    = getDB();
$error = null;

$action = $_GET['action'] ?? '';

if ($action === 'delete_zone' && isset($_GET['id'])) {
    requirePermission('zones.delete');
    $id = (int)$_GET['id'];
    try {
        Zone::deleteZone($db, $id);
        header('Location: index.php?page=zones&deleted_zone=1');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
} elseif ($action === 'delete_rack' && isset($_GET['id'])) {
    requirePermission('racks.delete');
    $id = (int)$_GET['id'];
    $zoneId = (int)($_GET['zone_id'] ?? 0);
    try {
        Zone::deleteRack($db, $id);
        header('Location: index.php?page=zones&deleted_rack=1&zone_id=' . $zoneId);
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
} elseif ($action === 'delete_slot' && isset($_GET['id'])) {
    requirePermission('racks.delete');
    $id = (int)$_GET['id'];
    $zoneId = (int)($_GET['zone_id'] ?? 0);
    try {
        Zone::deleteSlot($db, $id);
        header('Location: index.php?page=zones&deleted_slot=1&zone_id=' . $zoneId);
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    if ($postAction === 'add_rack') {
        requirePermission('racks.create');
        
        $zoneId     = (int)($_POST['zone_id'] ?? 0);
        $rackCode   = trim($_POST['rack_code'] ?? '');
        $rowNum     = (int)($_POST['row_num'] ?? 1);
        $colNum     = (int)($_POST['col_num'] ?? 1);
        $totalSlots = (int)($_POST['total_slots'] ?? 8);
        
        if ($zoneId > 0 && !empty($rackCode)) {
            try {
                $check = $db->prepare("SELECT COUNT(*) FROM racks WHERE rack_code = ?");
                $check->execute([$rackCode]);
                if ((int)$check->fetchColumn() > 0) {
                    $error = "Kode rak '$rackCode' sudah digunakan.";
                } else {
                    Zone::addRack($db, $zoneId, $rackCode, $rowNum, $colNum, $totalSlots);
                    header('Location: index.php?page=zones&saved=1&zone_id=' . $zoneId);
                    exit;
                }
            } catch (Exception $e) {
                $error = "Gagal menambahkan rak: " . $e->getMessage();
            }
        } else {
            $error = "Data rak tidak lengkap atau tidak valid.";
        }
    } elseif ($postAction === 'update_zone_description') {
        if (!hasPermission('zones.create')) {
            header('HTTP/1.1 403 Forbidden');
            exit('Akses ditolak.');
        }
        $zoneId      = (int)($_POST['zone_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        if ($zoneId > 0) {
            try {
                Zone::updateDescription($db, $zoneId, $description);
                header('Location: index.php?page=zones&saved_desc=1&zone_id=' . $zoneId);
                exit;
            } catch (Exception $e) {
                $error = "Gagal mengubah deskripsi: " . $e->getMessage();
            }
        } else {
            $error = "ID Seksi tidak valid.";
        }
    } elseif ($postAction === 'add_zone') {
        requirePermission('zones.create');
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if (!empty($name) && !empty($code)) {
            try {
                // Check if zone code or name already exists
                $check = $db->prepare("SELECT COUNT(*) FROM zones WHERE name = ? OR code = ?");
                $check->execute([$name, $code]);
                if ((int)$check->fetchColumn() > 0) {
                    $error = "Nama atau Kode Seksi sudah digunakan.";
                } else {
                    Zone::addZone($db, $name, $code, $description);
                    header('Location: index.php?page=zones&saved_zone=1');
                    exit;
                }
            } catch (Exception $e) {
                $error = "Gagal menambahkan seksi: " . $e->getMessage();
            }
        } else {
            $error = "Nama dan Kode Seksi wajib diisi.";
        }
    } elseif ($postAction === 'adjust_slots') {
        requirePermission('racks.create');
        $rackId = (int)($_POST['rack_id'] ?? 0);
        $zoneId = (int)($_POST['zone_id'] ?? 0);
        $newTotal = (int)($_POST['new_total_slots'] ?? 0);
        if ($rackId > 0 && $newTotal > 0 && $newTotal <= 24) {
            try {
                Zone::adjustSlots($db, $rackId, $newTotal);
                header('Location: index.php?page=zones&adjusted_slots=1&zone_id=' . $zoneId);
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        } else {
            $error = "Jumlah slot tidak valid (minimal 1, maksimal 24).";
        }
    }
}

$zones = Zone::getAllWithCapacity($db);
$pageTitle = 'Peta Gudang';
$pageSubtitle = 'Tata letak rak penyimpanan dan ketersediaan kapasitas slot gudang.';
ob_start();
require __DIR__ . '/../Views/zones/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

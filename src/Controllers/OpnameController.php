<?php
requirePermission('opname.view');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Opname.php';
require_once __DIR__ . '/../Models/Zone.php';

$page = $_GET['page'] ?? 'opname';
$action = $_GET['action'] ?? 'index';

if (empty($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$db = getDB();
$userId = (int)$_SESSION['user_id'];
$role = $_SESSION['user_role'] ?? '';


if ($action === 'initiate') {
    requirePermission('opname.initiate');
}
if ($action === 'finalize') {
    requirePermission('opname.finalize');
}
if ($action === 'scan') {
    requirePermission('opname.scan');
}
if ($action === 'cancel') {
    requirePermission('opname.initiate');
}



$error = '';
$success = '';

if ($action === 'initiate') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $targetType = $_POST['target_type'] ?? 'zone';
        $zoneId = (int)($_POST['zone_id'] ?? 0);
        $rackId = (int)($_POST['rack_id'] ?? 0);
        
        $slotIds = [];
        if ($targetType === 'zone' && $zoneId > 0) {
            $stmt = $db->prepare("SELECT rs.id FROM rack_slots rs 
                                  JOIN racks r ON rs.rack_id = r.id 
                                  WHERE r.zone_id = ?");
            $stmt->execute([$zoneId]);
            $slotIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } elseif ($targetType === 'rack' && $rackId > 0) {
            $stmt = $db->prepare("SELECT id FROM rack_slots WHERE rack_id = ?");
            $stmt->execute([$rackId]);
            $slotIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        
        if (empty($slotIds)) {
            $_SESSION['opname_error'] = "Gagal inisiasi: Tidak ada slot yang ditemukan pada target tersebut.";
        } else {
            try {
                $opnameNo = 'OPN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
                Opname::create($db, $opnameNo, $userId, $slotIds);
                $_SESSION['opname_success'] = "Sesi Stock Opname $opnameNo berhasil diinisiasi.";
            } catch (Exception $e) {
                $_SESSION['opname_error'] = "Terjadi kesalahan: " . $e->getMessage();
            }
        }
        header("Location: index.php?page=opname");
        exit;
    }
}

elseif ($action === 'finalize') {
    $opnameId = (int)($_POST['opname_id'] ?? 0);
    if ($opnameId > 0) {
        try {
            Opname::finalize($db, $opnameId, $userId);
            $_SESSION['opname_success'] = "Hasil Stock Opname berhasil diterapkan dan data stok telah diperbarui.";
        } catch (Exception $e) {
            $_SESSION['opname_error'] = "Gagal finalisasi: " . $e->getMessage();
        }
    }
    header("Location: index.php?page=opname");
    exit;
}

elseif ($action === 'cancel') {
    $opnameId = (int)($_POST['opname_id'] ?? 0);
    if ($opnameId > 0) {
        try {
            Opname::cancel($db, $opnameId);
            $_SESSION['opname_success'] = "Sesi Stock Opname berhasil dibatalkan.";
        } catch (Exception $e) {
            $_SESSION['opname_error'] = "Gagal membatalkan sesi: " . $e->getMessage();
        }
    }
    header("Location: index.php?page=opname");
    exit;
}

elseif ($action === 'scan') {
    $activeOpname = Opname::getActive($db);
    if (!$activeOpname) {
        header("Location: index.php?page=opname");
        exit;
    }
    
    $details = Opname::getDetails($db, $activeOpname['id']);
    
    $pageTitle = 'Hitung Fisik (Opname)';
    ob_start();
    require __DIR__ . '/../Views/opname/scan.php';
    $content = ob_get_clean();
    require __DIR__ . '/../Views/layouts/main.php';
    exit;
}

$activeOpname = Opname::getActive($db);
$history = Opname::getHistory($db);
$zones = Zone::getAllWithCapacity($db);
$racks = $db->query("SELECT r.id, r.rack_code, z.name AS zone_name 
                     FROM racks r 
                     JOIN zones z ON r.zone_id = z.id 
                     ORDER BY z.name, r.rack_code")->fetchAll();

$details = [];
if ($activeOpname) {
    $details = Opname::getDetails($db, $activeOpname['id']);
}

if (!empty($_SESSION['opname_error'])) {
    $error = $_SESSION['opname_error'];
    unset($_SESSION['opname_error']);
}
if (!empty($_SESSION['opname_success'])) {
    $success = $_SESSION['opname_success'];
    unset($_SESSION['opname_success']);
}

$pageTitle = 'Stock Opname Digital';
$pageSubtitle = 'Inisiasi audit fisik gudang, verifikasi QR Code, dan sinkronisasi selisih stok.';
ob_start();
require __DIR__ . '/../Views/opname/index.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

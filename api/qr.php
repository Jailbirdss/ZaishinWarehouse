<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Item.php';

header('Content-Type: application/json');
if (empty($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }

$db   = getDB();
$code = trim($_GET['code'] ?? '');

if (!$code) { echo json_encode(['error' => 'QR code diperlukan']); exit; }

$item = Item::getByQrCode($db, $code);

if (!$item) { echo json_encode(['error' => 'Barang tidak ditemukan']); exit; }

echo json_encode($item);

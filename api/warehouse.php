<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Zone.php';

header('Content-Type: application/json');
if (empty($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }

$db     = getDB();
$zoneId = (int)($_GET['zone_id'] ?? 0);

if ($zoneId <= 0) {

    $zones = Zone::getAllWithCapacity($db);
    echo json_encode($zones);
    exit;
}

$result = Zone::getRacksWithSlotsByZone($db, $zoneId);
echo json_encode($result);

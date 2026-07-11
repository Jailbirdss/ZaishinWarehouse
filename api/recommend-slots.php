<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Transaction.php';

header('Content-Type: application/json');
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = getDB();
$categoryId = (int)($_GET['category_id'] ?? 0);

if (!$categoryId) {
    echo json_encode(['error' => 'category_id diperlukan']);
    exit;
}

$slots = Transaction::getRecommendedSlots($db, $categoryId);
echo json_encode($slots);

<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Notification.php';

header('Content-Type: application/json');
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = getDB();
$userId = (int)$_SESSION['user_id'];
$role = $_SESSION['user_role'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $type = $_GET['type'] ?? 'unread_count';
    
    if ($type === 'unread_count') {
        $count = Notification::getUnreadCount($db, $userId, $role);
        echo json_encode(['count' => $count]);
        exit;
    }
    
    if ($type === 'list') {
        $list = Notification::getByUser($db, $userId, $role, 5);
        echo json_encode($list);
        exit;
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    if ($action === 'mark_read') {
        $id = (int)($data['id'] ?? 0);
        if ($id > 0) {
            Notification::markAsRead($db, $id);
            echo json_encode(['success' => true]);
            exit;
        }
    }
    
    if ($action === 'mark_all_read') {
        Notification::markAllAsRead($db, $userId, $role);
        echo json_encode(['success' => true]);
        exit;
    }
}

http_response_code(400);
echo json_encode(['error' => 'Bad request']);

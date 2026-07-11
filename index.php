<?php
session_start();
require_once __DIR__ . '/config/database.php';
tryAutoLogin();

$page   = $_GET['page']   ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

$publicPages = ['login'];

if (!in_array($page, $publicPages, true)) {
    requireLogin();
}

match ($page) {
    'login'     => require __DIR__ . '/src/Controllers/AuthController.php',
    'logout'    => require __DIR__ . '/src/Controllers/AuthController.php',
    'dashboard' => require __DIR__ . '/src/Controllers/DashboardController.php',
    'stock'     => require __DIR__ . '/src/Controllers/StockViewController.php',
    'items'     => require __DIR__ . '/src/Controllers/ItemController.php',
    'zones'     => require __DIR__ . '/src/Controllers/ZoneController.php',
    'inbound'   => require __DIR__ . '/src/Controllers/InboundController.php',
    'outbound'  => require __DIR__ . '/src/Controllers/OutboundController.php',
    'restock'   => require __DIR__ . '/src/Controllers/RestockController.php',
    'reports'   => require __DIR__ . '/src/Controllers/ReportController.php',
    'users'     => require __DIR__ . '/src/Controllers/UserController.php',
    'roles'     => require __DIR__ . '/src/Controllers/RoleController.php',
    'opname'      => require __DIR__ . '/src/Controllers/OpnameController.php',
    'opname-scan' => require __DIR__ . '/src/Controllers/OpnameController.php',
    'notifications' => require __DIR__ . '/src/Controllers/NotificationController.php',
    'sales-orders' => require __DIR__ . '/src/Controllers/SalesOrderController.php',
    'relocation' => require __DIR__ . '/src/Controllers/RelocationController.php',
    default     => require __DIR__ . '/src/Controllers/DashboardController.php',
};


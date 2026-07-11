<?php
require_once __DIR__ . '/../config/database.php';
$db = getDB();
$users = $db->query("SELECT id, name, username, role, is_active FROM users")->fetchAll(PDO::FETCH_ASSOC);
echo "USERS IN DB:\n";
print_r($users);

$roles = $db->query("SELECT role_key, display_name FROM roles")->fetchAll(PDO::FETCH_ASSOC);
echo "\nROLES IN DB:\n";
print_r($roles);

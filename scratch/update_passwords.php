<?php
require_once __DIR__ . '/../config/database.php';
$db = getDB();

$adminHash = password_hash('admin', PASSWORD_DEFAULT);
$fikriHash = password_hash('fikri', PASSWORD_DEFAULT);

$db->prepare("UPDATE users SET password = ? WHERE username = 'admin'")->execute([$adminHash]);
$db->prepare("UPDATE users SET password = ? WHERE username = 'fikri'")->execute([$fikriHash]);

echo "Passwords updated successfully!\n";
echo "admin hash: $adminHash\n";
echo "fikri hash: $fikriHash\n";

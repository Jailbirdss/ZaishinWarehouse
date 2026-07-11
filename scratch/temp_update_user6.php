<?php
require_once __DIR__ . '/../config/database.php';
$db = getDB();
$db->query('UPDATE users SET name = \'Fandy Ahmad\', username = \'fandy\', password = \'$2y$10$d6qUZGmAGRJeXp1W/kEZjeoezJEwR.iyBJkr0J4ykp7DDxmof14/e\', avatar = \'FA\' WHERE id = 6');
echo "Updated successfully!\n";

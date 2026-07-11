<?php
$hash = '$2y$10$UlCBUTu2c7FnQnxwRNPaHuOX2WgHzUxXrxlhfn14NqtPLpkJYd3G2';
$attempts = [
    'admin', 'admin123', 'admin1234', 'admin_gudang', 'root', '12345', '123456', '12345678', 
    'password', 'zaishin', 'wms', 'adminwms', 'gudang', 'etmin', 'datang', 'etmindatang', 
    'admin123!', 'Admin123', 'Admin', 'administrator', 'fikri', 'fikrianwar', 'staf', 'staff', 
    'user', 'users', 'testing', 'test', 'admin_psi', 'psi', 'warehouse', 'zaishin123', 'wms-psi'
];
foreach ($attempts as $a) {
    if (password_verify($a, $hash)) {
        echo "MATCH FOUND: '$a'\n";
        exit;
    }
}
echo "NO MATCH FOUND\n";

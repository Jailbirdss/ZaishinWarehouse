<?php
requirePermission('roles.manage');

$db    = getDB();
$error = null;
$success = null;

$action = $_GET['action'] ?? '';

// Defining the master list of all permissions grouped by module
$allPermissions = [
    'Dashboard & Utama' => [
        'dashboard.view' => 'Melihat Dashboard & Statistik Utama KPI',
        'notifications.view' => 'Melihat Pusat Notifikasi & Alarm Stok'
    ],
    'Barang & Inventaris' => [
        'stock.view' => 'Melihat Katalog Barang & Level Stok Aktif',
        'items.view' => 'Melihat Detail Spesifikasi Barang',
        'items.create' => 'Menambah Master Barang Baru',
        'items.edit' => 'Mengubah Master Barang',
        'items.delete' => 'Menghapus Master Barang'
    ],
    'Seksi & Rak Gudang' => [
        'zones.view' => 'Melihat Peta Gudang & Kapasitas Seksi',
        'zones.create' => 'Menambah Seksi/Zona Baru',
        'zones.delete' => 'Menghapus Seksi/Zona',
        'racks.create' => 'Menambah Rak Baru di Zona',
        'racks.delete' => 'Menghapus Rak atau Slot dari Gudang'
    ],
    'Barang Masuk (Inbound)' => [
        'inbound.view' => 'Melihat Daftar Inbound',
        'inbound.create' => 'Memproses Putaway & Pemindaian QR Barang Masuk',
        'restock.view' => 'Melihat Permintaan Restock',
        'restock.create' => 'Membuat Permintaan Restock Baru (PO)',
        'restock.approve' => 'Menyetujui/Menolak Permintaan Restock (PO)'
    ],
    'Barang Keluar (Outbound)' => [
        'outbound.view' => 'Melihat Daftar Outbound',
        'outbound.create' => 'Memproses Picking & Pemindaian QR Barang Keluar',
        'sales_orders.view' => 'Melihat Daftar Pesanan Penjualan (SO)',
        'sales_orders.create' => 'Membuat Pesanan Penjualan Baru'
    ],
    'Mutasi Internal' => [
        'relocation.view' => 'Melihat Riwayat Mutasi Stok',
        'relocation.create' => 'Memproses Mutasi/Pemindahan Stok Antar Slot'
    ],
    'Stock Opname (Audit)' => [
        'opname.view' => 'Melihat Daftar Sesi Stock Opname',
        'opname.initiate' => 'Membuat Sesi Stock Opname Baru',
        'opname.scan' => 'Melakukan Audit Blind Count (Scan Slot)',
        'opname.finalize' => 'Melakukan Finalisasi & Penyesuaian Selisih Opname'
    ],
    'Manajemen Staf & Akses' => [
        'users.view' => 'Melihat Daftar Pengguna',
        'users.create' => 'Menambah Akun Staf Baru',
        'users.edit' => 'Mengubah Data Staf',
        'users.delete' => 'Menghapus Akun Staf',
        'users.toggle' => 'Mengaktifkan/Menonaktifkan Akun Staf',
        'roles.manage' => 'Mengelola Peran & Konfigurasi Izin Akses (RBAC)'
    ],
    'Laporan & Ekspor' => [
        'reports.view' => 'Melihat Laporan Transaksi & Ekspor Excel/PDF'
    ]
];

$builtInRoles = ['admin_gudang', 'petugas_gudang', 'divisi_penjualan', 'divisi_pembelian', 'manajemen'];

if ($action === 'delete_role' && isset($_GET['key'])) {
    $roleKey = trim($_GET['key']);
    if (in_array($roleKey, $builtInRoles, true)) {
        $error = "Peran bawaan sistem ('$roleKey') tidak dapat dihapus.";
    } else {
        try {
            // Check if there are users with this role
            $checkUsers = $db->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
            $checkUsers->execute([$roleKey]);
            if ((int)$checkUsers->fetchColumn() > 0) {
                throw new Exception("Peran ini sedang digunakan oleh beberapa akun staf. Ubah peran staf tersebut terlebih dahulu.");
            }
            
            $db->beginTransaction();
            // Delete permission mappings
            $stmt1 = $db->prepare("DELETE FROM role_permissions WHERE role = ?");
            $stmt1->execute([$roleKey]);
            // Delete role record
            $stmt2 = $db->prepare("DELETE FROM roles WHERE role_key = ?");
            $stmt2->execute([$roleKey]);
            $db->commit();
            
            header('Location: index.php?page=roles&deleted=1');
            exit;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $error = $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    
    if ($postAction === 'add_role') {
        $displayName = trim($_POST['display_name'] ?? '');
        $roleKey     = trim($_POST['role_key'] ?? '');
        
        // Clean role_key: lowercase, alphanumeric and underscores only
        $roleKey = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $roleKey));
        
        if (empty($displayName) || empty($roleKey)) {
            $error = "Nama Peran dan Kode Peran wajib diisi.";
        } else {
            try {
                // Check duplicate key
                $check = $db->prepare("SELECT COUNT(*) FROM roles WHERE role_key = ?");
                $check->execute([$roleKey]);
                if ((int)$check->fetchColumn() > 0) {
                    throw new Exception("Kode Peran '$roleKey' sudah terdaftar.");
                }
                
                $stmt = $db->prepare("INSERT INTO roles (role_key, display_name) VALUES (?, ?)");
                $stmt->execute([$roleKey, $displayName]);
                
                // Set default dashboard view for new role
                $db->prepare("INSERT INTO role_permissions (role, permission_name) VALUES (?, 'dashboard.view')")->execute([$roleKey]);
                
                header('Location: index.php?page=roles&saved=1');
                exit;
            } catch (Exception $e) {
                $error = "Gagal membuat peran: " . $e->getMessage();
            }
        }
    } elseif ($postAction === 'edit_permissions') {
        $roleKey = trim($_POST['role_key'] ?? '');
        $permissions = $_POST['permissions'] ?? [];
        
        if (!empty($roleKey)) {
            try {
                // Safeguard against self-lockout: if editing current user's role, roles.manage must be retained
                if ($roleKey === ($_SESSION['user_role'] ?? '') && !in_array('roles.manage', $permissions, true)) {
                    // Check if current user had roles.manage before
                    $checkPrev = $db->prepare("SELECT COUNT(*) FROM role_permissions WHERE role = ? AND permission_name = 'roles.manage'");
                    $checkPrev->execute([$roleKey]);
                    if ((int)$checkPrev->fetchColumn() > 0) {
                        throw new Exception("Anda tidak dapat menghapus izin 'roles.manage' dari peran Anda sendiri untuk menghindari terkunci (lockout) dari sistem.");
                    }
                }

                $db->beginTransaction();
                
                // Clear old permissions
                $stmtDel = $db->prepare("DELETE FROM role_permissions WHERE role = ?");
                $stmtDel->execute([$roleKey]);
                
                // Insert selected permissions
                $stmtIns = $db->prepare("INSERT INTO role_permissions (role, permission_name) VALUES (?, ?)");
                foreach ($permissions as $pName) {
                    $stmtIns->execute([$roleKey, $pName]);
                }
                
                $db->commit();
                
                // If it's current user's role, reload permissions (optional but good practice)
                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === $roleKey) {
                    // Fetch new permissions into session if cached, but we check dynamically from DB anyway.
                }
                
                header('Location: index.php?page=roles&updated=1');
                exit;
            } catch (Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $error = "Gagal menyimpan hak akses: " . $e->getMessage();
            }
        } else {
            $error = "ID Peran tidak valid.";
        }
    }
}

// Fetch all roles with user counts
$rolesQuery = $db->query("
    SELECT r.role_key, r.display_name, COUNT(u.id) AS user_count
    FROM roles r
    LEFT JOIN users u ON r.role_key = u.role
    GROUP BY r.role_key
    ORDER BY r.created_at ASC
");
$rolesList = $rolesQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch all role permission mappings for ease of access
$permsMapping = [];
$permsQuery = $db->query("SELECT role, permission_name FROM role_permissions");
while ($row = $permsQuery->fetch(PDO::FETCH_ASSOC)) {
    $permsMapping[$row['role']][] = $row['permission_name'];
}

$pageTitle = 'Manajemen Peran & Akses';
$pageSubtitle = 'Konfigurasi peran pengguna gudang dan batasan akses fitur aplikasi secara dinamis.';

ob_start();
require __DIR__ . '/../Views/users/roles.php';
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';

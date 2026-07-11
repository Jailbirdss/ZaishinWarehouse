<?php
date_default_timezone_set('Asia/Jakarta');
function loadEnv(string $path): void {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
loadEnv(__DIR__ . '/../.env');

define('BASE_URL', '/wms-psi');

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'db_wms');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: '');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            $pdo->exec("SET time_zone = '+07:00'");
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

function tryAutoLogin(): bool {
    if (!empty($_SESSION['user_id'])) {
        return true;
    }
    $cookieName = 'remember_me';
    if (empty($_COOKIE[$cookieName])) {
        return false;
    }
    $cookieValue = $_COOKIE[$cookieName];
    $parts = explode('|', $cookieValue);
    if (count($parts) !== 2) {
        setcookie($cookieName, '', time() - 3600, '/');
        return false;
    }
    list($userId, $signature) = $parts;
    $secret = getenv('APP_SECRET') ?: 'zaishin-wms-secure-key-189f36f9a0c';
    $expectedSignature = hash_hmac('sha256', $userId, $secret);
    if (hash_equals($expectedSignature, $signature)) {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            if ($user) {
                $_SESSION['user_id']     = $user['id'];
                $_SESSION['user_name']   = $user['name'];
                $_SESSION['user_role']   = $user['role'];
                $_SESSION['user_avatar'] = $user['avatar'];
                return true;
            }
        } catch (Exception $e) {
        }
    }
    setcookie($cookieName, '', time() - 3600, '/');
    return false;
}

function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        if (tryAutoLogin()) {
            return;
        }
        header('Location: index.php?page=login');
        exit;
    }
}

function requireRole(array $allowed): void {
    requireLogin();
    if (!in_array($_SESSION['user_role'] ?? '', $allowed, true)) {
        header('Location: index.php?page=dashboard');
        exit;
    }
}

function hasPermission(string $permission): bool {
    if (empty($_SESSION['user_role'])) {
        return false;
    }
    static $userPermissions = null;
    if ($userPermissions === null) {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT permission_name FROM role_permissions WHERE role = ?");
            $stmt->execute([$_SESSION['user_role']]);
            $userPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (Exception $e) {
            $userPermissions = [];
        }
    }
    return in_array($permission, $userPermissions, true);
}

function requirePermission(string $permission): void {
    requireLogin();
    if (!hasPermission($permission)) {
        header('Location: index.php?page=dashboard');
        exit;
    }
}


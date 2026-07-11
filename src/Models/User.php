<?php

class User {

    public static function getByUsername(PDO $db, string $username) {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public static function getAll(PDO $db) {
        return $db->query("SELECT * FROM users ORDER BY role, name")->fetchAll();
    }

    public static function save(PDO $db, string $name, string $username, string $role, string $password = '', int $id = 0): bool {
        $av = strtoupper(substr(preg_replace('/[^a-zA-Z ]/', '', $name), 0, 1)) .
              strtoupper(substr(explode(' ', trim($name))[1] ?? 'X', 0, 1));

        if ($id > 0) {
            if ($password !== '') {
                $stmt = $db->prepare("UPDATE users SET name=?, username=?, role=?, avatar=?, password=? WHERE id=?");
                return $stmt->execute([$name, $username, $role, $av, password_hash($password, PASSWORD_DEFAULT), $id]);
            } else {
                $stmt = $db->prepare("UPDATE users SET name=?, username=?, role=?, avatar=? WHERE id=?");
                return $stmt->execute([$name, $username, $role, $av, $id]);
            }
        } else {
            $hashedPassword = password_hash($password ?: 'password123', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (name, username, role, avatar, password) VALUES (?,?,?,?,?)");
            return $stmt->execute([$name, $username, $role, $av, $hashedPassword]);
        }
    }

    public static function toggleActive(PDO $db, int $id): bool {
        $stmt = $db->prepare("UPDATE users SET is_active = NOT is_active WHERE id=?");
        return $stmt->execute([$id]);
    }

    public static function delete(PDO $db, int $id): bool {
        $stmt = $db->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute([$id]);
    }
}

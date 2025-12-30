<?php
require_once 'Database.php';

class UserRepository {
    public function getAll() {
        return Database::getConnect()
        ->query("SELECT * FROM users")
        ->fetchAll();
    }

    public function create($fullname, $email, $password) {
        $stmt = Database::getConnect()
        ->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$fullname, $email, $password]);
    }
}


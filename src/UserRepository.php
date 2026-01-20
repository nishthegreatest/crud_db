<?php
require_once 'Database.php';

class UserRepository {
    public function getAll() {
        return Database::getConnect()
        ->query("SELECT id, fullname AS name, email, password FROM users ORDER BY id ASC")
        ->fetchAll();
    }

    public function create($name, $email, $password) {
        $stmt = Database::getConnect()
        ->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $email, $password]);
    }

    public function update($id, $name, $email, $password) {
        $stmt = Database::getConnect()->prepare(
            "UPDATE users
            SET fullname = ?, email = ?, password = ?
            WHERE id = ?"
        );

        return $stmt->execute([$name, $email, $password, $id]);
    }

    public function delete($id) {
        $stmt = Database::getConnect()->prepare(
            "DELETE FROM users WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }
}

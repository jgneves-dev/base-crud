<?php

namespace App\Models;

class UserModel extends BaseModel {
    protected $table = 'users';

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (name, email, password) VALUES (:name, :email, :password)");
        return $stmt->execute($data);
    }
}
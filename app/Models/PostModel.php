<?php

namespace App\Models;

use App\Models\BaseModel;

class PostModel extends BaseModel {
    protected $table = 'posts';

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT posts.*, users.name AS author 
            FROM {$this->table}
            JOIN users ON posts.user_id = users.id
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT posts.*, users.name AS author 
            FROM {$this->table}
            JOIN users ON posts.user_id = users.id
            WHERE posts.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO {$this->table} (title, content, user_id) 
            VALUES (:title, :content, :user_id)
        ");
        return $stmt->execute([
            'title' => $data['title'],
            'content' => $data['content'],
            'user_id' => $data['user_id']
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table} 
            SET title = :title, content = :content 
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute([
            'title' => $data['title'],
            'content' => $data['content'],
            'id' => $id,
            'user_id' => $data['user_id']
        ]);
    }

    public function delete($id, $userId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM {$this->table} 
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
}
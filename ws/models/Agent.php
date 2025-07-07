<?php
require_once __DIR__ . '/../db.php';

class Agent {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM agent");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM agent WHERE idAgent = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO agent (nom, prenom, motdepasse, email, role, etatActif) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->prenom,
            $data->motdepasse,
            $data->email,
            $data->role,
            $data->etatActif
        ]);
        return $db->lastInsertId();
    }
}

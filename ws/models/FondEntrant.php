<?php
require_once __DIR__ . '/../db.php';
class FondEntrant {
    public static function getAll(){
        $db = getDB();
        $stmt = $db->query("SELECT * FROM fondEntrant");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO fondEntrant (montant, datefond, descri) VALUES (?, ?, ?)");
        $stmt->execute([
            $data->montant,
            $data->datefond,
            $data->descri
        ]);
        return $db->lastInsertId();
    }
}
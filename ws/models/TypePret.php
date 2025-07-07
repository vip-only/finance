<?php
require_once __DIR__ . '/../db.php';

class TypePret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM type_pret WHERE dateAbolition IS NULL");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getAllWithDeleted() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM type_pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM type_pret WHERE idTypePret = ? AND dateAbolition IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO type_pret (libelle, taux, assurance, dateCreation, pretmin, pretmax, dureeMoisMax) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->libelle,
            $data->taux,
            $data->assurance ?? 0.00,
            date('Y-m-d'),
            $data->pretmin,
            $data->pretmax,
            $data->dureeMoisMax
        ]);
        return $db->lastInsertId();
    }
    
    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE type_pret SET libelle = ?, taux = ?, assurance = ?, pretmin = ?, pretmax = ?, dureeMoisMax = ? WHERE idTypePret = ? AND dateAbolition IS NULL");      
        $stmt->execute([
            $data->libelle,
            $data->taux,
            $data->assurance ?? 0.00,
            $data->pretmin,
            $data->pretmax,
            $data->dureeMoisMax,
            $id
        ]);
        return $stmt->rowCount() > 0;
    }
    
    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE type_pret SET dateAbolition = ? WHERE idTypePret = ? AND dateAbolition IS NULL");
        $stmt->execute([date('Y-m-d'), $id]);
        return $stmt->rowCount() > 0;
    }
    
    public static function restore($id) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE type_pret SET dateAbolition = NULL WHERE idTypePret = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
    
    public static function getDeleted() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM type_pret WHERE dateAbolition IS NOT NULL");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function isDeleted($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT dateAbolition FROM type_pret WHERE idTypePret = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['dateAbolition'] !== null;
    }
    
    public static function hardDelete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM type_pret WHERE idTypePret = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
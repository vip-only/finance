<?php
require_once __DIR__ . '/../db.php';

class Clients {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM client");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM client WHERE idClient = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO client (nom, prenom, motdepasse, email, telephone, adresse, dateNaissance, profession, revenuMensuel, etatActif) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->prenom,
            password_hash($data->motdepasse, PASSWORD_DEFAULT),
            $data->email,
            $data->telephone ?? null,
            $data->adresse ?? null,
            $data->dateNaissance ?? null,
            $data->profession ?? null,
            $data->revenuMensuel ?? null,
            1 
        ]);
        return $db->lastInsertId();
    }
    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE client SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE idClient = ?");
        $stmt->execute([$data->nom, $data->prenom, $data->email, $data->telephone, $id]);
    }   
    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM client WHERE idClient = ?");
        $stmt->execute([$id]);
    }
    public static function getClientsDetails() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM client JOIN compteClient ON client.idClient = compteClient.idClient ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
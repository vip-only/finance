<?php
require_once __DIR__ . '/../db.php';

class EtatPret {
    public static function getEtatsCourants() {
        $db = getDB();
        $res = $db->query("SELECT idPret, etat FROM etat_pret WHERE idEtatPret IN (SELECT MAX(idEtatPret) FROM etat_pret GROUP BY idPret)");
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function changerEtat($idPret, $etat) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO etat_pret (idPret, etat, dateEtat) VALUES (?, ?, NOW())");
        $stmt->execute([$idPret, $etat]);
    }
}
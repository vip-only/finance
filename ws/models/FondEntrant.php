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
    public static function getFondsParMois() {
        $db = getDB();
        $query = " WITH mouvements AS (
                SELECT DATE_FORMAT(datefond, '%Y-%m') AS periode, montant AS entree, 0 AS sortie
                FROM fondEntrant
                UNION ALL
                SELECT DATE_FORMAT(COALESCE(datePaiement, NOW()), '%Y-%m') AS periode, montantPaye AS entree, 0 AS sortie
                FROM remboursement
                UNION ALL
                SELECT DATE_FORMAT(dateAccepte, '%Y-%m') AS periode, 0 AS entree, montantAccorde AS sortie
                FROM pret
            ),
            mois AS (
                SELECT DISTINCT periode FROM mouvements
            ),
            mouvements_par_mois AS (
                SELECT
                    m.periode,
                    SUM(m.entree) AS total_entrees,
                    SUM(m.sortie) AS total_sorties
                FROM mouvements m
                GROUP BY m.periode
            ),
            mois_ordonnes AS (
                SELECT periode FROM mois ORDER BY periode
            ),
            cumul AS (
                SELECT
                    mo.periode,
                    (SELECT SUM(total_entrees) FROM mouvements_par_mois WHERE periode <= mo.periode) AS cum_entrees,
                    (SELECT SUM(total_sorties) FROM mouvements_par_mois WHERE periode <= mo.periode) AS cum_sorties
                FROM mois_ordonnes mo
            )
            SELECT
                periode,
                cum_entrees,
                cum_sorties,
                (cum_entrees - cum_sorties) AS solde_disponible
            FROM cumul
            ORDER BY periode";
            
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }       
}
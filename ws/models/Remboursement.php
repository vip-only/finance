<?php
require_once __DIR__ . '/../db.php';

class Remboursement {
    public static function getAll() {
        $db = getDB();
        $query = "SELECT r.*, c.nom, c.prenom, p.montantAccorde, mp.libelle as modePaiement 
                  FROM remboursement r
                  JOIN pret p ON r.idPret = p.idPret
                  JOIN client c ON p.idClient = c.idClient
                  LEFT JOIN modePaiement mp ON r.modePaiement = mp.idmodePaiement
                  ORDER BY r.datePaiement DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getAmortissementsSansRemboursement() {
        $db = getDB();
        $query = "SELECT a.idAmortissement, a.idPret, a.numMois, a.montantMensuel, a.datePaiementPrevue as dateEcheance,
                     a.capitalRembourse as capital_rembourse, a.capitalRestant as capital_restant, a.interet, a.assurance,
                     c.nom, c.prenom, p.montantAccorde, tp.libelle as typePret
              FROM amortissement a
              JOIN pret p ON a.idPret = p.idPret
              JOIN client c ON p.idClient = c.idClient
              JOIN type_pret tp ON p.idTypePret = tp.idTypePret
              LEFT JOIN remboursement r ON a.idAmortissement = r.idAmortissement
              WHERE r.idAmortissement IS NULL
              AND p.idPret IN (SELECT idPret FROM etat_pret WHERE etat = 2)
              ORDER BY a.datePaiementPrevue ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getStatutClients() {
        $db = getDB();
        $query = "SELECT 
                    c.idClient,
                    c.nom,
                    c.prenom,
                    c.email,
                    c.telephone,
                    p.idPret,
                    p.montantAccorde,
                    p.montantTotal,
                    p.dateDebutRemboursement,
                    p.dateFinRemboursement,
                    tp.libelle as typePret,
                    -- Calcul du montant déjà payé
                    COALESCE(SUM(r.montantPaye), 0) as montantPaye,
                    -- Calcul du reste à payer
                    (p.montantTotal - COALESCE(SUM(r.montantPaye), 0)) as resteAPayer,
                    -- Nombre d'échéances payées
                    COUNT(r.idPaiement) as echancesPaye,
                    -- Nombre d'échéances totales
                    p.dureeMois as echancesTotales,
                    -- Dernier paiement
                    MAX(r.datePaiement) as dernierPaiement,
                    -- Statut de retard
                    CASE 
                        WHEN COUNT(r.idPaiement) = 0 AND DATEDIFF(NOW(), p.dateDebutRemboursement) > 30 THEN 'En retard'
                        WHEN COUNT(r.idPaiement) > 0 AND DATEDIFF(NOW(), MAX(r.datePaiement)) > 45 THEN 'En retard'
                        WHEN (p.montantTotal - COALESCE(SUM(r.montantPaye), 0)) <= 0 THEN 'Soldé'
                        ELSE 'En cours'
                    END as statut
                  FROM client c
                  JOIN pret p ON c.idClient = p.idClient
                  JOIN type_pret tp ON p.idTypePret = tp.idTypePret
                  LEFT JOIN remboursement r ON p.idPret = r.idPret
                  WHERE p.idPret IN (SELECT idPret FROM etat_pret WHERE etat = 2)
                  GROUP BY c.idClient, p.idPret
                  ORDER BY c.nom, c.prenom";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function create($data) {
        $db = getDB();
        $query = "INSERT INTO remboursement (idPret, idAmortissement, numMois, montantPaye, capital_restant, capital_rembourse, interet, assurance, modePaiement, reference, datePaiement) 
                  VALUES (:idPret, :idAmortissement, :numMois, :montantPaye, :capital_restant, :capital_rembourse, :interet, :assurance, :modePaiement, :reference, NOW())";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':idPret' => $data['idPret'],
            ':idAmortissement' => $data['idAmortissement'],
            ':numMois' => $data['numMois'],
            ':montantPaye' => $data['montantPaye'],
            ':capital_restant' => $data['capital_restant'],
            ':capital_rembourse' => $data['capital_rembourse'],
            ':interet' => $data['interet'],
            ':assurance' => $data['assurance'],
            ':modePaiement' => $data['modePaiement'],
            ':reference' => $data['reference']
        ]);
    }
    
    public static function getById($id) {
        $db = getDB();
        $query = "SELECT r.*, c.nom, c.prenom, mp.libelle as modePaiement 
                  FROM remboursement r
                  JOIN pret p ON r.idPret = p.idPret
                  JOIN client c ON p.idClient = c.idClient
                  LEFT JOIN modePaiement mp ON r.modePaiement = mp.idmodePaiement
                  WHERE r.idPaiement = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function getModePaiements() {
        $db = getDB();
        $query = "SELECT * FROM modePaiement ORDER BY libelle";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getPretsPremierRemboursementEnRetard() {
        $db = getDB();
        $query = "SELECT p.idPret, c.nom, c.prenom, p.montantAccorde, p.dateDebutRemboursement, p.DELAI, tp.libelle as typePret
                FROM pret p
                JOIN client c ON p.idClient = c.idClient
                JOIN type_pret tp ON p.idTypePret = tp.idTypePret
                LEFT JOIN remboursement r ON p.idPret = r.idPret AND r.numMois = 1
                WHERE r.idPaiement IS NULL
                    AND p.idPret IN (SELECT idPret FROM etat_pret WHERE etat = 2)
                    AND DATE_ADD(p.dateAccepte, INTERVAL p.DELAI MONTH) < CURDATE()";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getInteretsParMois($dateDebut, $dateFin) {
        $db = getDB();
        $query = "SELECT 
                    DATE_FORMAT(datePaiement, '%Y-%m') as mois,
                    DATE_FORMAT(datePaiement, '%M %Y') as moisLibelle,
                    YEAR(datePaiement) as annee,
                    MONTH(datePaiement) as moisNum,
                    SUM(interet) as totalInteret
                FROM remboursement
                WHERE datePaiement BETWEEN :dateDebut AND :dateFin
                GROUP BY DATE_FORMAT(datePaiement, '%Y-%m')
                ORDER BY YEAR(datePaiement) ASC, MONTH(datePaiement) ASC";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':dateDebut' => $dateDebut,
            ':dateFin' => $dateFin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   
}
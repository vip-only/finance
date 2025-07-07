<?php
require_once __DIR__ . '/../db.php';

class Remboursement {
    public static function getAll() {
        $db = Db::getInstance();
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
        $db = Db::getInstance();
        $query = "SELECT a.*, c.nom, c.prenom, p.montantAccorde, tp.libelle as typePret
                  FROM amortissement a
                  JOIN pret p ON a.idPret = p.idPret
                  JOIN client c ON p.idClient = c.idClient
                  JOIN type_pret tp ON p.idTypePret = tp.idTypePret
                  LEFT JOIN remboursement r ON a.idAmortissement = r.idAmortissement
                  WHERE r.idAmortissement IS NULL
                  AND p.idPret IN (SELECT idPret FROM etat_pret WHERE etat = 2)
                  ORDER BY a.idAmortissement ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getStatutClients() {
        $db = Db::getInstance();
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
                    -- Dernière mensualité théorique
                    (SELECT a.montantMensuel FROM amortissement a WHERE a.idPret = p.idPret LIMIT 1) as mensualite,
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
        $db = Db::getInstance();
        $query = "INSERT INTO remboursement (idPret, idAmortissement, numMois, montantPaye, capital_restant, capital_rembourse, interet, assurance, modePaiement, reference) 
                  VALUES (:idPret, :idAmortissement, :numMois, :montantPaye, :capital_restant, :capital_rembourse, :interet, :assurance, :modePaiement, :reference)";
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
        $db = Db::getInstance();
        $query = "SELECT * FROM remboursement WHERE idPaiement = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function getModePaiements() {
        $db = Db::getInstance();
        $query = "SELECT * FROM modePaiement ORDER BY libelle";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
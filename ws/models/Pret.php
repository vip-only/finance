<?php
require_once __DIR__ . '/../db.php';

class Pret {
    

    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret WHERE idPret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();

        // Récupérer infos type_pret
        $stmt = $db->prepare("SELECT * FROM type_pret WHERE idTypePret = ?");
        $stmt->execute([$data->idTypePret]);
        $typePret = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$typePret) throw new Exception("Type de prêt invalide");

        // Récupérer infos client
        $stmt = $db->prepare("SELECT * FROM client WHERE idClient = ?");
        $stmt->execute([$data->idClient]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$client) throw new Exception("Client invalide");

        // Règles de gestion
        $montantAccorde = floatval($data->montantAccorde);
        $dureeMois = intval($data->dureeMois);
        $pretmin = floatval($typePret['pretmin']);
        $pretmax = floatval($typePret['pretmax']);
        $dureeMoisMax = intval($typePret['dureeMoisMax']);
        $taux = floatval($typePret['taux']) / 100;
        $revenuMensuel = floatval($client['revenuMensuel']);

        if ($montantAccorde < $pretmin || $montantAccorde > $pretmax) {
            throw new Exception("Le montant accordé doit être entre $pretmin et $pretmax.");
        }
        if ($dureeMois > $dureeMoisMax) {
            throw new Exception("La durée ne doit pas dépasser $dureeMoisMax mois.");
        }

        // type_retour = 1 mois (mensuel)
        $type_retour = 1;
        $nb_de_remboursement = $dureeMois / $type_retour;
        if ($nb_de_remboursement < 1) throw new Exception("Nombre de remboursements invalide.");

        $montantTotal = $montantAccorde + ($montantAccorde * $taux / $nb_de_remboursement * $nb_de_remboursement);

        if ($revenuMensuel * $type_retour < $montantTotal / $nb_de_remboursement) {
            throw new Exception("Le revenu mensuel du client est insuffisant pour ce prêt.");
        }

        // Calcul des dates
        $delai = intval($data->DELAI);
        $dateAccepte = date('Y-m-d');
        $dateDebutRemboursement = date('Y-m-d', strtotime("+$delai month", strtotime($dateAccepte)));
        $dateFinRemboursement = date('Y-m-d', strtotime("+$dureeMois month", strtotime($dateDebutRemboursement)));

        // Insertion du prêt
        $stmt = $db->prepare("INSERT INTO pret (idClient, idTypePret, montantAccorde, dureeMois, montantTotal, dateAccepte, DELAI, dateDebutRemboursement, dateFinRemboursement, modePaiement)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->idClient,
            $data->idTypePret,
            $montantAccorde,
            $dureeMois,
            $montantTotal,
            $dateAccepte,
            $delai,
            $dateDebutRemboursement,
            $dateFinRemboursement,
            $data->modePaiement
        ]);
        $idPret = $db->lastInsertId();

        // Insertion dans etat_pret (etat=1 : en attente)
        $stmt = $db->prepare("INSERT INTO etat_pret (idPret, etat, dateEtat) VALUES (?, 1, NOW())");
        $stmt->execute([$idPret]);

        return $idPret;
    }

    public static function update($id, $data) {
        $db = getDB();
        // Même logique de calcul des dates que dans create
        $delai = intval($data->DELAI);
        $dateAccepte = date('Y-m-d');
        $dateDebutRemboursement = date('Y-m-d', strtotime("+$delai month", strtotime($dateAccepte)));
        $dateFinRemboursement = date('Y-m-d', strtotime("+".intval($data->dureeMois)." month", strtotime($dateDebutRemboursement)));

        $stmt = $db->prepare("UPDATE pret SET idClient=?, idTypePret=?, montantAccorde=?, dureeMois=?, DELAI=?, dateDebutRemboursement=?, dateFinRemboursement=?, modePaiement=? WHERE idPret=?");
        $stmt->execute([
            $data->idClient,
            $data->idTypePret,
            $data->montantAccorde,
            $data->dureeMois,
            $delai,
            $dateDebutRemboursement,
            $dateFinRemboursement,
            $data->modePaiement,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM pret WHERE idPret = ?");
        $stmt->execute([$id]);
    }

    public static function getOptions($table, $idField, $labelField) {
        $db = getDB();
        $res = $db->query("SELECT $idField as id, $labelField as label FROM $table");
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function genererAmortissements($idPret) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret WHERE idPret = ?");
        $stmt->execute([$idPret]);
        $pret = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT * FROM type_pret WHERE idTypePret = ?");
        $stmt->execute([$pret['idTypePret']]);
        $typePret = $stmt->fetch(PDO::FETCH_ASSOC);

        $C = floatval($pret['montantAccorde']);
        $n = intval($pret['dureeMois']);
        $i = (floatval($typePret['taux']) / 100) / 12;
        // $taux_annuel = floatval($typePret['taux']) / 100;
        // $i = pow(1 + $taux_annuel, 1/12) - 1;
        $taux_assurance = floatval($typePret['assurance']) / 100;
        $assurance = $C * $taux_assurance / $n;
        $date = $pret['dateDebutRemboursement'];
        $capital_restant = $C;

        // Annuité constante
        $A = $C * ($i / (1 - pow(1 + $i, -$n)));
        echo "Annuité A brute : " . $A;

        for ($mois = 1; $mois <= $n; $mois++) {
            $interet = $capital_restant * $i;
            $capital_rembourse = $A - $interet;
            $capital_restant_apres = $capital_restant - $capital_rembourse;
            $montantTotal = $A + $assurance;
            $datePaiementPrevue = date('Y-m-d', strtotime("+".($mois-1)." month", strtotime($date)));

            $stmt = $db->prepare("INSERT INTO amortissement (idPret, numMois, datePaiementPrevue, annuite, interet, assurance, capitalRembourse, capitalRestant, montantTotal)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $idPret,
                $mois,
                $datePaiementPrevue,
                round($A,2),
                round($interet,2),
                round($assurance,2),
                round($capital_rembourse,2),
                round(max($capital_restant_apres,0),2),
                round($montantTotal,2)
            ]);
            $capital_restant = $capital_restant_apres;
        }
        return $i;
    }
}
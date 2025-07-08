<?php
require_once __DIR__ . '/../models/Remboursement.php';
require_once __DIR__ . '/../helpers/Utils.php';

class RemboursementController {
    public static function getAll() {
        $remboursements = Remboursement::getAll();
        Flight::json($remboursements);
    }
    
    public static function getById($id) {
        $remboursement = Remboursement::getById($id);
        if ($remboursement) {
            Flight::json($remboursement);
        } else {
            Flight::json(['error' => 'Remboursement non trouvé'], 404);
        }
    }
    
    public static function create() {
        try {
            $data = Flight::request()->data;
            $success = Remboursement::create($data);
            if ($success) {
                Flight::json(['message' => 'Remboursement enregistré avec succès']);
            } else {
                Flight::json(['error' => 'Erreur lors de l\'enregistrement du remboursement'], 500);
            }
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }
    
    public static function getAmortissementsSansRemboursement() {
        $amortissements = Remboursement::getAmortissementsSansRemboursement();
        Flight::json($amortissements);
    }
    
    public static function getStatutClients() {
        $statutClients = Remboursement::getStatutClients();
        Flight::json($statutClients);
    }
    
    public static function getModePaiements() {
        $modePaiements = Remboursement::getModePaiements();
        Flight::json($modePaiements);
    }

    public static function getPretsPremierRemboursementEnRetard() {
        $prets = Remboursement::getPretsPremierRemboursementEnRetard();
        Flight::json($prets);
    }
    public static function getInteretsParMois() {
        $dateDebut = Flight::request()->query['dateDebut'];
        $dateFin = Flight::request()->query['dateFin'];
        $result = Remboursement::getInteretsParMois($dateDebut, $dateFin);
        Flight::json($result);
    }
}
<?php
require_once __DIR__ . '/../models/EtatPret.php';

class EtatPretController {
    public static function getEtatsCourants() {
        Flight::json(EtatPret::getEtatsCourants());
    }

    public static function changerEtat($idPret) {
        $etat = Flight::request()->data->etat;
        EtatPret::changerEtat($idPret, $etat);
        Flight::json(['message' => 'Etat modifié']);
    }
}
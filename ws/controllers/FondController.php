<?php
require_once __DIR__ . '/../models/FondEntrant.php';
require_once __DIR__ . '/../helpers/Utils.php';

class FondController {
    public static function getAll() {
        $fonds = FondEntrant::getAll();
        Flight::json($fonds);
    }
    public static function create() {
        $data = Flight::request()->data;
        $id = FondEntrant::create($data);
        Flight::json(['message' => 'Fond Entrant ajouté', 'id' => $id]);
    }   
}    
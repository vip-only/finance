<?php
require_once __DIR__ . '/../models/Pret.php';

class PretController {
    public static function getAll() {
        $prets = Pret::getAll();
        Flight::json($prets);
    }

    public static function getById($id) {
        $pret = Pret::getById($id);
        Flight::json($pret);
    }

    public static function create() {
        try {
            $data = Flight::request()->data;
            $id = Pret::create($data);
            Flight::json(['message' => 'Prêt ajouté', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Pret::update($id, $data);
        Flight::json(['message' => 'Prêt modifié']);
    }

    public static function delete($id) {
        Pret::delete($id);
        Flight::json(['message' => 'Prêt supprimé']);
    }

    public static function getOptions($table, $idField, $labelField) {
        $options = Pret::getOptions($table, $idField, $labelField);
        Flight::json($options);
    }
}
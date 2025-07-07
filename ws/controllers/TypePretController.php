<?php
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../helpers/Utils.php';

class TypePretController {
    public static function getAll() {
        $typePrets = TypePret::getAll();
        Flight::json($typePrets);
    }
    public static function create()   
    {    
        $data = Flight::request()->data;
        $id = TypePret::create($data);
        Flight::json(['message' => 'Type de prêt ajouté', 'id' => $id]);
    }
    public static function getById($id) {
        $typePret = TypePret::getById($id);
        if ($typePret) {
            Flight::json($typePret);
        }
        else {
            Flight::json(['error' => 'Type de prêt non trouvé'], 404);
        }
    }
    public static function update($id) {
        $data = Flight::request()->data;
        $updated = TypePret::update($id, $data);
        if ($updated) {
            Flight::json(['message' => 'Type de prêt mis à jour']);
        }
        else {
            Flight::json(['error' => 'Type de prêt non trouvé ou pas de changement'], 404);
        }
    }
    public static function delete($id) {
        $deleted = TypePret::delete($id);
        if ($deleted) {
            Flight::json(['message' => 'Type de prêt supprimé']);
        } else {
            Flight::json(['error' => 'Type de prêt non trouvé ou déjà supprimé'], 404);
        }
    }
}

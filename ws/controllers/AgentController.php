<?php
require_once __DIR__ . '/../models/Agent.php';

class AgentController {
    public static function getAll() {
        $agents = Agent::getAll();
        Flight::json($agents);
    }

    public static function getById($id) {
        $agent = Agent::getById($id);
        Flight::json($agent);
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Agent::create($data);
        Flight::json(['message' => 'Agent ajouté', 'id' => $id]);
    }
}

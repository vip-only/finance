<?php
require_once __DIR__ . '/../models/Clients.php';
require_once __DIR__ . '/../helpers/Utils.php';

class ClientsController {
    public static function getAll() {
        $clients = Clients::getAll();
        Flight::json($clients);
    }
    public static function getById($id) {
        $client = Clients::getById($id);
        if ($client) {
            Flight::json($client);
        } else {
            Flight::json(['error' => 'Client not found'], 404);
        }
    }
    public static function create() {
        $data = Flight::request()->data;
        $id = Clients::create($data);
        Flight::json(['message' => 'Client added', 'id' => $id]);
    }    
    public static function update($id) {
        $data = Flight::request()->data;
        Clients::update($id, $data);
        Flight::json(['message' => 'Client updated']);
    }
    public static function delete($id) {
        Clients::delete($id);
        Flight::json(['message' => 'Client deleted']);
    }
    public static function getClientsDetails() {
        $clientsDetails = Clients::getClientsDetails();
        Flight::json($clientsDetails);
    }
}
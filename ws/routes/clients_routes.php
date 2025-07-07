<?php
require_once __DIR__ . '/../controllers/ClientsController.php';

Flight::route('GET /clients', ['ClientsController', 'getAll']);
Flight::route('GET /clients/@id', ['ClientsController', 'getById']);
Flight::route('POST /clients', ['ClientsController', 'create']);    
Flight::route('PUT /clients/@id', ['ClientsController', 'update']);
Flight::route('DELETE /clients/@id', ['ClientsController', 'delete']);
Flight::route('GET /clients/details', ['ClientsController', 'getClientsDetails']);
?>
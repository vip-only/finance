<?php
require_once __DIR__ . '/../controllers/FondController.php';

Flight::route('GET /fonds', ['FondController', 'getAll']);
Flight::route('POST /fonds', ['FondController', 'create']);
// Flight::route('PUT /fonds/@id', ['FondController', 'update']);
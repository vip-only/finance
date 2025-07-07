<?php
// require_once __DIR__ . '/../controllers/ValidationPretController.php';
require_once __DIR__ . '/../controllers/PretController.php';

// Flight::route('GET /validation-prets', ['ValidationPretController', 'getAll']);
// Flight::route('GET /validation-prets/@id', ['ValidationPretController', 'getById']);
// Flight::route('POST /validation-prets', ['ValidationPretController', 'create']);

Flight::route('GET /prets', ['PretController', 'getAll']);
Flight::route('GET /prets/@id', ['PretController', 'getById']);
Flight::route('POST /prets', ['PretController', 'create']);
Flight::route('PUT /prets/@id', ['PretController', 'update']);
Flight::route('DELETE /prets/@id', ['PretController', 'delete']);
Flight::route('GET /pret/options/@table/@idField/@labelField', ['PretController', 'getOptions']);
Flight::route('POST /prets/@id/amortissements', ['PretController', 'genererAmortissements']);



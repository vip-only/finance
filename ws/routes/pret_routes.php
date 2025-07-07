<?php
require_once __DIR__ . '/../controllers/ValidationPretController.php';

Flight::route('GET /validation-prets', ['ValidationPretController', 'getAll']);
Flight::route('GET /validation-prets/@id', ['ValidationPretController', 'getById']);
Flight::route('POST /validation-prets', ['ValidationPretController', 'create']);


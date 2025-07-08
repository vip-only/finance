<?php
require_once __DIR__ . '/../controllers/AgentController.php';

Flight::route('GET /agents', ['AgentController', 'getAll']);
Flight::route('GET /agents/@id', ['AgentController', 'getById']);
Flight::route('POST /agents', ['AgentController', 'create']);

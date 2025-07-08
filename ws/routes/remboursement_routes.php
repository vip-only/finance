<?php
require_once __DIR__ . '/../controllers/RemboursementController.php';

Flight::route('GET /remboursements/amortissements-sans-remboursement', ['RemboursementController', 'getAmortissementsSansRemboursement']);
Flight::route('GET /remboursements/statut-clients', ['RemboursementController', 'getStatutClients']);
Flight::route('GET /remboursements/mode-paiements', ['RemboursementController', 'getModePaiements']);
Flight::route('GET /remboursements/prets-premier-retard', ['RemboursementController', 'getPretsPremierRemboursementEnRetard']);
Flight::route('GET /remboursements/interets-par-mois', ['RemboursementController', 'getInteretsParMois']);

Flight::route('GET /remboursements', ['RemboursementController', 'getAll']);
Flight::route('GET /remboursements/@id', ['RemboursementController', 'getById']);
Flight::route('POST /remboursements', ['RemboursementController', 'create']);
<?php
require_once __DIR__ . '/../controllers/EtatPretController.php';

Flight::route('GET /etat_prets', ['EtatPretController', 'getEtatsCourants']);
Flight::route('POST /prets/@idPret/etat', ['EtatPretController', 'changerEtat']);
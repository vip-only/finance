<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/clients_routes.php';

require 'routes/login_routes.php';
require 'routes/agent_routes.php';
require 'routes/pret_routes.php';
require 'routes/clients_routes.php';
require 'routes/TypePret_routes.php';
require 'routes/remboursement_routes.php';
require 'routes/fonds_routes.php';
require 'routes/etat_pret_routes.php';
Flight::start();
<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/clients_routes.php';
require 'routes/pret_routes.php';
require 'routes/fonds_routes.php';
require 'routes/TypePret.php';
require 'routes/etat_pret_routes.php';
require 'routes/remboursement_routes.php';

Flight::start();
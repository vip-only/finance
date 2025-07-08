<?php
require_once __DIR__ . '/../controllers/LoginController.php';

Flight::route('POST /authentification', ['LoginController', 'authenticate']);

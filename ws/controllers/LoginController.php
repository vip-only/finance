<?php
require_once __DIR__ . '/../models/Login.php';

class LoginController {
    public static function authenticate() {
        $data = Flight::request()->data;
        $email = $data->email ?? null;
        $password = $data->password ?? null;

        if (!$email || !$password) {
            Flight::json(['success' => false, 'message' => 'Email et mot de passe requis'], 400);
            return;
        }

        $user = Login::authenticate($email, $password);

        if ($user) {
            session_start();
            $_SESSION['idAgent'] = $user['idAgent'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['role'] = $user['role'];
            Flight::json(['success' => true]);
        } else {
            Flight::json(['success' => false, 'message' => 'Invalid email or password'], 401);
        }
    }
}
<?php
require_once __DIR__ . '/../models/Login.php';
require_once __DIR__ . '/../helpers/Utils.php';



class LoginController {
    public static function authenticate() {
        $data = Flight::request()->data;
        $email = $data->email;
        $password = $data->password;

        $user = Login::authenticate($email, $password);

        // if ($user) {
        //     Flight::json([
        //         'message' => 'Authentication successful',
        //         'user' => $user
        //     ]);
        // } else {
        //     Flight::json(['message' => 'Invalid email or password'], 401);
        // }

         if ($user) {
            session_start();
            $_SESSION['idAgent'] = $user['idAgent'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['role'] = $user['role'];
            Flight::redirect('accueil.html');
        } else {
            Flight::json(['message' => 'Invalid email or password'], 401);
        }
    }   
}



<?php
require_once __DIR__ . '/../db.php';

class Login {
    public static function authenticate($email, $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM agent WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['motdepasse']) {
            return [
                'idAgent' => $user['idAgent'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'role' => $user['role'],
                'etatActif' => $user['etatActif']
            ];
        }
        return null;
    }
   
}



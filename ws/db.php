<?php
function getDB() {
    // $host = '172.60.0.17';
    // $dbname = 'db_s2_ETU003088';
    // $username = 'ETU003088';
    // $password = 'msH9C4Ui';
    $host = 'localhost';
    $dbname = 'banque';
    $username = 'root';
    $password = '';

    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}
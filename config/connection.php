<?php

$host     = 'localhost';
$dbname   = 'reclutamiento_vacantes';
$user     = 'root';
$password = '';


$conn = new mysqli($host, $user, $password, $dbname, 3306);
=======
$conn = new mysqli($host, $user, $password, $dbname);

$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]));
}
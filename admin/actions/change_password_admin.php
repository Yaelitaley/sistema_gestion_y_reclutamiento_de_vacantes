<?php

header('Content-Type: application/json');

require_once '../../config/connection.php';

$correo = trim($_POST['correo'] ?? '');
$password = trim($_POST['password'] ?? '');

if(empty($correo) || empty($password)){

    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios'
    ]);

    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    UPDATE usuarios
    SET password = ?
    WHERE email = ?
");

$stmt->bind_param(
    "ss",
    $hash,
    $correo
);

$stmt->execute();

if($stmt->affected_rows > 0){

    echo json_encode([
        'success' => true,
        'message' => 'Contraseña actualizada correctamente'
    ]);

}else{

    echo json_encode([
        'success' => false,
        'message' => 'Correo no encontrado'
    ]);

}

$stmt->close();
$conn->close();
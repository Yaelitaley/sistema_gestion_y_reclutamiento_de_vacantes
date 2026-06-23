<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../config/app_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.'
    ]);
    exit;
}

$correo = trim($_POST['correo'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($correo === '' || $password === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios.'
    ]);
    exit;
}

$stmt = $conn->prepare(
    'SELECT id, password, rol_id
     FROM usuarios
     WHERE email = ?
     AND rol_id IN (1,2)
     LIMIT 1'
);

$stmt->bind_param('s', $correo);
$stmt->execute();

$result = $stmt->get_result();
$usuario = $result ? $result->fetch_assoc() : null;

$stmt->close();

if (!$usuario || !password_verify($password, $usuario['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Credenciales incorrectas.'
    ]);
    exit;
}

$_SESSION['usuario_id'] = (int)$usuario['id'];
$_SESSION['rol_id'] = (int)$usuario['rol_id'];
$_SESSION['correo'] = $correo;

echo json_encode([
    'success' => true,
    'message' => 'Acceso correcto.'
]);

$conn->close();
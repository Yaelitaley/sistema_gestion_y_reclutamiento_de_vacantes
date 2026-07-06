<?php
if (ob_get_level()) ob_end_clean();

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/connection.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.'
    ]);
    exit;
}

$correo   = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? ''; 

if ($correo === '' || $password === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios.'
    ]);
    exit;
}

$stmt = $conn->prepare(
    'SELECT id, password, rol_id, estado, nombre_completo
     FROM usuarios
     WHERE correo = ?
     AND rol_id IN (1,2)
     LIMIT 1'
);

$stmt->bind_param('s', $correo);
$stmt->execute();

$result = $stmt->get_result();
$usuario = $result ? $result->fetch_assoc() : null;
$stmt->close();

if ($usuario && strtolower($usuario['estado']) !== 'activo') {
    echo json_encode([
        'success' => false,
        'message' => 'Esta cuenta está inactiva o bloqueada. Contacta al administrador principal.'
    ]);
    exit;
}

if ($usuario && password_verify($password, $usuario['password'])) {
    
    $_SESSION['usuario_id'] = (int)$usuario['id'];
    $_SESSION['rol_id']     = (int)$usuario['rol_id'];
    $_SESSION['correo']     = $correo;
    $_SESSION['nombre']     = $usuario['nombre_completo'] ?? 'Administrador';

    echo json_encode([
        'success' => true,
        'message' => 'Acceso correcto.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'El correo o la contraseña son incorrectos.'
    ]);
}

$conn->close();
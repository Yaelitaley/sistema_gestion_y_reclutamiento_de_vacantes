<?php
// Limpiar cualquier salida accidental previa para evitar corromper el JSON
if (ob_get_level()) ob_end_clean();

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../config/app_helpers.php';

// Asegurar que la sesión esté iniciada para poder guardar los datos del usuario
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

$correo = trim($_POST['correo'] ?? '');
// CORRECCIÓN: Quitamos trim() de la contraseña para que valide los caracteres exactos tal como se digitan
$password = $_POST['password'] ?? '';

if ($correo === '' || $password === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios.'
    ]);
    exit;
}

// Consulta preparada para buscar por la columna 'correo' y validar los roles correspondientes (1 o 2)
$stmt = $conn->prepare(
    'SELECT id, password, rol_id
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

// Verificar si el usuario existe y si la contraseña coincide con el hash encriptado
if (!$usuario || !password_verify($password, $usuario['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Credenciales incorrectas.'
    ]);
    exit;
}

// Guardar datos en la sesión de forma segura
$_SESSION['usuario_id'] = (int)$usuario['id'];
$_SESSION['rol_id'] = (int)$usuario['rol_id'];
$_SESSION['correo'] = $correo;

echo json_encode([
    'success' => true,
    'message' => 'Acceso correcto.'
]);

$conn->close();
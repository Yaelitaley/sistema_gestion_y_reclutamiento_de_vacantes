<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$correo            = trim($_POST['correo'] ?? '');
$clave_seguridad   = trim($_POST['clave_seguridad'] ?? '');
$password          = trim($_POST['password'] ?? '');
$confirm_password  = trim($_POST['confirm_password'] ?? '');

// Validar campos vacíos
if ($correo === '' || $clave_seguridad === '' || $password === '' || $confirm_password === '') {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

// Validar formato de correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo no es válido.']);
    exit;
}

// Validar que las contraseñas coincidan
if ($password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
    exit;
}

// Validar longitud mínima
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
    exit;
}

// Verificar que el correo pertenezca a un administrador (rol_id 1 o 2)
$stmt = $conn->prepare(
    "SELECT id, estado, clave_seguridad
     FROM usuarios
     WHERE correo = ?
     AND rol_id IN (1,2)
     LIMIT 1"
);
$stmt->bind_param('s', $correo);
$stmt->execute();
$result  = $stmt->get_result();
$usuario = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$usuario) {
    echo json_encode(['success' => false, 'message' => 'El correo no está registrado como administrador.']);
    exit;
}

if (strtolower($usuario['estado']) !== 'activo') {
    echo json_encode(['success' => false, 'message' => 'Esta cuenta está inactiva o bloqueada. Contacta al administrador principal.']);
    exit;
}

// Verificar que la cuenta tenga una clave de seguridad configurada
if (empty($usuario['clave_seguridad'])) {
    echo json_encode(['success' => false, 'message' => 'Esta cuenta no tiene una clave de seguridad configurada. Contacta al administrador principal.']);
    exit;
}

// Validar la clave de seguridad personal del usuario
if (!password_verify($clave_seguridad, $usuario['clave_seguridad'])) {
    echo json_encode(['success' => false, 'message' => 'La clave de seguridad es incorrecta.']);
    exit;
}

// Generar hash y actualizar contraseña
$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare(
    "UPDATE usuarios
     SET password = ?
     WHERE id = ?
     AND rol_id IN (1,2)"
);
$stmt->bind_param('si', $hash, $usuario['id']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar la contraseña. Intenta de nuevo.']);
}

$stmt->close();
$conn->close();
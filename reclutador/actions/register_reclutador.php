<?php
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$nombre         = trim($_POST['nombre']   ?? '');
$correo         = trim($_POST['correo']   ?? '');
$telefono       = trim($_POST['telefono'] ?? '');
$empresaId      = (int) ($_POST['empresa'] ?? 0);
$password       = trim($_POST['password'] ?? '');
$confirm        = trim($_POST['confirmPassword'] ?? '');
$claveSeguridad = trim($_POST['claveSeguridad'] ?? '');

if (empty($nombre) || empty($correo) || empty($password) || empty($confirm) || empty($claveSeguridad) || $empresaId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios, incluida la empresa.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo no es válido.']);
    exit;
}

if ($password !== $confirm) {
    echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
    exit;
}

if (strlen($claveSeguridad) < 4) {
    echo json_encode(['success' => false, 'message' => 'La clave de seguridad debe tener al menos 4 caracteres.']);
    exit;
}

// Verificar que la empresa exista
$stmt = $conn->prepare("SELECT id FROM empresas WHERE id = ?");
$stmt->bind_param('i', $empresaId);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'La empresa seleccionada no es válida.']);
    $stmt->close();
    exit;
}
$stmt->close();

// Verificar correo duplicado
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->bind_param('s', $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El correo ya está registrado.']);
    $stmt->close();
    exit;
}
$stmt->close();

// rol_id = 3 (Reclutador)
$rol_id    = 3;
$hash      = password_hash($password, PASSWORD_BCRYPT);
$claveHash = password_hash($claveSeguridad, PASSWORD_BCRYPT);

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO usuarios (rol_id, email, password, clave_seguridad, correo, nombre_completo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssss', $rol_id, $correo, $hash, $claveHash, $correo, $nombre);
    $stmt->execute();
    $usuario_id = $conn->insert_id;
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO reclutadores (usuario_id, empresa_id, nombre_completo, telefono) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiss', $usuario_id, $empresaId, $nombre, $telefono);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Reclutador registrado correctamente.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()]);
}

$conn->close();
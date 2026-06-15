<?php
/* Codigo leylani backend , NO TOCAR */
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$nombre   = trim($_POST['nombre']   ?? '');
$correo   = trim($_POST['correo']   ?? '');
$password = trim($_POST['password'] ?? '');
$confirm  = trim($_POST['confirmPassword'] ?? '');

if (empty($nombre) || empty($correo) || empty($password) || empty($confirm)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
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

// Insertar usuario con rol_id = 4 (Candidato)
$rol_id = 4;
$hash   = password_hash($password, PASSWORD_BCRYPT);

$conn->begin_transaction();

try {
    // Insertar en usuarios
    $stmt = $conn->prepare("INSERT INTO usuarios (rol_id, email, password, correo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('isss', $rol_id, $correo, $hash, $correo);
    $stmt->execute();
    $usuario_id = $conn->insert_id;
    $stmt->close();

    // Insertar en candidatos
    $stmt = $conn->prepare("INSERT INTO candidatos (usuario_id, nombre_completo, correo) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $usuario_id, $nombre, $correo);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Cuenta creada correctamente. Ya puedes iniciar sesión.']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()]);
}

$conn->close();
<?php
/* Codigo leylani backend , recuperacion con clave de seguridad , NO TOCAR */
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$correo         = trim($_POST['correo'] ?? '');
$claveSeguridad = trim($_POST['claveSeguridad'] ?? '');
$nuevaPassword  = trim($_POST['nuevaPassword'] ?? '');
$confirm        = trim($_POST['confirmPassword'] ?? '');

if (empty($correo) || empty($claveSeguridad) || empty($nuevaPassword) || empty($confirm)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo no es válido.']);
    exit;
}

if ($nuevaPassword !== $confirm) {
    echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
    exit;
}

if (strlen($nuevaPassword) < 6) {
    echo json_encode(['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres.']);
    exit;
}

// Verificar que el correo existe y es candidato (rol_id = 4)
$stmt = $conn->prepare("SELECT id, clave_seguridad FROM usuarios WHERE email = ? AND rol_id = 4");
$stmt->bind_param('s', $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'El correo no está registrado.']);
    $stmt->close();
    exit;
}

$stmt->bind_result($usuario_id, $claveHashGuardada);
$stmt->fetch();
$stmt->close();

// Verificar la clave de seguridad
if (!password_verify($claveSeguridad, $claveHashGuardada)) {
    echo json_encode(['success' => false, 'message' => 'La clave de seguridad es incorrecta.']);
    exit;
}

// Actualizar la contraseña
$nuevoHash = password_hash($nuevaPassword, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
$stmt->bind_param('si', $nuevoHash, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la contraseña.']);
}

$stmt->close();
$conn->close();
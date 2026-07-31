<?php
/* Codigo leylani backend */
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../config/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 4) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida. Inicia sesión nuevamente.']);
    exit;
}

$usuario_id      = $_SESSION['usuario_id'];
$nombre          = trim($_POST['nombre'] ?? '');
$correo          = trim($_POST['correo'] ?? '');
$password        = trim($_POST['password'] ?? '');
$confirmPassword = trim($_POST['confirmPassword'] ?? '');

if (empty($nombre) || empty($correo)) {
    echo json_encode(['success' => false, 'message' => 'El nombre y el correo son obligatorios.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo no válido.']);
    exit;
}

if ($password !== '' || $confirmPassword !== '') {
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener mínimo 6 caracteres.']);
        exit;
    }
    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
        exit;
    }
}

// Verificar que el correo no esté en uso por otro usuario
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE (email = ? OR correo = ?) AND id != ?");
$stmt->bind_param('ssi', $correo, $correo, $usuario_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Ese correo ya está en uso por otra cuenta.']);
    $stmt->close();
    exit;
}
$stmt->close();

if ($password !== '') {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, correo = ?, password = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $nombre, $correo, $correo, $hash, $usuario_id);
} else {
    $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, correo = ? WHERE id = ?");
    $stmt->bind_param('sssi', $nombre, $correo, $correo, $usuario_id);
}

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la cuenta.']);
    $stmt->close();
    exit;
}
$stmt->close();

// Mantener también sincronizado candidatos.nombre_completo / correo si existen
$stmt = $conn->prepare("UPDATE candidatos SET nombre_completo = ?, correo = ? WHERE usuario_id = ?");
$stmt->bind_param('ssi', $nombre, $correo, $usuario_id);
$stmt->execute();
$stmt->close();

$_SESSION['correo'] = $correo;

echo json_encode(['success' => true, 'message' => 'Cuenta actualizada correctamente.']);

$conn->close();
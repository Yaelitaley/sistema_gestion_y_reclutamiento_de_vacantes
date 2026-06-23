<?php
header('Content-Type: application/json');
require_once '../../config/config.php';
require_once '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$id      = intval($_POST['id'] ?? 0);
$nombre  = trim($_POST['nombre'] ?? '');
$correo  = trim($_POST['correo'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($id <= 0 || $nombre === '' || $correo === '') {
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo no es válido.']);
    exit;
}

// Verificar que el correo no esté usado por otro usuario
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
$stmt->bind_param('si', $correo, $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Ese correo ya lo usa otro usuario.']);
    $stmt->close();
    exit;
}
$stmt->close();

if ($password !== '') {
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
        exit;
    }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, correo = ?, password = ? WHERE id = ? AND rol_id IN (1,2)");
    $stmt->bind_param('ssssi', $nombre, $correo, $correo, $hash, $id);
} else {
    $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, correo = ? WHERE id = ? AND rol_id IN (1,2)");
    $stmt->bind_param('sssi', $nombre, $correo, $correo, $id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Administrador actualizado correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar.']);
}

$stmt->close();
$conn->close();
<?php
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

//verifica si el correo ya esta 
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

// rol_id = 2 (Administrador)
$rol_id = 2;
$hash   = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO usuarios (rol_id, email, password, correo, nombre_completo) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('issss', $rol_id, $correo, $hash, $correo, $nombre);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Administrador registrado correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar.']);
}

$stmt->close();
$conn->close();
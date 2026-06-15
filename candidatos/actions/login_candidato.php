<?php
/* Codigo leylani backend , NO TOCAR */
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$correo   = trim($_POST['correo']   ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($correo) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}


$stmt = $conn->prepare("SELECT id, password FROM usuarios WHERE email = ? AND rol_id = 4");
$stmt->bind_param('s', $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
    $stmt->close();
    exit;
}

$stmt->bind_result($id, $hash);
$stmt->fetch();
$stmt->close();

if (!password_verify($password, $hash)) {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
    exit;
}

$_SESSION['usuario_id'] = $id;
$_SESSION['rol_id']     = 4;
$_SESSION['correo']     = $correo;

echo json_encode(['success' => true, 'message' => 'Acceso correcto.']);

$conn->close();
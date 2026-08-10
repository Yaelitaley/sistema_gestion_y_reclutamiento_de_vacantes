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

// Se une con "reclutadores" para verificar el estado de la solicitud
// (Activo / Pendiente / Bloqueado / Inactivo) antes de otorgar acceso.
$stmt = $conn->prepare(
    "SELECT u.id, u.password, r.estado
     FROM usuarios u
     INNER JOIN reclutadores r ON r.usuario_id = u.id
     WHERE u.email = ? AND u.rol_id = 3"
);
$stmt->bind_param('s', $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
    $stmt->close();
    exit;
}

$stmt->bind_result($id, $hash, $estado);
$stmt->fetch();
$stmt->close();

if (!password_verify($password, $hash)) {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
    exit;
}

// Verificación de la solicitud/estado del reclutador
$estado = strtolower(trim((string) $estado));

if ($estado !== 'activo') {
    $mensajes = [
        'pendiente' => 'Tu solicitud de reclutador aún está pendiente de aprobación por un administrador.',
        'bloqueado' => 'Tu cuenta de reclutador ha sido bloqueada. Contacta al administrador.',
        'inactivo'  => 'Tu cuenta de reclutador está inactiva. Contacta al administrador.',
    ];

    echo json_encode([
        'success' => false,
        'message' => $mensajes[$estado] ?? 'Tu cuenta no tiene permisos para acceder al panel de reclutador.'
    ]);
    exit;
}

$_SESSION['usuario_id'] = $id;
$_SESSION['rol_id']     = 3;
$_SESSION['correo']     = $correo;

echo json_encode(['success' => true, 'message' => 'Acceso correcto.']);

$conn->close();
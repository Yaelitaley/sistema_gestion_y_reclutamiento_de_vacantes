<?php
header('Content-Type: application/json');
require_once '../../config/config.php';
require_once '../../config/connection.php';
require_once '../../config/app_helpers.php';

require_admin_login_json();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit;
}

// Obtenemos el usuario_id asociado para borrar también su cuenta de acceso
$stmt = $conn->prepare("SELECT usuario_id FROM reclutadores WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Reclutador no encontrado.']);
    exit;
}

$usuarioId = (int) $row['usuario_id'];

$stmt = $conn->prepare("DELETE FROM reclutadores WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    $stmt->close();

    // Elimina también la cuenta de usuario ligada, si existe y es rol reclutador (3)
    $stmt2 = $conn->prepare("DELETE FROM usuarios WHERE id = ? AND rol_id = 3");
    $stmt2->bind_param('i', $usuarioId);
    $stmt2->execute();
    $stmt2->close();

    echo json_encode(['success' => true, 'message' => 'Reclutador eliminado correctamente.']);
} else {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'No se pudo eliminar. Puede tener vacantes asociadas.']);
}

$conn->close();

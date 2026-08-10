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

$postulacion_id = intval($_POST['postulacion_id'] ?? 0);

if ($postulacion_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Postulación no válida.']);
    exit;
}

// Obtener candidato_id real a partir de la sesión
$stmt = $conn->prepare("SELECT id FROM candidatos WHERE usuario_id = ?");
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No se encontró tu perfil de candidato.']);
    $stmt->close();
    exit;
}

$stmt->bind_result($candidato_id);
$stmt->fetch();
$stmt->close();

// Verificar que la postulación pertenece al candidato en sesión
$stmt = $conn->prepare("SELECT id FROM postulaciones WHERE id = ? AND candidato_id = ?");
$stmt->bind_param('ii', $postulacion_id, $candidato_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No se encontró esa postulación o no te pertenece.']);
    $stmt->close();
    exit;
}
$stmt->close();

// Borrar primero el historial de estados (llave foránea) y luego la postulación
$stmt = $conn->prepare("DELETE FROM historial_estados_postulacion WHERE postulacion_id = ?");
$stmt->bind_param('i', $postulacion_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM postulaciones WHERE id = ? AND candidato_id = ?");
$stmt->bind_param('ii', $postulacion_id, $candidato_id);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No se encontró esa postulación o no te pertenece.']);
    $stmt->close();
    exit;
}

$stmt->close();

echo json_encode(['success' => true, 'message' => 'La postulación ha sido cancelada correctamente.']);

$conn->close();
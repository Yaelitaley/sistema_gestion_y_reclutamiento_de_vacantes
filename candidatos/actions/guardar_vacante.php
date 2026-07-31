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

$vacante_id = intval($_POST['vacante_id'] ?? 0);
$accion     = trim($_POST['accion'] ?? 'guardar'); // 'guardar' o 'quitar'

if ($vacante_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Vacante no válida.']);
    exit;
}

// Obtener candidato_id real a partir de la sesión (NUNCA confiar en un id del formulario)
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

// ----- Quitar de guardados -----
if ($accion === 'quitar') {
    $stmt = $conn->prepare("DELETE FROM vacantes_guardadas WHERE candidato_id = ? AND vacante_id = ?");
    $stmt->bind_param('ii', $candidato_id, $vacante_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Vacante eliminada de guardados.', 'guardado' => false]);
    $conn->close();
    exit;
}

// ----- Verificar que la vacante exista -----
$stmt = $conn->prepare("SELECT id FROM vacantes WHERE id = ?");
$stmt->bind_param('i', $vacante_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'La vacante no existe.']);
    $stmt->close();
    exit;
}
$stmt->close();

// ----- Evitar duplicados -----
$stmt = $conn->prepare("SELECT id FROM vacantes_guardadas WHERE candidato_id = ? AND vacante_id = ?");
$stmt->bind_param('ii', $candidato_id, $vacante_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Ya tenías esta vacante guardada.', 'guardado' => true]);
    $conn->close();
    exit;
}
$stmt->close();

// ----- Guardar -----
$stmt = $conn->prepare("INSERT INTO vacantes_guardadas (candidato_id, vacante_id) VALUES (?, ?)");
$stmt->bind_param('ii', $candidato_id, $vacante_id);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar la vacante.']);
    $stmt->close();
    exit;
}
$stmt->close();

echo json_encode(['success' => true, 'message' => 'Vacante guardada correctamente.', 'guardado' => true]);

$conn->close();
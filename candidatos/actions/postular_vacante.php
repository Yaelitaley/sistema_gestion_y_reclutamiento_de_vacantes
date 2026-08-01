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

// Verificar que la vacante exista y esté activa
$stmt = $conn->prepare("SELECT id FROM vacantes WHERE id = ? AND activa = 1");
$stmt->bind_param('i', $vacante_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'La vacante no existe o ya no está disponible.']);
    $stmt->close();
    exit;
}
$stmt->close();

// Evitar postulaciones duplicadas
$stmt = $conn->prepare("SELECT id FROM postulaciones WHERE candidato_id = ? AND vacante_id = ?");
$stmt->bind_param('ii', $candidato_id, $vacante_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Ya te has postulado a esta vacante.']);
    $stmt->close();
    exit;
}
$stmt->close();

// Insertar postulación (estado_id = 1 -> Postulado)
$stmt = $conn->prepare("INSERT INTO postulaciones (candidato_id, vacante_id, estado_id) VALUES (?, ?, 1)");
$stmt->bind_param('ii', $candidato_id, $vacante_id);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al registrar la postulación.']);
    $stmt->close();
    exit;
}

$postulacion_id = $stmt->insert_id;
$stmt->close();

// Registrar en el historial de estados
$stmt = $conn->prepare("INSERT INTO historial_estados_postulacion (postulacion_id, estado_id) VALUES (?, 1)");
$stmt->bind_param('i', $postulacion_id);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'message' => '¡Tu postulación fue enviada correctamente!']);

$conn->close();
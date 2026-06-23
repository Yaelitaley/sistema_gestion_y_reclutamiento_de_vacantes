<?php
header('Content-Type: application/json');
require_once '../../config/config.php';
require_once '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit;
}

// Solo borra si de verdad es admin (rol_id 1 o 2), por seguridad
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ? AND rol_id IN (1, 2)");
$stmt->bind_param('i', $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Administrador eliminado correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo eliminar.']);
}

$stmt->close();
$conn->close();
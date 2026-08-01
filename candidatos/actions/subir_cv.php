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

// Obtener candidato_id real a partir de la sesión
$stmt = $conn->prepare("SELECT id, cv_path FROM candidatos WHERE usuario_id = ?");
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No se encontró tu perfil de candidato.']);
    $stmt->close();
    exit;
}

$stmt->bind_result($candidato_id, $cv_path_actual);
$stmt->fetch();
$stmt->close();

if (!isset($_FILES['archivo_cv']) || $_FILES['archivo_cv']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Selecciona un archivo PDF válido.']);
    exit;
}

$archivo = $_FILES['archivo_cv'];

// Validar tipo (extensión + mime real)
$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
$finfo     = finfo_open(FILEINFO_MIME_TYPE);
$mime      = finfo_file($finfo, $archivo['tmp_name']);
finfo_close($finfo);

if ($extension !== 'pdf' || $mime !== 'application/pdf') {
    echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos PDF.']);
    exit;
}

// Validar tamaño (máximo 5 MB)
if ($archivo['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'El archivo no debe superar 5 MB.']);
    exit;
}

// Carpeta destino: /assets/uploads/cv/ (raíz del proyecto)
$carpetaDestino = __DIR__ . '/../../assets/uploads/cv/';

if (!is_dir($carpetaDestino)) {
    mkdir($carpetaDestino, 0755, true);
}

$nombreArchivo = 'candidato_' . $candidato_id . '_' . time() . '.pdf';
$rutaDestino   = $carpetaDestino . $nombreArchivo;
$rutaRelativa  = 'assets/uploads/cv/' . $nombreArchivo;

if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo en el servidor.']);
    exit;
}

// Actualizar cv_path en la base de datos
$stmt = $conn->prepare("UPDATE candidatos SET cv_path = ? WHERE id = ?");
$stmt->bind_param('si', $rutaRelativa, $candidato_id);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el registro del CV.']);
    $stmt->close();
    exit;
}
$stmt->close();

// Borrar el archivo anterior si existía y es distinto
if (!empty($cv_path_actual)) {
    $rutaAnterior = __DIR__ . '/../../' . $cv_path_actual;
    if (is_file($rutaAnterior) && $rutaAnterior !== $rutaDestino) {
        @unlink($rutaAnterior);
    }
}

echo json_encode(['success' => true, 'message' => 'Currículum cargado correctamente.', 'cv_path' => $rutaRelativa]);

$conn->close();
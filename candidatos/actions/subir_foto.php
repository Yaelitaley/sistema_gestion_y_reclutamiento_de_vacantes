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
$stmt = $conn->prepare("SELECT id, foto_perfil FROM candidatos WHERE usuario_id = ?");
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No se encontró tu perfil de candidato.']);
    $stmt->close();
    exit;
}

$stmt->bind_result($candidato_id, $foto_actual);
$stmt->fetch();
$stmt->close();

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Selecciona una imagen válida.']);
    exit;
}

$archivo = $_FILES['foto'];

// Validar tipo (extensión + mime real)
$extensionesPermitidas = ['jpg', 'jpeg', 'png'];
$mimesPermitidos       = ['image/jpeg', 'image/png'];

$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
$finfo     = finfo_open(FILEINFO_MIME_TYPE);
$mime      = finfo_file($finfo, $archivo['tmp_name']);
finfo_close($finfo);

if (!in_array($extension, $extensionesPermitidas, true) || !in_array($mime, $mimesPermitidos, true)) {
    echo json_encode(['success' => false, 'message' => 'Solo se permiten imágenes JPG, JPEG o PNG.']);
    exit;
}

// Validar tamaño (máximo 3 MB)
if ($archivo['size'] > 3 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'La imagen no debe superar 3 MB.']);
    exit;
}

// Validar que realmente sea una imagen (protección extra)
$infoImagen = @getimagesize($archivo['tmp_name']);
if ($infoImagen === false) {
    echo json_encode(['success' => false, 'message' => 'El archivo no es una imagen válida.']);
    exit;
}

// Carpeta destino: /assets/uploads/perfil/ (raíz del proyecto)
$carpetaDestino = __DIR__ . '/../../assets/uploads/perfil/';

if (!is_dir($carpetaDestino)) {
    mkdir($carpetaDestino, 0755, true);
}

$nombreArchivo = 'candidato_' . $candidato_id . '_' . time() . '.' . $extension;
$rutaDestino   = $carpetaDestino . $nombreArchivo;
$rutaRelativa  = 'assets/uploads/perfil/' . $nombreArchivo;

if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen en el servidor.']);
    exit;
}

// Actualizar foto_perfil en la base de datos
$stmt = $conn->prepare("UPDATE candidatos SET foto_perfil = ? WHERE id = ?");
$stmt->bind_param('si', $rutaRelativa, $candidato_id);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el registro de la foto.']);
    $stmt->close();
    exit;
}
$stmt->close();

// Borrar la foto anterior si existía y es distinta
if (!empty($foto_actual)) {
    $rutaAnterior = __DIR__ . '/../../' . $foto_actual;
    if (is_file($rutaAnterior) && $rutaAnterior !== $rutaDestino) {
        @unlink($rutaAnterior);
    }
}

echo json_encode(['success' => true, 'message' => 'Foto de perfil actualizada correctamente.', 'foto_perfil' => $rutaRelativa]);

$conn->close();
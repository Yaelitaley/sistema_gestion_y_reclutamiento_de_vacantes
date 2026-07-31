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

// ----- Datos del formulario -----
$nombre        = trim($_POST['nombre'] ?? '');
$apellidos     = trim($_POST['apellidos'] ?? '');
$correo        = trim($_POST['email'] ?? '');
$telefono      = trim($_POST['telefono'] ?? '');
$fecha_nac     = trim($_POST['fecha_nacimiento'] ?? '');
$nacionalidad  = trim($_POST['nacionalidad'] ?? '');
$ciudad        = trim($_POST['ciudad'] ?? '');
$habilidadesTx = trim($_POST['habilidades'] ?? '');

$genero            = trim($_POST['genero'] ?? '');
$puesto_deseado    = trim($_POST['puesto_deseado'] ?? '');
$salario_esperado  = trim($_POST['salario_esperado'] ?? '');
$disponibilidad    = trim($_POST['disponibilidad'] ?? '');
$modalidad         = trim($_POST['modalidad'] ?? '');
$linkedin          = trim($_POST['linkedin'] ?? '');
$github            = trim($_POST['github'] ?? '');
$portafolio        = trim($_POST['portafolio'] ?? '');
$resumen           = trim($_POST['resumen'] ?? '');
$objetivos         = trim($_POST['objetivos'] ?? '');

$ofertas_empleo         = isset($_POST['ofertas_empleo']) ? 1 : 0;
$notificaciones_sistema = isset($_POST['notificaciones_sistema']) ? 1 : 0;
$perfil_publico         = isset($_POST['perfil_publico']) ? 1 : 0;

$nombre_completo = trim($nombre . ' ' . $apellidos);

if (empty($nombre_completo) || empty($correo)) {
    echo json_encode(['success' => false, 'message' => 'El nombre y el correo son obligatorios.']);
    exit;
}

// Normalizar vacíos a NULL donde aplica
$fecha_nac        = $fecha_nac !== '' ? $fecha_nac : null;
$nacionalidad      = $nacionalidad !== '' ? $nacionalidad : null;
$ciudad            = $ciudad !== '' ? $ciudad : null;
$telefono          = $telefono !== '' ? $telefono : null;
$genero            = $genero !== '' ? $genero : null;
$puesto_deseado    = $puesto_deseado !== '' ? $puesto_deseado : null;
$salario_esperado  = $salario_esperado !== '' ? $salario_esperado : null;
$disponibilidad    = $disponibilidad !== '' ? $disponibilidad : null;
$modalidad         = $modalidad !== '' ? $modalidad : null;
$linkedin          = $linkedin !== '' ? $linkedin : null;
$github            = $github !== '' ? $github : null;
$portafolio        = $portafolio !== '' ? $portafolio : null;
$resumen           = $resumen !== '' ? $resumen : null;
$objetivos         = $objetivos !== '' ? $objetivos : null;

$stmt = $conn->prepare(
    "UPDATE candidatos
     SET nombre_completo = ?, correo = ?, telefono = ?, fecha_nacimiento = ?, nacionalidad = ?, ubicacion = ?,
         genero = ?, puesto_deseado = ?, salario_esperado = ?, disponibilidad = ?, modalidad = ?,
         linkedin = ?, github = ?, portafolio = ?, resumen = ?, objetivos = ?,
         ofertas_empleo = ?, notificaciones_sistema = ?, perfil_publico = ?
     WHERE id = ?"
);
$stmt->bind_param(
    'ssssssssssssssssiii',
    $nombre_completo, $correo, $telefono, $fecha_nac, $nacionalidad, $ciudad,
    $genero, $puesto_deseado, $salario_esperado, $disponibilidad, $modalidad,
    $linkedin, $github, $portafolio, $resumen, $objetivos,
    $ofertas_empleo, $notificaciones_sistema, $perfil_publico,
    $candidato_id
);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el perfil.']);
    $stmt->close();
    exit;
}
$stmt->close();

// Reemplazar habilidades (lista separada por comas)
$stmt = $conn->prepare("DELETE FROM candidato_habilidades WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$stmt->close();

if ($habilidadesTx !== '') {
    $habilidades = array_filter(array_map('trim', explode(',', $habilidadesTx)));
    $stmtHab = $conn->prepare("INSERT INTO candidato_habilidades (candidato_id, habilidad) VALUES (?, ?)");
    foreach ($habilidades as $hab) {
        if ($hab === '') continue;
        $stmtHab->bind_param('is', $candidato_id, $hab);
        $stmtHab->execute();
    }
    $stmtHab->close();
}

echo json_encode(['success' => true, 'message' => 'Los cambios del perfil se guardaron correctamente.']);

$conn->close();
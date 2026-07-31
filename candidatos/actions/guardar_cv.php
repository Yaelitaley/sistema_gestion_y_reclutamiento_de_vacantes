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

// Obtener candidato_id real y cv_path actual a partir de la sesión
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

// ----- Datos personales / textos generales -----
$nombre_completo      = trim($_POST['nombre_completo'] ?? '');
$email                = trim($_POST['email'] ?? '');
$telefono              = trim($_POST['telefono'] ?? '');
$ciudad                = trim($_POST['ciudad'] ?? '');
$perfil_profesional    = trim($_POST['perfil_profesional'] ?? '');
$objetivo_profesional  = trim($_POST['objetivo_profesional'] ?? '');
$aptitudesTx           = trim($_POST['aptitudes'] ?? '');
$disponibilidad        = trim($_POST['disponibilidad'] ?? '');
$modalidad             = trim($_POST['modalidad'] ?? '');
$linkedin              = trim($_POST['linkedin'] ?? '');
$portafolio            = trim($_POST['portafolio'] ?? '');

if (empty($nombre_completo) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'El nombre y el correo son obligatorios.']);
    exit;
}

$telefono = $telefono !== '' ? $telefono : null;
$ciudad   = $ciudad !== '' ? $ciudad : null;
$perfil_profesional   = $perfil_profesional !== '' ? $perfil_profesional : null;
$objetivo_profesional = $objetivo_profesional !== '' ? $objetivo_profesional : null;
$aptitudesTx          = $aptitudesTx !== '' ? $aptitudesTx : null;
$disponibilidad = $disponibilidad !== '' ? $disponibilidad : null;
$modalidad      = $modalidad !== '' ? $modalidad : null;
$linkedin       = $linkedin !== '' ? $linkedin : null;
$portafolio     = $portafolio !== '' ? $portafolio : null;

$stmt = $conn->prepare(
    "UPDATE candidatos
     SET nombre_completo = ?, correo = ?, telefono = ?, ubicacion = ?,
         perfil_profesional = ?, objetivo_profesional = ?, aptitudes = ?,
         disponibilidad = ?, modalidad = ?, linkedin = ?, portafolio = ?
     WHERE id = ?"
);
$stmt->bind_param(
    'sssssssssssi',
    $nombre_completo, $email, $telefono, $ciudad,
    $perfil_profesional, $objetivo_profesional, $aptitudesTx,
    $disponibilidad, $modalidad, $linkedin, $portafolio,
    $candidato_id
);
$stmt->execute();
$stmt->close();

// ----- Habilidades técnicas con nivel -----
$stmt = $conn->prepare("DELETE FROM candidato_habilidades WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$stmt->close();

$habilidadesNivel = [
    'HTML'       => trim($_POST['html_nivel'] ?? ''),
    'CSS'        => trim($_POST['css_nivel'] ?? ''),
    'Bootstrap'  => trim($_POST['bootstrap_nivel'] ?? ''),
    'JavaScript' => trim($_POST['js_nivel'] ?? ''),
    'PHP'        => trim($_POST['php_nivel'] ?? ''),
    'MySQL'      => trim($_POST['mysql_nivel'] ?? ''),
];

$stmtHab = $conn->prepare("INSERT INTO candidato_habilidades (candidato_id, habilidad, nivel) VALUES (?, ?, ?)");
foreach ($habilidadesNivel as $habilidad => $nivel) {
    if ($nivel === '') continue;
    $stmtHab->bind_param('iss', $candidato_id, $habilidad, $nivel);
    $stmtHab->execute();
}
$stmtHab->close();

// ----- Formación académica -----
$stmt = $conn->prepare("DELETE FROM candidato_formacion WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$stmt->close();

$institucion = trim($_POST['institucion'] ?? '');
$carrera     = trim($_POST['carrera'] ?? '');
$ini_form    = trim($_POST['inicio_formacion'] ?? '');
$fin_form    = trim($_POST['fin_formacion'] ?? '');

if ($institucion !== '' && $carrera !== '') {
    $stmt = $conn->prepare("INSERT INTO candidato_formacion (candidato_id, institucion, carrera, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $candidato_id, $institucion, $carrera, $ini_form, $fin_form);
    $stmt->execute();
    $stmt->close();
}

// ----- Experiencia laboral -----
$stmt = $conn->prepare("DELETE FROM candidato_experiencia WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$stmt->close();

$empresa     = trim($_POST['empresa'] ?? '');
$puesto      = trim($_POST['puesto'] ?? '');
$ini_exp     = trim($_POST['inicio_experiencia'] ?? '');
$fin_exp     = trim($_POST['fin_experiencia'] ?? '');
$desc_exp    = trim($_POST['descripcion_experiencia'] ?? '');

if ($empresa !== '' && $puesto !== '') {
    $stmt = $conn->prepare("INSERT INTO candidato_experiencia (candidato_id, empresa, puesto, fecha_inicio, fecha_fin, descripcion) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssss', $candidato_id, $empresa, $puesto, $ini_exp, $fin_exp, $desc_exp);
    $stmt->execute();
    $stmt->close();
}

// ----- Idiomas -----
$stmt = $conn->prepare("DELETE FROM candidato_idiomas WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$stmt->close();

$idiomas = [
    [trim($_POST['idioma1'] ?? ''), trim($_POST['nivel1'] ?? '')],
    [trim($_POST['idioma2'] ?? ''), trim($_POST['nivel2'] ?? '')],
];

$stmtIdi = $conn->prepare("INSERT INTO candidato_idiomas (candidato_id, idioma, nivel) VALUES (?, ?, ?)");
foreach ($idiomas as $idi) {
    if ($idi[0] === '') continue;
    $stmtIdi->bind_param('iss', $candidato_id, $idi[0], $idi[1]);
    $stmtIdi->execute();
}
$stmtIdi->close();

// ----- Certificaciones (una por línea) -----
$stmt = $conn->prepare("DELETE FROM candidato_certificaciones WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$stmt->close();

$certificacionesTx = trim($_POST['certificaciones'] ?? '');
if ($certificacionesTx !== '') {
    $lineas = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $certificacionesTx)));
    $stmtCert = $conn->prepare("INSERT INTO candidato_certificaciones (candidato_id, descripcion) VALUES (?, ?)");
    foreach ($lineas as $linea) {
        if ($linea === '') continue;
        $stmtCert->bind_param('is', $candidato_id, $linea);
        $stmtCert->execute();
    }
    $stmtCert->close();
}

// ----- Archivo del CV (opcional en este mismo formulario) -----
if (isset($_FILES['archivo_cv']) && $_FILES['archivo_cv']['error'] === UPLOAD_ERR_OK) {
    $archivo   = $_FILES['archivo_cv'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $finfo     = finfo_open(FILEINFO_MIME_TYPE);
    $mime      = finfo_file($finfo, $archivo['tmp_name']);
    finfo_close($finfo);

    if ($extension === 'pdf' && $mime === 'application/pdf' && $archivo['size'] <= 5 * 1024 * 1024) {
        $carpetaDestino = __DIR__ . '/../../assets/uploads/cv/';
        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0755, true);
        }

        $nombreArchivo = 'candidato_' . $candidato_id . '_' . time() . '.pdf';
        $rutaDestino   = $carpetaDestino . $nombreArchivo;
        $rutaRelativa  = 'assets/uploads/cv/' . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            $stmt = $conn->prepare("UPDATE candidatos SET cv_path = ? WHERE id = ?");
            $stmt->bind_param('si', $rutaRelativa, $candidato_id);
            $stmt->execute();
            $stmt->close();

            if (!empty($cv_path_actual)) {
                $rutaAnterior = __DIR__ . '/../../' . $cv_path_actual;
                if (is_file($rutaAnterior) && $rutaAnterior !== $rutaDestino) {
                    @unlink($rutaAnterior);
                }
            }
        }
    }
}

echo json_encode(['success' => true, 'message' => 'Los cambios del currículum se guardaron correctamente.']);

$conn->close();
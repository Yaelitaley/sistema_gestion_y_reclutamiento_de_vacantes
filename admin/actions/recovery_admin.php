
<?php
/* NO tocar codigo backend ley , es codigo de recuperacion de contraseñas*/
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../config/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once $_SERVER['DOCUMENT_ROOT'] . '/sistema_gestion_y_reclutamiento_de_vacantes/vendor/PHPMailer/src/Exception.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sistema_gestion_y_reclutamiento_de_vacantes/vendor/PHPMailer/src/PHPMailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sistema_gestion_y_reclutamiento_de_vacantes/vendor/PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$correo = trim($_POST['correo'] ?? '');

if (empty($correo)) {
    echo json_encode(['success' => false, 'message' => 'El correo es obligatorio.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo no es válido.']);
    exit;
}

// Verificar que el correo existe y es admin (rol_id 1 o 2)
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND rol_id IN (1, 2)");
$stmt->bind_param('s', $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'El correo no está registrado.']);
    $stmt->close();
    exit;
}

$stmt->bind_result($usuario_id);
$stmt->fetch();
$stmt->close();

// Generar token y guardarlo en sesión
$token  = bin2hex(random_bytes(32));
$expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

$_SESSION['reset_token']   = $token;
$_SESSION['reset_usuario'] = $usuario_id;
$_SESSION['reset_expira']  = $expira;

$enlace = "http://localhost/sistema_gestion_y_reclutamiento_de_vacantes/admin/reset_password.php?token=" . $token;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'd474e4b21b8f33';
    $mail->Password   = '1f1a2fea257954';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('noreply@occ-empleo.com', 'OCC Empleo Admin');
    $mail->addAddress($correo);

    $mail->isHTML(true);
    $mail->Subject = 'Recuperación de contraseña';
    $mail->Body    = '
        <div style="font-family: Poppins, sans-serif; max-width: 500px; margin: auto; padding: 30px; border-radius: 10px; border: 1px solid #ddd;">
            <h2 style="color: #0d6efd;">Recuperación de contraseña</h2>
            <p>Haz clic en el siguiente botón para restablecer tu contraseña. El enlace expira en <strong>1 hora</strong>.</p>
            <div style="text-align: center; margin: 30px 0;">
                <a href="' . $enlace . '"
                   style="background-color: #0d6efd; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: bold;">
                    Restablecer contraseña
                </a>
            </div>
            <p style="color: #888; font-size: 12px;">Si no solicitaste esto, ignora este correo.</p>
        </div>
    ';

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Correo enviado. Revisa tu bandeja.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
}

$conn->close();
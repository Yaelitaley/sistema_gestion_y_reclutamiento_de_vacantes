<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../config/app_helpers.php';

require_admin_login();

include "includes/header.php";
?>

<?php include "includes/sidebar.php"; ?>
    <div class="content">
        <?php include "includes/topbar.php"; ?>


<div class="d-flex">

    <div class="content w-100 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Candidatos</h3>
                <p class="text-muted">Administra los Candidatos registrados en el sistema.</p>
            </div>

            <a href="../candidatos/register.php" class="btn btn-reclutador">
                <i class="bi bi-plus-circle-fill me-2"></i>
                Agregar Candidato
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT c.id, c.nombre_completo, u.correo, c.ubicacion, c.estado, c.cv_path
                            FROM candidatos c
                            INNER JOIN usuarios u ON c.usuario_id = u.id
                            ORDER BY c.id DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $estado = strtolower($row['estado']);
                            if ($estado === 'activo') {
                                $badge = 'bg-success';
                            } elseif ($estado === 'pendiente') {
                                $badge = 'bg-warning text-dark';
                            } elseif ($estado === 'bloqueado') {
                                $badge = 'bg-danger';
                            } else {
                                $badge = 'bg-secondary';
                            }

                            if (!empty($row['cv_path'])) {
                                $cvUrl = '../' . htmlspecialchars($row['cv_path']);
                                $cvBotones = '
                                    <a href="' . $cvUrl . '" class="btn btn-outline-primary btn-sm" target="_blank" title="Ver CV">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="' . $cvUrl . '" class="btn btn-outline-primary btn-sm" download title="Descargar CV">
                                        <i class="bi bi-download"></i>
                                    </a>';
                            } else {
                                $cvBotones = '
                                    <button class="btn btn-outline-secondary btn-sm" disabled title="El candidato no ha subido su CV">
                                        <i class="bi bi-file-earmark-x"></i>
                                    </button>';
                            }

                            echo '<tr>';
                            echo '<td>' . $row['id'] . '</td>';
                            echo '<td>' . htmlspecialchars($row['nombre_completo']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['correo']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['ubicacion'] ?? 'Sin definir') . '</td>';
                            echo '<td><span class="badge ' . $badge . '">' . htmlspecialchars(ucfirst($estado)) . '</span></td>';
                            echo '<td>
                                    ' . $cvBotones . '
                                    <a href="../candidatos/edit_candidatos.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm btnEliminar" data-id="' . $row['id'] . '">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                  </td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6" class="text-center text-muted">No hay candidatos registrados.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="text-center mt-3">
    <a href="javascript:history.back()" class="cancel-link">Regresar</a>
</div>
</div>
<?php include "includes/footer.php"; ?>
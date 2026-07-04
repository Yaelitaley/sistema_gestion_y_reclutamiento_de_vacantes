<?php
require_once '../config/config.php';
require_once '../config/connection.php';
include "includes/header.php";
?>

<div class="d-flex">

    <?php include "includes/sidebar.php"; ?>

    <div class="content w-100 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Reclutadores</h3>
                <p class="text-muted">Administra los reclutadores registrados en el sistema.</p>
            </div>

            <a href="../reclutador/register.php" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>
                Agregar Reclutador
            </a>
        </div>

        <div class="table-box">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Empresa</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT r.id, r.nombre_completo, u.correo, e.nombre AS empresa, r.estado
                            FROM reclutadores r
                            INNER JOIN usuarios u ON r.usuario_id = u.id
                            LEFT JOIN empresas e ON r.empresa_id = e.id
                            ORDER BY r.id DESC";
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
                            echo '<tr>';
                            echo '<td>' . $row['id'] . '</td>';
                            echo '<td>' . htmlspecialchars($row['nombre_completo']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['correo']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['empresa'] ?? 'Sin empresa') . '</td>';
                            echo '<td><span class="badge ' . $badge . '">' . htmlspecialchars(ucfirst($estado)) . '</span></td>';
                            echo '<td>
                                    <a href="../reclutador/edit_reclutador.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm btnEliminar" data-id="' . $row['id'] . '">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                  </td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6" class="text-center text-muted">No hay reclutadores registrados.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="text-center mt-3">
    <a href="../admin/dashboard.php" class="cancel-link">Regresar</a>
</div>

<?php include "includes/footer.php"; ?>
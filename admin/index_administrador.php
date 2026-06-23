<?php
require_once '../config/config.php';
require_once '../config/connection.php';
include "includes/header.php";
?>

<div class="d-flex">

    <div class="content w-100 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Administradores</h3>
                <p class="text-muted">Administra los demás administradores registrados en el sistema.</p>
            </div>

            <a href="../admin/register.php" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>
                Agregar Administrador
            </a>
        </div>

        <div class="table-box">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT id, nombre_completo, correo, estado
                            FROM usuarios
                            WHERE rol_id IN (1, 2)
                            ORDER BY id DESC";
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
                            echo '<td>' . htmlspecialchars($row['nombre_completo'] ?? 'Sin nombre') . '</td>';
                            echo '<td>' . htmlspecialchars($row['correo']) . '</td>';
                            echo '<td><span class="badge ' . $badge . '">' . htmlspecialchars(ucfirst($estado)) . '</span></td>';
                            echo '<td>
                                    <a href="../admin/edit_administrador.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm btnEliminar" data-id="' . $row['id'] . '">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                  </td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center text-muted">No hay administradores registrados.</td></tr>';
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

<?php include "includes/footer.php"; ?>
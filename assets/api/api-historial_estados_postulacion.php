<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

class HistorialEstadoPostulacionController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'historial_estados_postulacion',
            'id',
            ['postulacion_id', 'estado_id'],
            ['postulacion_id', 'estado_id']
        );
    }

    protected function handleGet(): void
    {
        $id = $_GET['id'] ?? null;
        $postulacionId = $_GET['postulacion_id'] ?? null;

        if ($id !== null) {
            $this->getOne($id);
            return;
        }

        if ($postulacionId !== null) {
            try {
                $stmt = $this->db->prepare(
                    "SELECT h.*, ep.nombre AS estado_nombre
                     FROM historial_estados_postulacion h
                     INNER JOIN estados_postulacion ep ON ep.id = h.estado_id
                     WHERE h.postulacion_id = :pid
                     ORDER BY h.fecha_cambio ASC"
                );
                $stmt->bindValue(':pid', $postulacionId);
                $stmt->execute();
                Response::json(['success' => true, 'data' => $stmt->fetchAll()], 200);
            } catch (PDOException $e) {
                Response::error('Error al consultar el historial.', 500, $e->getMessage());
            }
            return;
        }

        parent::handleGet();
    }
}

$db = Database::getConnection();
(new HistorialEstadoPostulacionController($db))->handleRequest();

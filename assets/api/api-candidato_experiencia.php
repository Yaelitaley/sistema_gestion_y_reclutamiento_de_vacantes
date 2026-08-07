<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

class CandidatoExperienciaController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'candidato_experiencia',
            'id',
            ['candidato_id', 'empresa', 'puesto', 'fecha_inicio', 'fecha_fin', 'descripcion'],
            ['candidato_id', 'empresa', 'puesto']
        );
    }

    protected function handleGet(): void
    {
        $id = $_GET['id'] ?? null;
        $candidatoId = $_GET['candidato_id'] ?? null;

        if ($id !== null) {
            $this->getOne($id);
            return;
        }

        if ($candidatoId !== null) {
            try {
                $stmt = $this->db->prepare(
                    "SELECT * FROM candidato_experiencia WHERE candidato_id = :cid ORDER BY fecha_inicio DESC"
                );
                $stmt->bindValue(':cid', $candidatoId);
                $stmt->execute();
                Response::json(['success' => true, 'data' => $stmt->fetchAll()], 200);
            } catch (PDOException $e) {
                Response::error('Error al consultar la experiencia laboral.', 500, $e->getMessage());
            }
            return;
        }

        parent::handleGet();
    }
}

$db = Database::getConnection();
(new CandidatoExperienciaController($db))->handleRequest();

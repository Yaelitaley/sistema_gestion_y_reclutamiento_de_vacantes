<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

/**
 * Endpoint: /api/api-candidato_habilidades.php
 * CRUD completo sobre la tabla `candidato_habilidades`.
 *
 * Este archivo sirve como PLANTILLA para las demás tablas "hijas"
 * de candidatos: candidato_experiencia, candidato_formacion,
 * candidato_idiomas, candidato_certificaciones. Solo cambia:
 *   1) El nombre de la tabla en parent::__construct()
 *   2) El arreglo de columnas permitidas (allowedFields)
 *   3) Las columnas obligatorias (requiredFields)
 *
 * Extra:
 *   GET /api/api-candidato_habilidades.php?candidato_id=1
 *   Lista solo las habilidades de un candidato específico.
 */
class CandidatoHabilidadController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'candidato_habilidades',
            'id',
            ['candidato_id', 'habilidad', 'nivel'],
            ['candidato_id', 'habilidad']
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
                    "SELECT * FROM candidato_habilidades WHERE candidato_id = :cid ORDER BY id DESC"
                );
                $stmt->bindValue(':cid', $candidatoId);
                $stmt->execute();
                Response::json(['success' => true, 'data' => $stmt->fetchAll()], 200);
            } catch (PDOException $e) {
                Response::error('Error al consultar las habilidades.', 500, $e->getMessage());
            }
            return;
        }

        parent::handleGet();
    }
}

$db = Database::getConnection();
(new CandidatoHabilidadController($db))->handleRequest();

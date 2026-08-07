<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

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

<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

class CandidatoIdiomaController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'candidato_idiomas',
            'id',
            ['candidato_id', 'idioma', 'nivel'],
            ['candidato_id', 'idioma']
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
                    "SELECT * FROM candidato_idiomas WHERE candidato_id = :cid ORDER BY id ASC"
                );
                $stmt->bindValue(':cid', $candidatoId);
                $stmt->execute();
                Response::json(['success' => true, 'data' => $stmt->fetchAll()], 200);
            } catch (PDOException $e) {
                Response::error('Error al consultar los idiomas.', 500, $e->getMessage());
            }
            return;
        }

        parent::handleGet();
    }
}

$db = Database::getConnection();
(new CandidatoIdiomaController($db))->handleRequest();

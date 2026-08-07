<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/Auth.php';
require_once __DIR__ . '/controllers/BaseController.php';

class EntrevistaController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'entrevistas',
            'id',
            ['postulacion_id', 'fecha', 'modalidad', 'lugar', 'notas', 'estado'],
            ['postulacion_id', 'fecha']
        );
    }

    protected function handleGet(): void
    {
        $id = $_GET['id'] ?? null;

        if ($id !== null) {
            $this->getOne($id);
            return;
        }

        try {
            $where  = [];
            $params = [];

            if (isset($_GET['reclutador_id']) && $_GET['reclutador_id'] !== '') {
                $where[] = 'v.reclutador_id = :reclutador_id';
                $params[':reclutador_id'] = (int)$_GET['reclutador_id'];
            }

            if (isset($_GET['postulacion_id']) && $_GET['postulacion_id'] !== '') {
                $where[] = 'e.postulacion_id = :postulacion_id';
                $params[':postulacion_id'] = (int)$_GET['postulacion_id'];
            }

            if (isset($_GET['estado']) && $_GET['estado'] !== '') {
                $where[] = 'e.estado = :estado';
                $params[':estado'] = $_GET['estado'];
            }

            if (isset($_GET['buscar']) && trim($_GET['buscar']) !== '') {
                $where[] = '(c.nombre_completo LIKE :buscar OR v.trabajo LIKE :buscar2)';
                $like = '%' . trim($_GET['buscar']) . '%';
                $params[':buscar']  = $like;
                $params[':buscar2'] = $like;
            }

            if (isset($_GET['proximas']) && $_GET['proximas'] === '1') {
                $where[] = "e.estado = 'Programada' AND e.fecha >= NOW()";
            }

            $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
            $limit = min(200, max(1, (int)($_GET['limit'] ?? 100)));

            $sql = "SELECT e.*, c.nombre_completo AS candidato_nombre, v.trabajo AS vacante_trabajo
                    FROM entrevistas e
                    INNER JOIN postulaciones p ON e.postulacion_id = p.id
                    INNER JOIN vacantes v ON p.vacante_id = v.id
                    INNER JOIN candidatos c ON p.candidato_id = c.id
                    $whereSql
                    ORDER BY e.fecha ASC
                    LIMIT $limit";

            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            Response::json(['success' => true, 'data' => $stmt->fetchAll()], 200);
        } catch (PDOException $e) {
            Response::error('Error al consultar las entrevistas.', 500, $e->getMessage());
        }
    }

    private function assertOwnership(int $entrevistaId): void
    {
        if (!Auth::isReclutador()) {
            return;
        }

        $reclutadorId = Auth::currentReclutadorId($this->db);
        $stmt = $this->db->prepare(
            'SELECT v.reclutador_id FROM entrevistas e
             INNER JOIN postulaciones p ON e.postulacion_id = p.id
             INNER JOIN vacantes v ON p.vacante_id = v.id
             WHERE e.id = :id LIMIT 1'
        );
        $stmt->bindValue(':id', $entrevistaId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row || (int)$row['reclutador_id'] !== (int)$reclutadorId) {
            Response::error('No tienes permiso para modificar esta entrevista.', 403);
        }
    }

    protected function beforeCreate(array $data): array
    {
        Auth::requireRole([1, 2, 3]);

        if (Auth::isReclutador()) {
            $reclutadorId = Auth::currentReclutadorId($this->db);
            $stmt = $this->db->prepare(
                'SELECT v.reclutador_id FROM postulaciones p
                 INNER JOIN vacantes v ON p.vacante_id = v.id
                 WHERE p.id = :pid LIMIT 1'
            );
            $stmt->bindValue(':pid', $data['postulacion_id'] ?? 0);
            $stmt->execute();
            $row = $stmt->fetch();
            if (!$row || (int)$row['reclutador_id'] !== (int)$reclutadorId) {
                Response::error('No tienes permiso para agendar una entrevista sobre esta postulación.', 403);
            }
        }

        if (!isset($data['estado'])) {
            $data['estado'] = 'Programada';
        }

        return $data;
    }

    protected function beforeUpdate(array $data, $id): array
    {
        Auth::requireRole([1, 2, 3]);
        $this->assertOwnership((int)$id);
        return $data;
    }

    protected function handleDelete(): void
    {
        Auth::requireRole([1, 2, 3]);
        $id = $this->requireExistingId('eliminar');
        $this->assertOwnership((int)$id);

        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            Response::json(['success' => true, 'message' => 'Entrevista eliminada correctamente.'], 200);
        } catch (PDOException $e) {
            Response::error('Error al eliminar la entrevista.', 500, $e->getMessage());
        }
    }
}

$db = Database::getConnection();
(new EntrevistaController($db))->handleRequest();

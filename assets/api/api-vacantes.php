<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/Auth.php';
require_once __DIR__ . '/controllers/BaseController.php';

class VacanteController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'vacantes',
            'id',
            [
                'reclutador_id', 'trabajo', 'descripcion', 'categoria', 'requisitos',
                'salario', 'ubicacion', 'nivel_experiencia', 'activa', 'modalidad',
            ],
            ['reclutador_id', 'trabajo', 'descripcion', 'categoria', 'requisitos', 'ubicacion', 'nivel_experiencia']
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
            $page   = max(1, (int)($_GET['page'] ?? 1));
            $limit  = min(200, max(1, (int)($_GET['limit'] ?? 50)));
            $offset = ($page - 1) * $limit;

            $where  = [];
            $params = [];

            if (isset($_GET['categoria']) && $_GET['categoria'] !== '') {
                $where[] = 'v.categoria = :categoria';
                $params[':categoria'] = $_GET['categoria'];
            }

            if (isset($_GET['activa']) && $_GET['activa'] !== '') {
                $where[] = 'v.activa = :activa';
                $params[':activa'] = (int)$_GET['activa'];
            }

            if (isset($_GET['reclutador_id']) && $_GET['reclutador_id'] !== '') {
                $where[] = 'v.reclutador_id = :reclutador_id';
                $params[':reclutador_id'] = (int)$_GET['reclutador_id'];
            }

            if (isset($_GET['modalidad']) && $_GET['modalidad'] !== '') {
                $where[] = 'v.modalidad = :modalidad';
                $params[':modalidad'] = $_GET['modalidad'];
            }

            if (isset($_GET['ubicacion']) && $_GET['ubicacion'] !== '') {
                $where[] = 'v.ubicacion = :ubicacion';
                $params[':ubicacion'] = $_GET['ubicacion'];
            }

            if (isset($_GET['nivel_experiencia']) && $_GET['nivel_experiencia'] !== '') {
                $where[] = 'v.nivel_experiencia = :nivel_experiencia';
                $params[':nivel_experiencia'] = $_GET['nivel_experiencia'];
            }

            if (isset($_GET['buscar']) && trim($_GET['buscar']) !== '') {
                $where[] = '(v.trabajo LIKE :buscar OR v.descripcion LIKE :buscar2 OR v.ubicacion LIKE :buscar3 OR e.nombre LIKE :buscar4)';
                $like = '%' . trim($_GET['buscar']) . '%';
                $params[':buscar']  = $like;
                $params[':buscar2'] = $like;
                $params[':buscar3'] = $like;
                $params[':buscar4'] = $like;
            }

            if (isset($_GET['excluir_id']) && $_GET['excluir_id'] !== '') {
                $where[] = 'v.id != :excluir_id';
                $params[':excluir_id'] = (int)$_GET['excluir_id'];
            }

            $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $sql = "SELECT v.*, r.nombre_completo AS reclutador_nombre,
                           e.nombre AS empresa_nombre, e.logo_path AS empresa_logo,
                           (SELECT COUNT(*) FROM postulaciones p WHERE p.vacante_id = v.id) AS total_postulaciones
                    FROM vacantes v
                    LEFT JOIN reclutadores r ON r.id = v.reclutador_id
                    LEFT JOIN empresas e ON e.id = r.empresa_id
                    $whereSql
                    ORDER BY v.id DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();

            $countSql = "SELECT COUNT(*) FROM vacantes v LEFT JOIN reclutadores r ON r.id = v.reclutador_id LEFT JOIN empresas e ON e.id = r.empresa_id $whereSql";
            $countStmt = $this->db->prepare($countSql);
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $total = (int)$countStmt->fetchColumn();

            Response::json([
                'success' => true,
                'data'    => $rows,
                'meta'    => [
                    'total'       => $total,
                    'page'        => $page,
                    'limit'       => $limit,
                    'total_pages' => (int)ceil($total / max(1, $limit)),
                ],
            ], 200);
        } catch (PDOException $e) {
            Response::error('Error al consultar las vacantes.', 500, $e->getMessage());
        }
    }

   
    protected function beforeCreate(array $data): array
    {
        Auth::requireRole([1, 2, 3]);

        if (Auth::isReclutador()) {
            $reclutadorId = Auth::currentReclutadorId($this->db);
            if ($reclutadorId === null) {
                Response::error('No se encontró el perfil de reclutador asociado a tu cuenta.', 403);
            }
            $data['reclutador_id'] = $reclutadorId;
        }

        return $data;
    }

    protected function beforeUpdate(array $data, $id): array
    {
        Auth::requireRole([1, 2, 3]);
        $this->assertOwnership((int)$id);

        if (Auth::isReclutador()) {
            unset($data['reclutador_id']);
        }

        return $data;
    }

    protected function handleDelete(): void
    {
        Auth::requireRole([1, 2, 3]);
        $id = $this->requireExistingId('eliminar');
        $this->assertOwnership((int)$id);

        try {
            $this->db->prepare('DELETE FROM postulaciones WHERE vacante_id = :id')
                ->execute([':id' => $id]);

            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            Response::json(['success' => true, 'message' => 'Vacante eliminada correctamente.'], 200);
        } catch (PDOException $e) {
            Response::error('Error al eliminar la vacante.', 500, $e->getMessage());
        }
    }

    private function assertOwnership(int $vacanteId): void
    {
        if (!Auth::isReclutador()) {
            return; 
        }

        $reclutadorId = Auth::currentReclutadorId($this->db);
        $stmt = $this->db->prepare('SELECT reclutador_id FROM vacantes WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $vacanteId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row || (int)$row['reclutador_id'] !== (int)$reclutadorId) {
            Response::error('No tienes permiso para modificar esta vacante.', 403);
        }
    }
}

$db = Database::getConnection();
(new VacanteController($db))->handleRequest();

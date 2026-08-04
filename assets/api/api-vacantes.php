<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

/**
 * Endpoint: /api/api-vacantes.php
 * CRUD completo sobre la tabla `vacantes`.
 *
 * Extra:
 *   GET /api/api-vacantes.php            -> lista SOLO vacantes activas por defecto.
 *   GET /api/api-vacantes.php?activa=0   -> incluye también las inactivas.
 *   GET /api/api-vacantes.php?categoria=Tecnología -> filtra por categoría.
 */
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
            $limit  = min(100, max(1, (int)($_GET['limit'] ?? 50)));
            $offset = ($page - 1) * $limit;

            $where  = [];
            $params = [];

            if (isset($_GET['categoria'])) {
                $where[] = 'v.categoria = :categoria';
                $params[':categoria'] = $_GET['categoria'];
            }

            if (isset($_GET['activa'])) {
                $where[] = 'v.activa = :activa';
                $params[':activa'] = (int)$_GET['activa'];
            }

            $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $sql = "SELECT v.*, r.nombre_completo AS reclutador_nombre, e.nombre AS empresa_nombre
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

            $countSql = "SELECT COUNT(*) FROM vacantes v $whereSql";
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
}

$db = Database::getConnection();
(new VacanteController($db))->handleRequest();

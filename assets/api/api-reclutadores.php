<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/Auth.php';
require_once __DIR__ . '/controllers/BaseController.php';

class ReclutadorController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'reclutadores',
            'id',
            ['usuario_id', 'empresa_id', 'nombre_completo', 'telefono', 'estado'],
            ['usuario_id', 'empresa_id', 'nombre_completo']
        );
    }

    protected function handleDelete(): void
    {
        // Solo un administrador puede eliminar reclutadores desde el panel.
        Auth::requireRole([1, 2]);

        $id = $this->requireExistingId('eliminar');

        $stmt = $this->db->prepare('SELECT usuario_id FROM reclutadores WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row) {
            Response::error('Reclutador no encontrado.', 404);
        }

        try {
            $this->db->prepare('DELETE FROM reclutadores WHERE id = :id')->execute([':id' => $id]);
            // También elimina la cuenta de acceso ligada, si sigue siendo de rol reclutador (3).
            $this->db->prepare('DELETE FROM usuarios WHERE id = :uid AND rol_id = 3')->execute([':uid' => $row['usuario_id']]);

            Response::json(['success' => true, 'message' => 'Reclutador eliminado correctamente.'], 200);
        } catch (PDOException $e) {
            Response::error('No se pudo eliminar. Puede tener vacantes asociadas.', 500, $e->getMessage());
        }
    }

    protected function handleGet(): void
    {
        $id = $_GET['id'] ?? null;

        if ($id !== null) {
            $this->getOne($id);
            return;
        }

        if (isset($_GET['usuario_id']) && $_GET['usuario_id'] !== '') {
            try {
                $stmt = $this->db->prepare('SELECT * FROM reclutadores WHERE usuario_id = :uid LIMIT 1');
                $stmt->bindValue(':uid', (int)$_GET['usuario_id'], PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch();

                Response::json([
                    'success' => true,
                    'data'    => $row ? $this->sanitizeRow($row) : null,
                ], 200);
            } catch (PDOException $e) {
                Response::error('Error al consultar el reclutador.', 500, $e->getMessage());
            }
            return;
        }

        $this->listado();
    }

    protected function getOne($id): void
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT r.*, u.correo, u.created_at AS usuario_created_at, e.nombre AS empresa_nombre
                 FROM reclutadores r
                 LEFT JOIN usuarios u ON u.id = r.usuario_id
                 LEFT JOIN empresas e ON e.id = r.empresa_id
                 WHERE r.id = :id LIMIT 1"
            );
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch();

            if (!$row) {
                Response::error('Reclutador no encontrado.', 404);
            }

            Response::json(['success' => true, 'data' => $this->sanitizeRow($row)], 200);
        } catch (PDOException $e) {
            Response::error('Error al consultar el reclutador.', 500, $e->getMessage());
        }
    }

    protected function listado(): void
    {
        try {
            $where  = [];
            $params = [];

            if (isset($_GET['estado']) && $_GET['estado'] !== '') {
                $where[] = 'r.estado = :estado';
                $params[':estado'] = $_GET['estado'];
            }

            if (isset($_GET['buscar']) && trim($_GET['buscar']) !== '') {
                $where[] = '(r.nombre_completo LIKE :buscar OR u.correo LIKE :buscar2)';
                $like = '%' . trim($_GET['buscar']) . '%';
                $params[':buscar']  = $like;
                $params[':buscar2'] = $like;
            }

            $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
            $limit = min(200, max(1, (int)($_GET['limit'] ?? 100)));

            $sql = "SELECT r.*, u.correo, e.nombre AS empresa_nombre
                    FROM reclutadores r
                    LEFT JOIN usuarios u ON u.id = r.usuario_id
                    LEFT JOIN empresas e ON e.id = r.empresa_id
                    $whereSql
                    ORDER BY r.id DESC
                    LIMIT $limit";

            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            Response::json(['success' => true, 'data' => $stmt->fetchAll()], 200);
        } catch (PDOException $e) {
            Response::error('Error al consultar los reclutadores.', 500, $e->getMessage());
        }
    }
}

$db = Database::getConnection();
(new ReclutadorController($db))->handleRequest();

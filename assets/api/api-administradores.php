<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/Auth.php';
require_once __DIR__ . '/controllers/BaseController.php';

class AdministradorController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'administradores',
            'id',
            ['usuario_id', 'nombre_completo', 'correo', 'password', 'empresa', 'estado'],
            ['usuario_id', 'nombre_completo', 'correo']
        );
    }

    protected function validate(array $data, string $mode): void
    {
        if (isset($data['correo']) && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            Response::error('El campo correo no tiene un formato válido.', 400);
        }
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

            if (isset($_GET['buscar']) && trim($_GET['buscar']) !== '') {
                $where[] = '(a.nombre_completo LIKE :buscar OR a.correo LIKE :buscar2 OR a.empresa LIKE :buscar3)';
                $like = '%' . trim($_GET['buscar']) . '%';
                $params[':buscar']  = $like;
                $params[':buscar2'] = $like;
                $params[':buscar3'] = $like;
            }

            if (isset($_GET['estado']) && $_GET['estado'] !== '') {
                $where[] = 'a.estado = :estado';
                $params[':estado'] = $_GET['estado'];
            }

            $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $sql = "SELECT a.id, a.usuario_id, a.nombre_completo, a.correo, a.empresa, a.estado, u.rol_id
                    FROM administradores a
                    INNER JOIN usuarios u ON u.id = a.usuario_id
                    $whereSql
                    ORDER BY a.id DESC";

            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            Response::json(['success' => true, 'data' => $stmt->fetchAll()], 200);
        } catch (PDOException $e) {
            Response::error('Error al consultar los administradores.', 500, $e->getMessage());
        }
    }

    protected function beforeCreate(array $data): array
    {
        Auth::requireRole([1, 2]);
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    protected function beforeUpdate(array $data, $id): array
    {
        Auth::requireRole([1, 2]);
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    protected function handleDelete(): void
    {
        Auth::requireRole([1, 2]);
        $id = $this->requireExistingId('eliminar');

        $stmt = $this->db->prepare('SELECT usuario_id FROM administradores WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row) {
            Response::error('Administrador no encontrado.', 404);
        }

        // Un administrador no puede eliminar su propia cuenta.
        if ((int)$row['usuario_id'] === (int)Auth::usuarioId()) {
            Response::error('No puedes eliminar tu propio usuario.', 400);
        }

        try {
            $this->db->beginTransaction();
            $this->db->prepare('DELETE FROM administradores WHERE id = :id')->execute([':id' => $id]);
            $this->db->prepare('DELETE FROM usuarios WHERE id = :uid')->execute([':uid' => $row['usuario_id']]);
            $this->db->commit();

            Response::json(['success' => true, 'message' => 'Administrador eliminado correctamente.'], 200);
        } catch (PDOException $e) {
            $this->db->rollBack();
            Response::error('No se pudo eliminar el administrador.', 500, $e->getMessage());
        }
    }

    protected function sanitizeRow(array $row): array
    {
        unset($row['password']);
        return $row;
    }
}

$db = Database::getConnection();
(new AdministradorController($db))->handleRequest();

<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

/**
 * Endpoint: /api/api-administradores.php
 * CRUD completo sobre la tabla `administradores`.
 */
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

    protected function beforeCreate(array $data): array
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    protected function beforeUpdate(array $data, $id): array
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    protected function sanitizeRow(array $row): array
    {
        unset($row['password']);
        return $row;
    }
}

$db = Database::getConnection();
(new AdministradorController($db))->handleRequest();

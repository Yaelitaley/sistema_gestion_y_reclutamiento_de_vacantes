<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

/**
 * Endpoint: /api/api-usuarios.php
 *
 * GET    /api/api-usuarios.php           -> lista todos (paginado: ?page=1&limit=20)
 * GET    /api/api-usuarios.php?id=1      -> obtiene un usuario
 * POST   /api/api-usuarios.php           -> crea un usuario (body JSON)
 * PUT    /api/api-usuarios.php?id=1      -> actualiza un usuario (body JSON)
 * DELETE /api/api-usuarios.php?id=1      -> elimina un usuario
 */
class UsuarioController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'usuarios',
            'id',
            ['rol_id', 'email', 'nombre_completo', 'password', 'clave_seguridad', 'correo', 'estado'],
            ['rol_id', 'email', 'password', 'correo']
        );
    }

    protected function validate(array $data, string $mode): void
    {
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Response::error('El campo email no tiene un formato válido.', 400);
        }
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

    // Nunca devolver el hash de la contraseña ni la clave de seguridad en las respuestas.
    protected function sanitizeRow(array $row): array
    {
        unset($row['password'], $row['clave_seguridad']);
        return $row;
    }
}

$db = Database::getConnection();
(new UsuarioController($db))->handleRequest();

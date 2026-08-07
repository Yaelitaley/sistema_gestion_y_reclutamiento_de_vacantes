<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/Auth.php';
require_once __DIR__ . '/controllers/BaseController.php';

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
        parent::handleDelete();
    }

    // Nunca devolver el hash de la contraseña ni la clave de seguridad 
    protected function sanitizeRow(array $row): array
    {
        unset($row['password'], $row['clave_seguridad']);
        return $row;
    }
}

$db = Database::getConnection();
(new UsuarioController($db))->handleRequest();

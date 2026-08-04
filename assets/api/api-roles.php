<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

/**
 * Endpoint: /api/api-roles.php
 * CRUD completo sobre la tabla `roles`.
 */
class RolController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'roles',
            'id',
            ['nombre'],
            ['nombre']
        );
    }
}

$db = Database::getConnection();
(new RolController($db))->handleRequest();

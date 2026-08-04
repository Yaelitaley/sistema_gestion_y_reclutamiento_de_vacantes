<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

/**
 * Endpoint: /api/api-empresas.php
 * CRUD completo sobre la tabla `empresas`.
 */
class EmpresaController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'empresas',
            'id',
            ['nombre', 'logo_path'],
            ['nombre']
        );
    }
}

$db = Database::getConnection();
(new EmpresaController($db))->handleRequest();

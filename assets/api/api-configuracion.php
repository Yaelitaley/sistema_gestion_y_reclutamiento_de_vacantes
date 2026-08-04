<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

/**
 * Endpoint: /api/api-configuracion.php
 * CRUD completo sobre la tabla `configuracion` (clave/valor).
 */
class ConfiguracionController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'configuracion',
            'id',
            ['clave', 'valor'],
            ['clave']
        );
    }
}

$db = Database::getConnection();
(new ConfiguracionController($db))->handleRequest();

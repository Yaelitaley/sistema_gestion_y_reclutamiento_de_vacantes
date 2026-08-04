<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

/**
 * Endpoint: /api/api-entrevistas.php
 * CRUD completo sobre la tabla `entrevistas`.
 */
class EntrevistaController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'entrevistas',
            'id',
            ['postulacion_id', 'fecha', 'modalidad', 'lugar', 'notas', 'estado'],
            ['postulacion_id', 'fecha']
        );
    }
}

$db = Database::getConnection();
(new EntrevistaController($db))->handleRequest();

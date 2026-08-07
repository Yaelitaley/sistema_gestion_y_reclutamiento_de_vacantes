<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
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
}

$db = Database::getConnection();
(new ReclutadorController($db))->handleRequest();

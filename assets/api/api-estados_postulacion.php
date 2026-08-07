<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';


class EstadoPostulacionController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'estados_postulacion',
            'id',
            ['nombre'],
            ['nombre']
        );
    }
}

$db = Database::getConnection();
(new EstadoPostulacionController($db))->handleRequest();

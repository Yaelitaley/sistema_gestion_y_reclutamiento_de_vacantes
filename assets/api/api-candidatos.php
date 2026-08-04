<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

/**
 * Endpoint: /api/api-candidatos.php
 * CRUD completo sobre la tabla `candidatos`.
 *
 * Extra:
 *   GET /api/api-candidatos.php?id=1&full=1
 *   Devuelve el candidato junto con sus habilidades, experiencia,
 *   formación e idiomas (tablas relacionadas).
 */
class CandidatoController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'candidatos',
            'id',
            [
                'usuario_id', 'nombre_completo', 'correo', 'telefono', 'cv_path',
                'fecha_nacimiento', 'nacionalidad', 'ubicacion', 'estado', 'genero',
                'puesto_deseado', 'salario_esperado', 'disponibilidad', 'modalidad',
                'linkedin', 'github', 'portafolio', 'resumen', 'objetivos',
                'ofertas_empleo', 'notificaciones_sistema', 'perfil_publico',
                'perfil_profesional', 'objetivo_profesional', 'aptitudes',
            ],
            ['usuario_id', 'nombre_completo', 'correo', 'cv_path']
        );
    }

    protected function validate(array $data, string $mode): void
    {
        if (isset($data['correo']) && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            Response::error('El campo correo no tiene un formato válido.', 400);
        }
    }

    protected function getOne($id): void
    {
        // Si el cliente pide ?full=1, devolvemos el candidato con sus relaciones.
        if (isset($_GET['full']) && $_GET['full'] == '1') {
            $stmt = $this->db->prepare("SELECT * FROM candidatos WHERE id = :id LIMIT 1");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $candidato = $stmt->fetch();

            if (!$candidato) {
                Response::error('Candidato no encontrado.', 404);
            }

            $habilidades = $this->db->prepare("SELECT id, habilidad, nivel FROM candidato_habilidades WHERE candidato_id = :id");
            $habilidades->bindValue(':id', $id);
            $habilidades->execute();

            $experiencia = $this->db->prepare("SELECT id, empresa, puesto, fecha_inicio, fecha_fin, descripcion FROM candidato_experiencia WHERE candidato_id = :id");
            $experiencia->bindValue(':id', $id);
            $experiencia->execute();

            $formacion = $this->db->prepare("SELECT id, institucion, carrera, fecha_inicio, fecha_fin FROM candidato_formacion WHERE candidato_id = :id");
            $formacion->bindValue(':id', $id);
            $formacion->execute();

            $idiomas = $this->db->prepare("SELECT id, idioma, nivel FROM candidato_idiomas WHERE candidato_id = :id");
            $idiomas->bindValue(':id', $id);
            $idiomas->execute();

            $candidato['habilidades']  = $habilidades->fetchAll();
            $candidato['experiencia']  = $experiencia->fetchAll();
            $candidato['formacion']    = $formacion->fetchAll();
            $candidato['idiomas']      = $idiomas->fetchAll();

            Response::json(['success' => true, 'data' => $candidato], 200);
            return;
        }

        parent::getOne($id);
    }
}

$db = Database::getConnection();
(new CandidatoController($db))->handleRequest();

<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/Auth.php';
require_once __DIR__ . '/controllers/BaseController.php';

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

    protected function handleGet(): void
    {
        $id = $_GET['id'] ?? null;

        if ($id !== null) {
            $this->getOne($id);
            return;
        }

     
        if (isset($_GET['usuario_id']) && $_GET['usuario_id'] !== '') {
            try {
                $stmt = $this->db->prepare('SELECT * FROM candidatos WHERE usuario_id = :uid LIMIT 1');
                $stmt->bindValue(':uid', (int)$_GET['usuario_id'], PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch();

                Response::json([
                    'success' => true,
                    'data'    => $row ? $this->sanitizeRow($row) : null,
                ], 200);
            } catch (PDOException $e) {
                Response::error('Error al consultar el candidato.', 500, $e->getMessage());
            }
            return;
        }

        parent::handleGet();
    }

    protected function getOne($id): void
    {

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

    protected function handleDelete(): void
    {
      
        Auth::requireRole([1, 2]);

        $id = $this->requireExistingId('eliminar');

        $stmt = $this->db->prepare('SELECT usuario_id FROM candidatos WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row) {
            Response::error('Candidato no encontrado.', 404);
        }

        try {
            $this->db->prepare('DELETE FROM candidatos WHERE id = :id')->execute([':id' => $id]);
            $this->db->prepare('DELETE FROM usuarios WHERE id = :uid AND rol_id = 4')->execute([':uid' => $row['usuario_id']]);

            Response::json(['success' => true, 'message' => 'Candidato eliminado correctamente.'], 200);
        } catch (PDOException $e) {
            Response::error('No se pudo eliminar. Puede tener postulaciones asociadas.', 500, $e->getMessage());
        }
    }
}

$db = Database::getConnection();
(new CandidatoController($db))->handleRequest();

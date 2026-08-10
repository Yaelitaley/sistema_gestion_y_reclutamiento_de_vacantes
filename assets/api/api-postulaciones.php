<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/Auth.php';
require_once __DIR__ . '/controllers/BaseController.php';

class PostulacionController extends BaseController
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'postulaciones',
            'id',
            ['candidato_id', 'vacante_id', 'estado_id'],
            ['candidato_id', 'vacante_id', 'estado_id']
        );
    }

    protected function handleGet(): void
    {
        $id = $_GET['id'] ?? null;

        if ($id !== null) {
            $this->getOne($id);
            return;
        }

        try {
            $page   = max(1, (int)($_GET['page'] ?? 1));
            $limit  = min(200, max(1, (int)($_GET['limit'] ?? 50)));
            $offset = ($page - 1) * $limit;

            $where  = [];
            $params = [];

            if (isset($_GET['candidato_id']) && $_GET['candidato_id'] !== '') {
                $where[] = 'p.candidato_id = :candidato_id';
                $params[':candidato_id'] = (int)$_GET['candidato_id'];
            }

            if (isset($_GET['vacante_id']) && $_GET['vacante_id'] !== '') {
                $where[] = 'p.vacante_id = :vacante_id';
                $params[':vacante_id'] = (int)$_GET['vacante_id'];
            }

            if (isset($_GET['reclutador_id']) && $_GET['reclutador_id'] !== '') {
                $where[] = 'v.reclutador_id = :reclutador_id';
                $params[':reclutador_id'] = (int)$_GET['reclutador_id'];
            }

            if (isset($_GET['estado_id']) && $_GET['estado_id'] !== '') {
                $where[] = 'p.estado_id = :estado_id';
                $params[':estado_id'] = (int)$_GET['estado_id'];
            }

            $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $sql = "SELECT p.*, v.trabajo, v.ubicacion, v.salario, v.modalidad,
                           e.nombre AS empresa_nombre, ep.nombre AS estado_nombre,
                           c.nombre_completo AS candidato_nombre, c.puesto_deseado AS candidato_puesto_deseado,
                           c.correo AS candidato_correo, c.telefono AS candidato_telefono
                    FROM postulaciones p
                    LEFT JOIN vacantes v ON v.id = p.vacante_id
                    LEFT JOIN reclutadores r ON r.id = v.reclutador_id
                    LEFT JOIN empresas e ON e.id = r.empresa_id
                    LEFT JOIN estados_postulacion ep ON ep.id = p.estado_id
                    LEFT JOIN candidatos c ON c.id = p.candidato_id
                    $whereSql
                    ORDER BY p.id DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();

            $countSql = "SELECT COUNT(*) FROM postulaciones p LEFT JOIN vacantes v ON v.id = p.vacante_id $whereSql";
            $countStmt = $this->db->prepare($countSql);
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $total = (int)$countStmt->fetchColumn();

            Response::json([
                'success' => true,
                'data'    => $rows,
                'meta'    => [
                    'total'       => $total,
                    'page'        => $page,
                    'limit'       => $limit,
                    'total_pages' => (int)ceil($total / max(1, $limit)),
                ],
            ], 200);
        } catch (PDOException $e) {
            Response::error('Error al consultar las postulaciones.', 500, $e->getMessage());
        }
    }

    protected function beforeCreate(array $data): array
    {
        // Solo un candidato autenticado puede postularse, y siempre a nombre
        Auth::requireRole([4]);
        $candidatoId = Auth::currentCandidatoId($this->db);
        if ($candidatoId === null) {
            Response::error('No se encontró tu perfil de candidato.', 403);
        }
        $data['candidato_id'] = $candidatoId;

        if (!isset($data['estado_id'])) {
            $data['estado_id'] = 1; // 1 = 'Postulado' por defecto
        }

        // Validar que la vacante exista y esté activa.
        $stmt = $this->db->prepare('SELECT id FROM vacantes WHERE id = :id AND activa = 1 LIMIT 1');
        $stmt->bindValue(':id', $data['vacante_id'] ?? 0);
        $stmt->execute();
        if (!$stmt->fetch()) {
            Response::error('La vacante no existe o ya no está disponible.', 404);
        }

        // Evitar postulaciones duplicadas.
        $stmt = $this->db->prepare('SELECT id FROM postulaciones WHERE candidato_id = :cid AND vacante_id = :vid LIMIT 1');
        $stmt->bindValue(':cid', $candidatoId);
        $stmt->bindValue(':vid', $data['vacante_id'] ?? 0);
        $stmt->execute();
        if ($stmt->fetch()) {
            Response::error('Ya te has postulado a esta vacante.', 409);
        }

        return $data;
    }

    protected function handlePost(): void
    {
        $input = $this->getJsonInput();

        foreach ($this->requiredFields as $field) {
            
            if ($field === 'candidato_id' || $field === 'estado_id') {
                continue;
            }
            if (!array_key_exists($field, $input) || $input[$field] === '') {
                Response::error("El campo '{$field}' es obligatorio.", 400);
            }
        }

        $data = $this->filterFields($input);
        $data = $this->beforeCreate($data);

        try {
            $this->db->beginTransaction();

            $columns      = array_keys($data);
            $placeholders = array_map(fn($c) => ':' . $c, $columns);
            $sql = "INSERT INTO postulaciones (" . implode(', ', $columns) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->db->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();
            $newId = $this->db->lastInsertId();

            $histStmt = $this->db->prepare(
                "INSERT INTO historial_estados_postulacion (postulacion_id, estado_id) VALUES (:pid, :eid)"
            );
            $histStmt->bindValue(':pid', $newId);
            $histStmt->bindValue(':eid', $data['estado_id']);
            $histStmt->execute();

            $this->db->commit();

            $stmt = $this->db->prepare("SELECT * FROM postulaciones WHERE id = :id");
            $stmt->bindValue(':id', $newId);
            $stmt->execute();

            Response::json([
                'success' => true,
                'message' => 'Postulación creada correctamente.',
                'data'    => $stmt->fetch(),
            ], 201);
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->handlePdoWriteError($e);
        }
    }

    
    protected function beforeUpdate(array $data, $id): array
    {
        // Solo el reclutador dueño de la vacante o un admin pueden cambiar el estado.
        Auth::requireRole([1, 2, 3]);

        if (Auth::isReclutador()) {
            $reclutadorId = Auth::currentReclutadorId($this->db);
            $stmt = $this->db->prepare(
                'SELECT v.reclutador_id FROM postulaciones p
                 INNER JOIN vacantes v ON v.id = p.vacante_id
                 WHERE p.id = :id LIMIT 1'
            );
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch();
            if (!$row || (int)$row['reclutador_id'] !== (int)$reclutadorId) {
                Response::error('No tienes permiso para modificar esta postulación.', 403);
            }
        }

        if (isset($data['estado_id'])) {
            $stmt = $this->db->prepare(
                "INSERT INTO historial_estados_postulacion (postulacion_id, estado_id) VALUES (:pid, :eid)"
            );
            $stmt->bindValue(':pid', $id);
            $stmt->bindValue(':eid', $data['estado_id']);
            $stmt->execute();
        }
        return $data;
    }

    protected function handleDelete(): void
    {
        Auth::requireLogin();
        $id = $this->requireExistingId('eliminar');

        // Un candidato solo puede cancelar sus propias postulaciones.
        if (Auth::isCandidato()) {
            $candidatoId = Auth::currentCandidatoId($this->db);
            $stmt = $this->db->prepare('SELECT candidato_id FROM postulaciones WHERE id = :id LIMIT 1');
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch();
            if (!$row || (int)$row['candidato_id'] !== (int)$candidatoId) {
                Response::error('No tienes permiso para cancelar esta postulación.', 403);
            }
        } elseif (!Auth::isAdmin() && !Auth::isReclutador()) {
            Response::error('No tienes permisos para realizar esta acción.', 403);
        }

        try {
            $this->db->prepare('DELETE FROM historial_estados_postulacion WHERE postulacion_id = :id')
                ->execute([':id' => $id]);

            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            Response::json(['success' => true, 'message' => 'Postulación eliminada correctamente.'], 200);
        } catch (PDOException $e) {
            Response::error('Error al eliminar la postulación.', 500, $e->getMessage());
        }
    }
}

$db = Database::getConnection();
(new PostulacionController($db))->handleRequest();

<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BaseController.php';

/**
 * Endpoint: /api/api-postulaciones.php
 * CRUD completo sobre la tabla `postulaciones`.
 *
 * Al crear una postulación (POST), automáticamente se registra
 * también en `historial_estados_postulacion` (misma lógica que
 * usa el sistema para el timeline de una postulación).
 */
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

    protected function beforeCreate(array $data): array
    {
        if (!isset($data['estado_id'])) {
            $data['estado_id'] = 1; // 1 = 'Postulado' por defecto
        }
        return $data;
    }

    protected function handlePost(): void
    {
        $input = $this->getJsonInput();

        foreach ($this->requiredFields as $field) {
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

    // Al actualizar el estado_id, también se agrega una entrada al historial.
    protected function beforeUpdate(array $data, $id): array
    {
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
}

$db = Database::getConnection();
(new PostulacionController($db))->handleRequest();

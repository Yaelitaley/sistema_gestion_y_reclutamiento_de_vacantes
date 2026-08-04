<?php
require_once __DIR__ . '/../config/Response.php';

/**
 * BaseController
 * ----------------
 * Controlador genérico que implementa un CRUD completo y seguro sobre
 * cualquier tabla de la base de datos usando PDO y prepared statements.
 *
 * Cada endpoint concreto (UsuarioController, VacanteController, etc.)
 * extiende esta clase indicando: la tabla, la llave primaria, los
 * campos permitidos para insertar/actualizar y los campos obligatorios.
 *
 * Verbos soportados:
 *  - GET    -> listar (con paginación) o obtener uno (?id=)
 *  - POST   -> crear un registro nuevo
 *  - PUT    -> reemplazo COMPLETO de un registro (?id=), exige los
 *              mismos campos obligatorios que POST
 *  - PATCH  -> actualización PARCIAL de un registro (?id=), solo
 *              modifica los campos que se envíen
 *  - DELETE -> eliminar un registro (?id=)
 *
 * Métodos que las clases hijas pueden sobrescribir (hooks):
 *  - beforeCreate(array $data): array   -> transformar datos antes de INSERT
 *  - beforeUpdate(array $data, $id): array -> transformar datos antes de UPDATE
 *  - sanitizeRow(array $row): array     -> limpiar/ocultar campos en la respuesta (ej. password)
 *  - validate(array $data, string $mode): void -> validaciones extra (lanzar Response::error)
 */
class BaseController
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey;
    protected array $allowedFields;   // Columnas que se pueden insertar/actualizar
    protected array $requiredFields;  // Columnas obligatorias al crear (POST)

    public function __construct(
        PDO $db,
        string $table,
        string $primaryKey,
        array $allowedFields,
        array $requiredFields = []
    ) {
        $this->db             = $db;
        $this->table           = $table;
        $this->primaryKey      = $primaryKey;
        $this->allowedFields   = $allowedFields;
        $this->requiredFields  = $requiredFields;
    }

    /**
     * Punto de entrada: enruta la petición según el método HTTP.
     */
    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->handleGet();
                break;
            case 'POST':
                $this->handlePost();
                break;
            case 'PUT':
                $this->handlePut();
                break;
            case 'PATCH':
                $this->handlePatch();
                break;
            case 'DELETE':
                $this->handleDelete();
                break;
            default:
                Response::error('Método HTTP no permitido.', 405);
        }
    }

    /* ------------------------------------------------------------------ */
    /*  GET  ->  Listar todos (con paginación y búsqueda opcional)         */
    /*         o obtener uno solo si se envía ?id=                        */
    /* ------------------------------------------------------------------ */
    protected function handleGet(): void
    {
        $id = $_GET['id'] ?? null;

        if ($id !== null) {
            $this->getOne($id);
            return;
        }

        try {
            $page   = max(1, (int)($_GET['page'] ?? 1));
            $limit  = min(100, max(1, (int)($_GET['limit'] ?? 50)));
            $offset = ($page - 1) * $limit;

            $sql  = "SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();

            $rows = array_map([$this, 'sanitizeRow'], $rows);

            $total = (int)$this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();

            Response::json([
                'success' => true,
                'data'    => $rows,
                'meta'    => [
                    'total'       => $total,
                    'page'        => $page,
                    'limit'       => $limit,
                    'total_pages' => (int)ceil($total / $limit),
                ],
            ], 200);
        } catch (PDOException $e) {
            Response::error('Error al consultar los registros.', 500, $e->getMessage());
        }
    }

    protected function getOne($id): void
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch();

            if (!$row) {
                Response::error('Registro no encontrado.', 404);
            }

            Response::json(['success' => true, 'data' => $this->sanitizeRow($row)], 200);
        } catch (PDOException $e) {
            Response::error('Error al consultar el registro.', 500, $e->getMessage());
        }
    }

    /* ------------------------------------------------------------------ */
    /*  POST  ->  Crear un nuevo registro                                  */
    /* ------------------------------------------------------------------ */
    protected function handlePost(): void
    {
        $input = $this->getJsonInput();

        foreach ($this->requiredFields as $field) {
            if (!array_key_exists($field, $input) || $input[$field] === '' || $input[$field] === null) {
                Response::error("El campo '{$field}' es obligatorio.", 400);
            }
        }

        $data = $this->filterFields($input);

        if (empty($data)) {
            Response::error('No se enviaron campos válidos para crear el registro.', 400);
        }

        $this->validate($data, 'create');
        $data = $this->beforeCreate($data);

        try {
            $columns      = array_keys($data);
            $placeholders = array_map(fn($c) => ':' . $c, $columns);

            $sql  = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                     VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->db->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->execute();
            $newId = $this->db->lastInsertId();

            $this->getOneAsCreated($newId);
        } catch (PDOException $e) {
            $this->handlePdoWriteError($e);
        }
    }

    private function getOneAsCreated($id): void
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();

        Response::json([
            'success' => true,
            'message' => 'Registro creado correctamente.',
            'data'    => $row ? $this->sanitizeRow($row) : null,
        ], 201);
    }

    /* ------------------------------------------------------------------ */
    /*  PUT  ->  Reemplazo COMPLETO de un registro existente (?id=)        */
    /*         Debe enviarse el mismo conjunto de campos obligatorios      */
    /*         que en un POST (representación completa del recurso).      */
    /* ------------------------------------------------------------------ */
    protected function handlePut(): void
    {
        $id = $this->requireExistingId('actualizar');

        $input = $this->getJsonInput();

        foreach ($this->requiredFields as $field) {
            if (!array_key_exists($field, $input) || $input[$field] === '' || $input[$field] === null) {
                Response::error("El campo '{$field}' es obligatorio en un reemplazo completo (PUT).", 400);
            }
        }

        $data = $this->filterFields($input);

        if (empty($data)) {
            Response::error('No se enviaron campos válidos para actualizar.', 400);
        }

        $this->performUpdate($id, $data);
    }

    /* ------------------------------------------------------------------ */
    /*  PATCH  ->  Actualización PARCIAL de un registro existente (?id=)   */
    /*           Solo se exigen y modifican los campos enviados.           */
    /* ------------------------------------------------------------------ */
    protected function handlePatch(): void
    {
        $id = $this->requireExistingId('actualizar');

        $input = $this->getJsonInput();
        $data  = $this->filterFields($input);

        if (empty($data)) {
            Response::error('No se enviaron campos válidos para actualizar.', 400);
        }

        $this->performUpdate($id, $data);
    }

    /** Verifica que venga ?id= y que el registro exista; lo retorna. */
    protected function requireExistingId(string $accion)
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            Response::error("Debe indicar el id del registro a {$accion} (ej. ?id=1).", 400);
        }

        $stmt = $this->db->prepare("SELECT {$this->primaryKey} FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        if (!$stmt->fetch()) {
            Response::error('Registro no encontrado.', 404);
        }

        return $id;
    }

    /** Lógica compartida de UPDATE usada tanto por PUT como por PATCH. */
    protected function performUpdate($id, array $data): void
    {
        $this->validate($data, 'update');
        $data = $this->beforeUpdate($data, $id);

        try {
            $setParts = array_map(fn($c) => "{$c} = :{$c}", array_keys($data));
            $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $this->getOne($id);
        } catch (PDOException $e) {
            $this->handlePdoWriteError($e);
        }
    }

    /* ------------------------------------------------------------------ */
    /*  DELETE  ->  Eliminar un registro (?id=)                            */
    /* ------------------------------------------------------------------ */
    protected function handleDelete(): void
    {
        $id = $this->requireExistingId('eliminar');

        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            Response::json(['success' => true, 'message' => 'Registro eliminado correctamente.'], 200);
        } catch (PDOException $e) {
            // Código 23000 = violación de restricción de integridad (llave foránea)
            if ($e->getCode() === '23000') {
                Response::error(
                    'No se puede eliminar: existen registros relacionados que dependen de este.',
                    409,
                    $e->getMessage()
                );
            }
            Response::error('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Utilidades internas                                                */
    /* ------------------------------------------------------------------ */

    /** Lee y decodifica el cuerpo JSON de la petición (php://input). */
    protected function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');

        if (empty($raw)) {
            Response::error('El cuerpo de la petición está vacío. Se esperaba un JSON.', 400);
        }

        $input = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
            Response::error('El JSON enviado no es válido: ' . json_last_error_msg(), 400);
        }

        return $input;
    }

    /** Filtra el array de entrada dejando solo las columnas permitidas (whitelist). */
    protected function filterFields(array $input): array
    {
        return array_intersect_key($input, array_flip($this->allowedFields));
    }

    /** Maneja errores de escritura comunes (duplicados, FK inexistente, etc.) */
    protected function handlePdoWriteError(PDOException $e): void
    {
        if ($e->getCode() === '23000') {
            Response::error(
                'Conflicto de integridad: dato duplicado o referencia inexistente (llave foránea).',
                409,
                $e->getMessage()
            );
        }
        Response::error('Error al guardar el registro.', 500, $e->getMessage());
    }

    /** Hook: transformar datos antes de un INSERT. Sobrescribir si es necesario. */
    protected function beforeCreate(array $data): array
    {
        return $data;
    }

    /** Hook: transformar datos antes de un UPDATE. Sobrescribir si es necesario. */
    protected function beforeUpdate(array $data, $id): array
    {
        return $data;
    }

    /** Hook: limpiar/ocultar campos sensibles antes de responder (ej. password). */
    protected function sanitizeRow(array $row): array
    {
        return $row;
    }

    /** Hook: validaciones adicionales de negocio. Lanza Response::error si falla. */
    protected function validate(array $data, string $mode): void
    {
        // Sin validaciones extra por defecto.
    }
}

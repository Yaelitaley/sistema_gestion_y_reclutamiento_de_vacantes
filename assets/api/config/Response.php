<?php
/**
 * Clase auxiliar para enviar respuestas JSON estandarizadas
 * junto con el código de estado HTTP correspondiente.
 */
class Response
{
    /**
     * Envía una respuesta JSON y termina la ejecución del script.
     *
     * @param mixed $data   Datos a enviar (array asociativo normalmente)
     * @param int   $status Código de estado HTTP
     */
    public static function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function success($data = null, string $message = 'OK', int $status = 200): void
    {
        $payload = ['success' => true, 'message' => $message];
        if ($data !== null) {
            $payload['data'] = $data;
        }
        self::json($payload, $status);
    }

    public static function error(string $message, int $status = 400, $extra = null): void
    {
        $payload = ['success' => false, 'message' => $message];
        if ($extra !== null) {
            $payload['error'] = $extra;
        }
        self::json($payload, $status);
    }
}

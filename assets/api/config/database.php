<?php
require_once __DIR__ . '/Response.php';

/**
 * Clase de conexión a la base de datos usando PDO.
 * Implementa un patrón singleton para reutilizar la misma
 * conexión durante toda la ejecución de la petición.
 */
class Database
{
    // --- Ajusta estos valores según tu entorno local (XAMPP/Laragon/etc.) ---
    private static string $host    = 'localhost';
    private static string $dbname  = 'reclutamiento_vacantes';
    private static string $user    = 'root';
    private static string $pass    = '';
    private static string $charset = 'utf8mb4';
    private static int    $port    = 3306;
    // --------------------------------------------------------------------

    private static ?PDO $instance = null;

    private function __construct()
    {
        // Constructor privado: no se permite instanciar directamente.
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                self::$host,
                self::$port,
                self::$dbname,
                self::$charset
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // Usa prepared statements reales (más seguro)
            ];

            try {
                self::$instance = new PDO($dsn, self::$user, self::$pass, $options);
            } catch (PDOException $e) {
                // Si la conexión falla, respondemos con un JSON de error 500
                // en lugar de dejar que PHP muestre un error crudo en HTML.
                Response::error(
                    'No fue posible conectar a la base de datos.',
                    500,
                    $e->getMessage()
                );
            }
        }

        return self::$instance;
    }
}

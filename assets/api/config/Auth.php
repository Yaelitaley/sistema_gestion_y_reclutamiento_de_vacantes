<?php
/**
 * Helper de autorización para los endpoints de la API.
 * Se apoya en la sesión PHP que ya usan admin/reclutador/candidatos
 * (ver config/config.php y config/app_helpers.php del proyecto).
 *
 * Roles: 1 = Super Usuario, 2 = Administrador, 3 = Reclutador, 4 = Candidato
 */
class Auth
{
    public static function rolId(): ?int
    {
        return isset($_SESSION['rol_id']) ? (int) $_SESSION['rol_id'] : null;
    }

    public static function usuarioId(): ?int
    {
        return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
    }

    public static function isAdmin(): bool
    {
        return in_array(self::rolId(), [1, 2], true);
    }

    public static function isReclutador(): bool
    {
        return self::rolId() === 3;
    }

    public static function isCandidato(): bool
    {
        return self::rolId() === 4;
    }

    /** Corta la ejecución con 401 si no hay sesión activa. */
    public static function requireLogin(): void
    {
        if (self::usuarioId() === null || self::rolId() === null) {
            Response::error('Sesión no válida. Inicia sesión nuevamente.', 401);
        }
    }

    /** Corta la ejecución con 403 si el rol actual no está permitido. */
    public static function requireRole(array $rolesPermitidos): void
    {
        self::requireLogin();
        if (!in_array(self::rolId(), $rolesPermitidos, true)) {
            Response::error('No tienes permisos para realizar esta acción.', 403);
        }
    }

    /** Obtiene el id de reclutador ligado al usuario en sesión (o null). */
    public static function currentReclutadorId(PDO $db): ?int
    {
        $uid = self::usuarioId();
        if ($uid === null) {
            return null;
        }
        $stmt = $db->prepare('SELECT id FROM reclutadores WHERE usuario_id = :uid LIMIT 1');
        $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? (int) $row['id'] : null;
    }

    /** Obtiene el id de candidato ligado al usuario en sesión (o null). */
    public static function currentCandidatoId(PDO $db): ?int
    {
        $uid = self::usuarioId();
        if ($uid === null) {
            return null;
        }
        $stmt = $db->prepare('SELECT id FROM candidatos WHERE usuario_id = :uid LIMIT 1');
        $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? (int) $row['id'] : null;
    }
}

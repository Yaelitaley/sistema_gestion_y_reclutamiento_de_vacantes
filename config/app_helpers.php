<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect_to')) {
    function redirect_to($url) {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('badge_estado')) {
    function badge_estado($estado) {
        $estado = trim((string) $estado);
        $clases = [
            'Activo' => 'bg-success',
            'Activa' => 'bg-success',
            'Inactivo' => 'bg-secondary',
            'Inactiva' => 'bg-secondary',
            'Pendiente' => 'bg-warning text-dark',
            'Bloqueado' => 'bg-danger',
            'Postulado' => 'bg-primary',
            'En revisión' => 'bg-info text-dark',
            'Entrevista' => 'bg-warning text-dark',
            'Contratado' => 'bg-success',
            'Rechazado' => 'bg-danger'
        ];

        $clase = $clases[$estado] ?? 'bg-secondary';
        return '<span class="badge ' . $clase . '">' . e($estado ?: 'Sin estado') . '</span>';
    }
}

if (!function_exists('table_exists')) {
    function table_exists($conn, $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        return $result && $result->num_rows > 0;
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('s', $table);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}

if (!function_exists('admin_required_tables_ok')) {
    function admin_required_tables_ok($conn, $tables) {
        foreach ($tables as $table) {
            if (!table_exists($conn, $table)) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('get_config_value')) {
    function get_config_value($conn, $clave, $default = '') {
        if (!table_exists($conn, 'configuracion')) {
            return $default;
        }
        $stmt = $conn->prepare("SELECT valor FROM configuracion WHERE clave = ? LIMIT 1");
        $stmt->bind_param('s', $clave);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $row ? $row['valor'] : $default;
    }
}

if (!function_exists('set_config_value')) {
    function set_config_value($conn, $clave, $valor) {
        $stmt = $conn->prepare("INSERT INTO configuracion (clave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
        $stmt->bind_param('ss', $clave, $valor);
        $stmt->execute();
        $stmt->close();
    }
}


if (!function_exists('texto_corto')) {
    function texto_corto($texto, $limite = 65) {
        $texto = trim((string) $texto);
        if (function_exists('mb_strimwidth')) {
            return mb_strimwidth($texto, 0, $limite, '...', 'UTF-8');
        }
        return strlen($texto) > $limite ? substr($texto, 0, $limite - 3) . '...' : $texto;
    }
}

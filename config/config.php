<?php

define('BASE_URL', '/OCC_EMPLEO');
define('ADMIN_URL', BASE_URL . '/admin');


if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 60 * 60 * 4, 
        'path'     => '/',
        'domain'   => '',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.gc_maxlifetime', 60 * 60 * 4);

    session_start();
}
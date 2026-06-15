<?php

define('BASE_URL', '/OCC_EMPLEO');
define('ADMIN_URL', BASE_URL . '/admin');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
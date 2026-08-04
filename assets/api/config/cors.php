<?php
/**
 * Configuración de CORS (Cross-Origin Resource Sharing).
 * Se incluye al inicio de CADA endpoint para permitir que
 * el frontend (u otras herramientas como Postman) consuman la API.
 */

// Origen permitido. En producción, cambia "*" por tu dominio real,
// por ejemplo: header("Access-Control-Allow-Origin: https://tudominio.com");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 86400");
header("Content-Type: application/json; charset=UTF-8");

// Las peticiones "preflight" (OPTIONS) que el navegador envía antes de
// un POST/PUT/DELETE con JSON deben responder 200 sin cuerpo.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

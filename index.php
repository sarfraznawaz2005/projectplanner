<?php
require_once __DIR__ . '/config.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Create necessary directories
foreach (config('paths') as $path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

// Simple routing
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string and decode URL
$path = parse_url($request, PHP_URL_PATH);
$path = urldecode($path);

// Remove trailing slash
$path = rtrim($path, '/');

// Route handling
try {
    switch ($path) {
        case '':
        case '/':
            require __DIR__ . '/views/index.php';
            break;

        case '/api/generate-prd':
            if ($method === 'POST') {
                require __DIR__ . '/api/generate-prd.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case '/api/generate-plan':
            if ($method === 'POST') {
                require __DIR__ . '/api/generate-plan.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case '/api/generate-sdd':
            if ($method === 'POST') {
                require __DIR__ . '/api/generate-sdd.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case '/api/generate-ai-instructions':
            if ($method === 'POST') {
                require __DIR__ . '/api/generate-ai-instructions.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case '/api/generate-phases':
            if ($method === 'POST') {
                require __DIR__ . '/api/generate-phases.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

         case '/api/generate-diagram':
             if ($method === 'POST') {
                 require __DIR__ . '/api/generate-diagram.php';
             } else {
                 http_response_code(405);
                 echo json_encode(['error' => 'Method not allowed']);
             }
             break;



         case '/download':
            if ($method === 'POST') {
                require __DIR__ . '/api/download.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        default:
            http_response_code(404);
            require __DIR__ . '/views/404.php';
            break;
    }
} catch (Exception $e) {
    if (config('app.debug')) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Internal Server Error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error']);
    }
}
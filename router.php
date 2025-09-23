<?php
// Simple router for PHP built-in server
// This handles static files and routes everything else to index.php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Handle static files
$staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'woff', 'woff2', 'ttf'];
$pathInfo = pathinfo($path);
$extension = $pathInfo['extension'] ?? '';

if (in_array($extension, $staticExtensions)) {
    $filePath = __DIR__ . $path;
    if (file_exists($filePath)) {
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf'
        ];

        header('Content-Type: ' . ($mimeTypes[$extension] ?? 'application/octet-stream'));
        header('Cache-Control: public, max-age=3600');
        readfile($filePath);
        exit;
    }
}

// For all other requests, use the main routing logic
require_once __DIR__ . '/index.php';
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

// Increase timeout for long-running AI operations
ini_set('max_execution_time', 300); // 5 minutes
ini_set('default_socket_timeout', 300); // 5 minutes

use ProjectPlanner\ProjectPlannerAgent;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['name']) || !isset($input['idea'])) {
        throw new Exception('Invalid input data');
    }

    $projectName = trim($input['name']);
    $projectIdea = trim($input['idea']);

    if (empty($projectName) || empty($projectIdea)) {
        throw new Exception('Project name and idea are required');
    }

    // Basic sanitization
    $projectName = strip_tags($projectName);
    $projectIdea = strip_tags($projectIdea);

    // Generate PRD using Neuron AI
    $agent = new ProjectPlannerAgent();
    $prdContent = $agent->generatePRD($projectName, $projectIdea);

    echo json_encode([
        'success' => true,
        'content' => $prdContent
    ]);

} catch (Exception $e) {
    error_log('PRD Generation Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (Error $e) {
    error_log('PRD Generation Error (PHP Error): ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An unexpected error occurred. The input might be too large for the AI provider to process.'
    ]);
}
<?php
require_once __DIR__ . '/../vendor/autoload.php';

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
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    // Debug logging
    error_log('Raw input: ' . $rawInput);
    error_log('Decoded input: ' . print_r($input, true));
    error_log('JSON error: ' . json_last_error_msg());

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }

    if (!$input || !isset($input['name']) || !isset($input['idea'])) {
        throw new Exception('Invalid input data: missing name or idea');
    }

    $projectName = trim($input['name']);
    $projectIdea = trim($input['idea']);

    if (empty($projectName) || empty($projectIdea)) {
        throw new Exception('Project name and idea are required');
    }

    // Basic sanitization
    $projectName = strip_tags($projectName);
    $projectIdea = strip_tags($projectIdea);

    // Generate diagram using Neuron AI
    $agent = new ProjectPlannerAgent();
    $diagramPng = $agent->generateDiagram($projectName, $projectIdea);

    if ($diagramPng === null) {
        throw new Exception('Failed to generate diagram');
    }

    echo json_encode([
        'success' => true,
        'diagram' => base64_encode($diagramPng)
    ]);

} catch (Exception $e) {
    error_log('Diagram Generation Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (Error $e) {
    error_log('Diagram Generation Error (PHP Error): ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An unexpected error occurred'
    ]);
}
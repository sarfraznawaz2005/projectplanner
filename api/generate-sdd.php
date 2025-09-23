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
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['name']) || !isset($input['prd'])) {
        throw new Exception('Invalid input data');
    }

    $projectName = trim($input['name']);
    $prdContent = trim($input['prd']);

    if (empty($projectName) || empty($prdContent)) {
        throw new Exception('Project name and PRD content are required');
    }

    // Input validation and sanitization
    if (strlen($projectName) > 100) {
        throw new Exception('Project name must be less than 100 characters');
    }
    if (strlen($prdContent) > 200000) { // Allow larger content for PRD
        throw new Exception('PRD content must be less than 200,000 characters');
    }

    // Basic sanitization
    $projectName = strip_tags($projectName);


    // Generate system design document using Neuron AI
    $agent = new ProjectPlannerAgent();
    $sddContent = $agent->generateSDD($projectName, $prdContent);

    echo json_encode([
        'success' => true,
        'content' => $sddContent
    ]);

} catch (Exception $e) {
    error_log('SDD Generation Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (Error $e) {
    error_log('SDD Generation Error (PHP Error): ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An unexpected error occurred. The input might be too large for the AI provider to process.'
    ]);
}
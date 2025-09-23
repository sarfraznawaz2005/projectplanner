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

    if (!$input || !isset($input['name']) || !isset($input['sdd'])) {
        throw new Exception('Invalid input data');
    }

    $projectName = trim($input['name']);
    $sddContent = trim($input['sdd']);

    if (empty($projectName) || empty($sddContent)) {
        throw new Exception('Project name and SDD content are required');
    }

    // Input validation and sanitization
    if (strlen($projectName) > 100) {
        throw new Exception('Project name must be less than 100 characters');
    }
    if (strlen($sddContent) > 200000) {
        throw new Exception('SDD content must be less than 200,000 characters');
    }

    // Basic sanitization
    $projectName = strip_tags($projectName);

    // Generate development plan using Neuron AI
    $agent = new ProjectPlannerAgent();
    $planContent = $agent->generatePlan($projectName, $sddContent);

    echo json_encode([
        'success' => true,
        'content' => $planContent
    ]);

} catch (Exception $e) {
    error_log('Plan Generation Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (Error $e) {
    error_log('Plan Generation Error (PHP Error): ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An unexpected error occurred. The input might be too large for the AI provider to process.'
    ]);
}
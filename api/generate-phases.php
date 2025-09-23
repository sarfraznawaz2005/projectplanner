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

    if (!$input || !isset($input['plan'])) {
        throw new Exception('Invalid input data');
    }

    $planContent = trim($input['plan']);

    if (empty($planContent)) {
        throw new Exception('Development plan is required');
    }

    // Input validation
    if (strlen($planContent) > 200000) {
        throw new Exception('Plan content must be less than 200,000 characters');
    }

    // Generate phase documents using Neuron AI
    $agent = new ProjectPlannerAgent();
    $phasesContent = $agent->generatePhases($planContent);

    echo json_encode([
        'success' => true,
        'content' => $phasesContent
    ]);

} catch (Exception $e) {
    error_log('Phases Generation Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (Error $e) {
    error_log('Phases Generation Error (PHP Error): ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An unexpected error occurred. The input might be too large for the AI provider to process.'
    ]);
}
<?php
require_once __DIR__ . '/../vendor/autoload.php';

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

    if (!$input || !isset($input['name'])) {
        throw new Exception('Invalid input data');
    }

    $projectName = trim($input['name']);

    if (empty($projectName)) {
        throw new Exception('Project name is required');
    }

    // Input validation and sanitization
    if (strlen($projectName) > 100) {
        throw new Exception('Project name must be less than 100 characters');
    }

    // Basic sanitization
    $projectName = strip_tags($projectName);

    // Generate AI instruction document dynamically (no AI call needed)
    $agent = new ProjectPlannerAgent();
    $aiInstructionsContent = $agent->generateAIInstructions($projectName);

    echo json_encode([
        'success' => true,
        'content' => $aiInstructionsContent
    ]);

} catch (Exception $e) {
    error_log('AI Instructions Generation Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (Error $e) {
    error_log('AI Instructions Generation Error (PHP Error): ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An unexpected error occurred'
    ]);
}
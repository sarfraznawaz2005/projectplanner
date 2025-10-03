<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $projectName = trim($_POST['name'] ?? '');
    $projectIdea = trim($_POST['idea'] ?? '');
    $prdContent = trim($_POST['prd'] ?? '');
    $sddContent = trim($_POST['sdd'] ?? '');
    $diagramData = trim($_POST['systemArchDiagram'] ?? '');
    $planContent = trim($_POST['plan'] ?? '');
    $phasesContent = trim($_POST['phases'] ?? '');
    $aiInstructionsContent = trim($_POST['aiInstructions'] ?? '');

    if (empty($projectName)) {
        throw new Exception('Project name is required');
    }

    // Basic sanitization
    $projectName = strip_tags($projectName);
    $projectIdea = strip_tags($projectIdea);

    // Create project directory
    $safeName = sanitizeFilename($projectName);
    $projectDir = config('paths.projects') . '/' . $safeName;

    if (!is_dir($projectDir)) {
        mkdir($projectDir, 0755, true);
    }

    // Save PRD
    if (!empty($prdContent)) {
        file_put_contents($projectDir . '/prd.md', $prdContent);
    }

    // Save SDD
    if (!empty($sddContent)) {
        file_put_contents($projectDir . '/sdd.md', $sddContent);
    }

    // Save System Architecture Diagram
    if (!empty($diagramData)) {
        $diagramBinary = base64_decode($diagramData);
        if ($diagramBinary !== false) {
            file_put_contents($projectDir . '/system_architecture.png', $diagramBinary);
        }
    }

    // Save Development Plan
    if (!empty($planContent)) {
        file_put_contents($projectDir . '/plan.md', $planContent);
    }

    // Save Phase Documents
    if (!empty($phasesContent)) {
        file_put_contents($projectDir . '/phases.md', $phasesContent);

        // Split into individual phase files
        splitPhaseDocuments($projectDir, $phasesContent);
    }

    // Save AI Instructions Document
    if (!empty($aiInstructionsContent)) {
        file_put_contents($projectDir . '/AGENTS.md', $aiInstructionsContent);
    }

    // Create ZIP file
    $zipFile = $projectDir . '.zip';
    createZip($projectDir, $zipFile);

    // Serve the ZIP file
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $safeName . '_project_docs.zip"');
    header('Content-Length: ' . filesize($zipFile));

    readfile($zipFile);

    // Clean up
    cleanup($projectDir, $zipFile);

} catch (Exception $e) {
    error_log('Download Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (Error $e) {
    error_log('Download Error (PHP Error): ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An unexpected error occurred'
    ]);
}

function sanitizeFilename($filename) {
    $filename = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $filename);
    $filename = preg_replace('/_+/', '_', $filename);
    $filename = trim($filename, '_');
    return $filename;
}

function splitPhaseDocuments($projectDir, $phasesContent) {
    $phases = preg_split('/^## Phase \d+/m', $phasesContent, -1, PREG_SPLIT_NO_EMPTY);

    if (count($phases) > 1) {
        array_shift($phases); // Remove content before first phase
    }

    foreach ($phases as $index => $phaseContent) {
        $phaseNumber = $index + 1;
        $phaseTitle = "Phase {$phaseNumber}";

        // Extract phase title if it exists
        if (preg_match('/^## (.+)$/m', $phaseContent, $matches)) {
            $phaseTitle = trim($matches[1]);
        }

        $safeTitle = sanitizeFilename($phaseTitle);
        $fileName = $projectDir . '/' . $safeTitle . '.md';

        file_put_contents($fileName, "## {$phaseTitle}\n\n" . trim($phaseContent));
    }
}

function createZip($sourceDir, $zipFile) {
    $zip = new ZipArchive();

    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new Exception('Cannot create ZIP file');
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceDir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($sourceDir) + 1);

            $zip->addFile($filePath, $relativePath);
        }
    }

    $zip->close();
}

function cleanup($projectDir, $zipFile) {
    // Remove project directory after ZIP creation
    removeDirectory($projectDir);

    // Remove ZIP file after serving (optional, could be kept for caching)
    if (file_exists($zipFile)) {
        unlink($zipFile);
    }
}

function removeDirectory($dir) {
    if (!is_dir($dir)) {
        return;
    }

    $files = array_diff(scandir($dir), ['.', '..']);

    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            unlink($path);
        }
    }

    rmdir($dir);
}
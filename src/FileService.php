<?php

namespace ProjectPlanner;

class FileService
{
    private string $projectsDir;

    public function __construct()
    {
        $this->projectsDir = config('paths.projects');
    }

    /**
     * Create project directory structure
     */
    public function createProjectDirectory(string $projectName): string
    {
        $safeName = $this->sanitizeFilename($projectName);
        $projectDir = $this->projectsDir . '/' . $safeName;

        if (!is_dir($projectDir)) {
            mkdir($projectDir, 0755, true);
        }

        return $projectDir;
    }

    /**
     * Save document files to project directory
     */
    public function saveProjectDocuments(string $projectDir, array $documents): void
    {
        $fileMappings = [
            'prd' => 'prd.md',
            'sdd' => 'sdd.md',
            'plan' => 'plan.md',
            'phases' => 'phases.md',
            'aiInstructions' => 'AGENTS.md'
        ];

        foreach ($fileMappings as $key => $filename) {
            if (!empty($documents[$key])) {
                file_put_contents($projectDir . '/' . $filename, $documents[$key]);
            }
        }

        // Handle diagram separately
        if (!empty($documents['systemArchDiagram'])) {
            $diagramBinary = base64_decode($documents['systemArchDiagram']);
            if ($diagramBinary !== false) {
                file_put_contents($projectDir . '/system_architecture.png', $diagramBinary);
            }
        }

        // Split phases into individual files
        if (!empty($documents['phases'])) {
            $this->splitPhaseDocuments($projectDir, $documents['phases']);
        }
    }

    /**
     * Create ZIP archive of project documents
     */
    public function createProjectZip(string $projectDir, string $projectName): string
    {
        $safeName = $this->sanitizeFilename($projectName);
        $zipFile = $projectDir . '.zip';

        $zip = new \ZipArchive();

        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Cannot create ZIP file');
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($projectDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($projectDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        return $zipFile;
    }

    /**
     * Clean up temporary project files
     */
    public function cleanupProjectFiles(string $projectDir, string $zipFile): void
    {
        // Remove project directory
        $this->removeDirectory($projectDir);

        // Remove ZIP file after serving
        if (file_exists($zipFile)) {
            unlink($zipFile);
        }
    }

    /**
     * Sanitize filename for safe file operations
     */
    private function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $filename);
        $filename = preg_replace('/_+/', '_', $filename);
        return trim($filename, '_');
    }

    /**
     * Split phase documents into individual files
     */
    private function splitPhaseDocuments(string $projectDir, string $phasesContent): void
    {
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

            $safeTitle = $this->sanitizeFilename($phaseTitle);
            $fileName = $projectDir . '/' . $safeTitle . '.md';

            file_put_contents($fileName, "## {$phaseTitle}\n\n" . trim($phaseContent));
        }
    }

    /**
     * Recursively remove directory
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }
}
<?php

namespace ProjectPlanner;

class DiagramGenerator
{
    /**
     * Generate PNG diagram from DOT code
     * @param string $dotCode
     * @return string|null Base64 encoded PNG data or null on failure
     */
    public function generatePngFromDot(string $dotCode): ?string
    {
        if (empty($dotCode)) {
            return null;
        }

        $tempDir = sys_get_temp_dir();
        $inputFile = tempnam($tempDir, 'diagram_') . '.dot';
        $outputFile = str_replace('.dot', '.png', $inputFile);

        // Write DOT code to file
        if (file_put_contents($inputFile, $dotCode) === false) {
            return null;
        }

        // Generate PNG using dot command
        $dotPath = config('graphviz.path', 'C:\\Program Files\\Graphviz\\bin\\dot.exe');
        // Use higher DPI and better quality settings
        $command = "\"$dotPath\" -Tpng -Gdpi=150 -Gsize=10,8! \"$inputFile\" -o \"$outputFile\" 2>&1";
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputFile)) {
            // Log error for debugging
            error_log('Graphviz error: ' . implode("\n", $output));
            unlink($inputFile);
            return null;
        }

        $pngData = file_get_contents($outputFile);

        // Cleanup
        unlink($inputFile);
        unlink($outputFile);

        return $pngData;
    }

    /**
     * Extract DOT code from AI response
     * @param string $content
     * @return string
     */
    public function extractDotCode(string $content): string
    {
        // Look for DOT code in code blocks
        $pattern = '/```(?:dot|graphviz)?\s*(.*?)\s*```/s';

        if (preg_match($pattern, $content, $matches)) {
            return trim($matches[1]);
        }

        // If no code blocks, look for digraph keyword
        if (preg_match('/\bdigraph\s+\w+\s*\{.*\}/s', $content, $matches)) {
            return trim($matches[0]);
        }

        // Return the whole content if it looks like DOT
        if (strpos($content, 'digraph') === 0) {
            return trim($content);
        }

        return $content;
    }
}
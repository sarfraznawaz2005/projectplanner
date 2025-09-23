<?php

namespace ProjectPlanner;

use ProjectPlanner\ProjectPlannerAgent;

class DocumentService
{
    private ProjectPlannerAgent $agent;

    public function __construct(ProjectPlannerAgent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * Generate complete project documentation workflow
     */
    public function generateProjectDocumentation(string $projectName, string $projectIdea): array
    {
        $documents = [];

        // Generate PRD
        $documents['prd'] = $this->agent->generatePRD($projectName, $projectIdea);

        // Generate SDD based on PRD
        $documents['sdd'] = $this->agent->generateSDD($projectName, $documents['prd']);

        // Generate Plan based on SDD
        $documents['plan'] = $this->agent->generatePlan($projectName, $documents['sdd']);

        // Generate Phases based on Plan
        $documents['phases'] = $this->agent->generatePhases($documents['plan']);

        // Generate AI Instructions
        $documents['aiInstructions'] = $this->agent->generateAIInstructions($projectName);

        // Generate System Architecture Diagram
        $documents['systemArchDiagram'] = $this->agent->generateDiagram($projectName, $projectIdea);

        return $documents;
    }

    /**
     * Validate project input data
     */
    public function validateProjectInput(string $name, string $idea): void
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Project name is required');
        }

        if (empty(trim($idea))) {
            throw new \InvalidArgumentException('Project idea is required');
        }

        if (strlen($name) > 100) {
            throw new \InvalidArgumentException('Project name must be less than 100 characters');
        }

        if (strlen($idea) > 10000) {
            throw new \InvalidArgumentException('Project idea must be less than 10,000 characters');
        }
    }

    /**
     * Sanitize project input data
     */
    public function sanitizeProjectInput(string $name, string $idea): array
    {
        return [
            'name' => strip_tags(trim($name)),
            'idea' => strip_tags(trim($idea))
        ];
    }
}
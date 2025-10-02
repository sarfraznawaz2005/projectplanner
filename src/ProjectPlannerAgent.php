<?php

namespace ProjectPlanner;

use NeuronAI\Agent;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\Providers\Ollama\Ollama;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\SystemPrompt;

class ProjectPlannerAgent extends Agent
{
    private DiagramGenerator $diagramGenerator;
    private PromptService $promptService;

    public function __construct()
    {
        $this->diagramGenerator = new DiagramGenerator();
        $this->promptService = new PromptService();
    }

    /**
     * Call AI with prompt and extract markdown content from response
     */
    private function callAIAndExtractContent(string $prompt): string
    {
        $response = $this->chat(new UserMessage($prompt));
        $content = $response->getContent();
        return $this->extractMarkdownContent($content);
    }

    protected function provider(): AIProviderInterface
    {
        $provider = config('ai.provider', 'gemini');
        $model = config('ai.model');
        $apiKey = config('ai.api_key');
        $temperature = config('ai.temperature', 0.7);
        $maxTokens = config('ai.max_tokens', 15000);

        switch ($provider) {
            case 'anthropic':
                return new Anthropic(
                    key: $apiKey,
                    model: $model ?: 'claude-3-5-sonnet-20240620',
                    parameters: [
                        'temperature' => $temperature,
                        'max_tokens' => $maxTokens,
                    ]
                );
                
            case 'ollama':
                return new Ollama(
                    url: config('ai.url', 'http://localhost:11434/api'),
                    model: $model ?: 'llama3',
                    parameters: [
                        'temperature' => $temperature,
                    ]
                );
                
            case 'openai':
                return new OpenAI(
                    key: $apiKey,
                    model: $model ?: 'gpt-4o',
                    parameters: [
                        'temperature' => $temperature,
                        'max_tokens' => $maxTokens,
                    ]
                );
                
            case 'gemini':
            default:
                return new Gemini(
                    key: $apiKey,
                    model: $model ?: 'gemini-2.0-flash',
                    parameters: [
                        'safetySettings' => [
                            [
                                'category' => 'HARM_CATEGORY_HARASSMENT',
                                'threshold' => 'BLOCK_NONE',
                            ],
                            [
                                'category' => 'HARM_CATEGORY_HATE_SPEECH',
                                'threshold' => 'BLOCK_NONE',
                            ],
                            [
                                'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                                'threshold' => 'BLOCK_NONE',
                            ],
                            [
                                'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                                'threshold' => 'BLOCK_NONE',
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => $temperature,
                            'maxOutputTokens' => $maxTokens
                        ]
                    ]
                );
        }
    }

    public function generatePRD(string $projectName, string $projectIdea): string
    {
        $prompt = $this->promptService->buildPRDPrompt($projectName, $projectIdea);

        $prompt = new SystemPrompt(background: [$prompt]);

        return $this->callAIAndExtractContent($prompt);
    }

    public function generateSDD(string $projectName, string $prdContent): string
    {
        $prompt = $this->promptService->buildSDDPrompt($projectName, $prdContent);
        
        $prompt = new SystemPrompt(background: [$prompt]);

        return $this->callAIAndExtractContent($prompt);
    }

    public function generateDiagram(string $projectName, string $projectIdea): ?string
    {
        $prompt = $this->promptService->buildDiagramPrompt($projectName, $projectIdea);

        $prompt = new SystemPrompt(background: [$prompt]);

        $response = $this->chat(new UserMessage($prompt));
        $content = $response->getContent();

        // Extract DOT code from response
        $dotCode = $this->diagramGenerator->extractDotCode($content);

        // Generate PNG from DOT code
        return $this->diagramGenerator->generatePngFromDot($dotCode);
    }

    public function generatePlan(string $projectName, string $sddContent): string
    {
        $prompt = $this->promptService->buildPlanPrompt($projectName, $sddContent);

        $prompt = new SystemPrompt(background: [$prompt]);

        return $this->callAIAndExtractContent($prompt);
    }

    public function generatePhases(string $planContent): string
    {
        $prompt = $this->promptService->buildPhasesPrompt($planContent);

        $prompt = new SystemPrompt(background: [$prompt]);
        
        return $this->callAIAndExtractContent($prompt);
    }

    public function generateAIInstructions(string $projectName): string
    {
        // Generate the AI Instruction Guide dynamically without calling AI
        return $this->buildAIInstructionsContent($projectName);
    }
    
    private function buildAIInstructionsContent(string $projectName): string
    {
        return "# AI Coding Agent Instructions for {$projectName}

## Project Overview
This project contains comprehensive documentation to guide AI coding agents in understanding and implementing the {$projectName} application.

## Document Guide

### prd.md (Project Requirements Document)
**Purpose**: Defines what the project is, its goals, requirements, and target audience.
**For AI Agents**: Use this as the starting point to understand the project's objectives, user needs, and functional requirements.

### sdd.md (System Design Document)  
**Purpose**: Provides technical architecture, component design, and implementation details.
**For AI Agents**: Reference this for technical specifications, data models, API designs, and architectural decisions.

### plan.md (Development Plan)
**Purpose**: Outlines the development approach, timeline, resources, and risk management.
**For AI Agents**: Use this to understand the development phases, timeline expectations, and resource requirements.

### phases.md (Phase Documents)
**Purpose**: Breaks down the project into actionable development phases with specific tasks.
**For AI Agents**: Follow these phase-by-phase instructions for implementation. Each phase contains detailed todo lists.

### Phase_X.md (Individual Phase Files)
**Purpose**: Separate files for each development phase with detailed task breakdowns.
**For AI Agents**: Work through these files sequentially for granular implementation guidance.

## How to Use This Documentation
1. Start with prd.md to understand project requirements
2. Review sdd.md for technical architecture and design
3. Use plan.md to understand the development approach
4. Follow phases.md and individual Phase_X.md files for implementation
5. Refer to specific documents as needed during development

## Key Technical Details
- All documents are in Markdown format
- Code examples and technical specifications are provided
- Task breakdowns include implementation details and testing requirements
- Architecture decisions are documented in sdd.md

## Project Structure
```
project/
├── prd.md              # Project Requirements Document
├── sdd.md              # System Design Document  
├── plan.md             # Development Plan
├── phases.md           # Combined Phase Documents
├── Phase_1.md          # Individual Phase 1
├── Phase_2.md          # Individual Phase 2
└── ...                 # Additional Phase files
```

";
    }


    /**
     * Extract clean markdown content from AI response
     * Removes explanatory text before/after markdown code blocks
     *
     * @param string $content
     * @return string
     */
    protected function extractMarkdownContent(string $content): string
    {
        // Pattern to match markdown content within code blocks
        $pattern = '/```(?:markdown)?\s*(.*?)\s*```/s';

        if (preg_match($pattern, $content, $matches)) {
            $extracted = trim($matches[1]);
            // Check if extracted content looks like a complete document (has headers)
            if (preg_match('/^#{1,6} /m', $extracted)) {
                return $extracted;
            }
            // If it's just code, look for the full content outside the code block
        }

        // If no markdown code blocks found, or code block doesn't contain full document,
        // check if content starts with markdown headers and extract everything from the first header onwards
        $headerPattern = '/(#{1,6} .*)/s';
        if (preg_match($headerPattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            // Return everything from the first header onwards
            return trim(substr($content, $matches[0][1]));
        }

        // If no headers found, return the original content
        return trim($content);
    }

}
<?php

namespace ProjectPlanner;

class PromptService
{
    /**
     * Build PRD generation prompt
     */
    public function buildPRDPrompt(string $projectName, string $projectIdea): string
    {
        $today = date('Y-m-d');

        return "Create a comprehensive Project Requirements Document for '{$projectName}'.

Application Idea: {$projectIdea}

Infer the appropriate project type, technology stack, and scale based on the idea. Consider modern development practices and industry standards.

Include these sections:
1. Executive Summary
2. Project Overview
3. Goals and Objectives
4. Target Audience
5. Functional Requirements
6. Non-Functional Requirements
7. User Stories
8. Technical Requirements
9. Constraints and Assumptions
10. Success Metrics
11. Risk Assessment

Provide specific, actionable content for each section tailored to the project type.
Use bullet points and clear descriptions.
Format in markdown with proper headers.
Today's date: {$today}";
    }

    /**
     * Build SDD generation prompt
     */
    public function buildSDDPrompt(string $projectName, string $prdContent): string
    {
        $today = date('Y-m-d');

        return "Create a detailed System Design Document for '{$projectName}' based on this PRD:

{$prdContent}

Design the system architecture considering modern best practices, security standards, and scalability needs. Choose appropriate technologies based on the project requirements and scale.

Include these sections:
1. System Architecture Overview
2. Technology Stack
3. Component Design
4. Data Model and Database Schema
5. API Design
6. Security Considerations
7. Performance Considerations
8. Scalability Design
9. Integration Points
10. Deployment Architecture
11. Testing Strategy

Provide technical details for each section with specific recommendations.
Use bullet points and clear descriptions.
Format in markdown with proper headers.
Today's date: {$today}
NO MERMAID DIAGRAMS - ONLY TEXT.";
    }

    /**
     * Build development plan generation prompt
     */
    public function buildPlanPrompt(string $projectName, string $sddContent): string
    {
        $today = date('Y-m-d');

        return "Create a realistic development plan for '{$projectName}' based on this SDD:

{$sddContent}

Structure the plan with achievable phases, realistic timelines, and appropriate resource allocation based on project complexity and technology choices.

Include these sections:
1. Project Overview
2. Technology Stack
3. Development Phases
4. Timeline Estimates
5. Resource Requirements
6. Risk Assessment and Mitigation
7. Testing Strategy
8. Deployment Plan

Provide specific details for each section with time estimates and dependencies.
Use bullet points and clear descriptions.
Format in markdown with proper headers.
Today's date: {$today}";
    }

    /**
     * Build phase documents generation prompt
     */
    public function buildPhasesPrompt(string $planContent): string
    {
        $today = date('Y-m-d');

        return "Break down this development plan into phase documents with todo lists:

{$planContent}

For each phase:
## Phase X: [Phase Name]
- [ ] Task 1: What needs to be accomplished
- [ ] Task 2: Implementation details
- [ ] Task 3: Testing requirements

Number phases sequentially.
Give each phase a descriptive name.
Include specific, actionable tasks.
Format in markdown with proper headers.
Today's date: {$today}
NO MERMAID DIAGRAMS - ONLY TEXT.";
    }

    /**
     * Build diagram generation prompt
     */
    public function buildDiagramPrompt(string $projectName, string $projectIdea): string
    {
        return "Create a system architecture diagram for '{$projectName}' using Graphviz DOT language.

Based on this project description:
{$projectIdea}

Requirements:
- Use digraph format with proper graph attributes
- Include main components, data flow, and relationships
- Use appropriate node shapes (box, circle, cylinder for databases, etc.)
- Add clear, descriptive labels for components
- Number the relationships/edges sequentially (1, 2, 3...) to show the flow order
- Make it readable and professional with good spacing
- Focus on high-level architecture, not implementation details

Styling requirements:
- Set graph size to at least 8x6 inches for better readability
- Use nodesep=1.0 and ranksep=1.5 for proper spacing between nodes
- Add padding with pad=0.5
- Ensure to increase font size based on diagram size and number of shapes to maintain readability, preferably large font size (e.g., 20-24pt)
- Use 'Arial' for all text to ensure readability
- Ensure black font color for all text
- Make nodes colorful with purposeful colors:
  * User interface components: light blue (fillcolor=\"#E3F2FD\", color=\"#1976D2\")
  * Backend/API components: light green (fillcolor=\"#E8F5E8\", color=\"#388E3C\")
  * Database components: light orange (fillcolor=\"#FFF3E0\", color=\"#F57C00\")
  * External services: light purple (fillcolor=\"#F3E5F5\", color=\"#7B1FA2\")
  * Infrastructure: light gray (fillcolor=\"#F5F5F5\", color=\"#616161\")
- Include sequential numbers in edge labels (e.g., \"1. User Input\", \"2. Data Processing\")
- Make edges colored based on data flow type (blue for user interactions, green for data flow, etc.)

Output only the complete DOT code starting with 'digraph', no explanations or markdown formatting.";
    }
}
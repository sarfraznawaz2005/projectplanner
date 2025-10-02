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

Infer the appropriate project type, technology stack, and scale based on the idea. Consider modern development 
practices and industry standards.

Include these sections:

1. Project Overview & Objectives
2. Core Features
3. Target Audience
4. User Stories
5. Constraints & Assumptions
6. Risk Assessment

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

You are an expert at software system design and web application architecture. Design the system design architecture 
considering modern best practices, security standards, and scalability needs. Choose appropriate technologies 
based on the project requirements and scale.

Include these sections:

1. Technical Requirements
2. System Architecture Overview:
    - Architectural Style: [monolithic, microservices, client-server, DDD, modular, event-driven, etc. that aligns with given prd]
    - Database Type: [SQL vs NoSQL]
    - Database System:
    - Frontend Framwork: [reactjs, vue, inertiajs, livewrite, etc]
    - Backend Framwork: [laravel, symfony, codeigniter, etc]
    - Hosting/Infrastructure: [cloud provider, on-premises, serverless, containerization, etc]

    Provide rationale for each choice.
3. Functional Components
4. Non-Functional Requirements
5. Capacity Planning: [Make assumptions about user load based on project prd. Provide calculations and any used formula too.]
6. System Components: [markdown table]
7. Data Model:
    - Entity Relationships: [must use markdown table format: `Entity 1`, `Relationship`, `Entity 2`, `Description`]
    - Database Schema: [in SQL format]
8. Interface Screens: [markdown table format with these fields: `Screen`, `Description`, `Key Elements`]
9. System APIs: [markdown table]
10. Security Considerations
11. Performance Considerations
12. Scalability Design
13. Integration Points
14. Deployment Architecture
15. Testing Strategy

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

Structure the plan with achievable phases, realistic timelines, and appropriate resource allocation based 
on project complexity and technology choices.

Include these sections:

1. Development Phases
2. Timeline Estimates
3. Resource Requirements
4. Risk Assessment and Mitigation

IMPORTANT: Always provide `Objectives` and `Deliverables` for each phase.

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
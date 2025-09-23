<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo config('app.name'); ?> - AI-Powered Project Documentation</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

     <!-- Styles -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
     <link href="assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">

    <!-- Markdown Editor -->
    <link href="https://unpkg.com/easymde/dist/easymde.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header class="app-header">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-project-diagram header-icon"></i>
                        <div>
                            <h1 class="header-title"><?php echo config('app.name'); ?></h1>
                            <p class="header-subtitle">AI-Powered Project Documentation Generator</p>
                        </div>
                    </div>
                      <div class="progress-indicator">
                          <div class="step-indicator">
                              <span class="step-number active" data-step="1">1</span>
                              <span class="step-label">Project Requirements</span>
                          </div>
                          <div class="step-connector"></div>
                          <div class="step-indicator">
                              <span class="step-number" data-step="2">2</span>
                              <span class="step-label">System Design</span>
                          </div>
                          <div class="step-connector"></div>
                          <div class="step-indicator">
                              <span class="step-number" data-step="3">3</span>
                              <span class="step-label">Development Plan</span>
                          </div>
                          <div class="step-connector"></div>
                          <div class="step-indicator">
                              <span class="step-number" data-step="4">4</span>
                              <span class="step-label">Phase Documents</span>
                          </div>
                          <div class="step-connector"></div>
                          <div class="step-indicator">
                              <span class="step-number" data-step="5">5</span>
                              <span class="step-label">AI Instructions</span>
                          </div>
                          <div class="step-connector"></div>
                          <div class="step-indicator">
                              <span class="step-number" data-step="6">6</span>
                              <span class="step-label">Generate Diagrams</span>
                          </div>
                          <div class="step-connector"></div>
                          <div class="step-indicator">
                              <span class="step-number" data-step="7">7</span>
                              <span class="step-label">Download Documents</span>
                          </div>
                      </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="app-main">
            <div class="container-fluid">
                <!-- Step 1: Project Details -->
                <div class="wizard-step active" data-step="1">
                    <div class="step-header">
                        <h2 class="step-title">
                            <i class="fas fa-lightbulb"></i>
                            Project Details
                        </h2>
                        <p class="step-description">
                            Tell us about your project idea. We'll use this information to generate comprehensive documentation.
                        </p>
                    </div>

                    <div class="form-section">
                        <div class="form-group">
                            <label for="appName" class="form-label">
                                <i class="fas fa-tag"></i>
                                Project Name
                            </label>
                             <input type="text" class="form-control" id="appName" placeholder="Enter your project name">
                             <div class="form-feedback error">Project name is required</div>
                        </div>

                        <div class="form-group">
                            <label for="appIdea" class="form-label">
                                <i class="fas fa-rocket"></i>
                                Project Idea
                            </label>
                             <textarea class="form-control" id="appIdea" rows="8" placeholder="Describe your project idea in detail. What problem does it solve? Who is it for? What are the main features?"></textarea>
                             <div class="form-feedback error">Project idea is required</div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Project Requirements Document -->
                <div class="wizard-step" data-step="2">
                    <div class="step-header">
                        <h2 class="step-title">
                            <i class="fas fa-file-alt"></i>
                            Project Requirements Document
                        </h2>
                    </div>

                    <div class="content-section">
                        <textarea class="content-editor" id="prdEditor" style="display: none;"></textarea>
                    </div>
                </div>

                <!-- Step 3: System Design Document -->
                <div class="wizard-step" data-step="3">
                    <div class="step-header">
                        <h2 class="step-title">
                            <i class="fas fa-project-diagram"></i>
                            System Design Document
                        </h2>
                    </div>

                     <div class="content-section">
                         <textarea class="content-editor" id="sddEditor" style="display: none;"></textarea>
                     </div>
                </div>

                <!-- Step 4: Development Plan -->
                <div class="wizard-step" data-step="4">
                    <div class="step-header">
                        <h2 class="step-title">
                            <i class="fas fa-tasks"></i>
                            Development Plan
                        </h2>
                    </div>

                    <div class="content-section">
                        <textarea class="content-editor" id="planEditor" style="display: none;"></textarea>
                    </div>
                </div>

                <!-- Step 5: Phase Documents -->
                <div class="wizard-step" data-step="5">
                    <div class="step-header">
                        <h2 class="step-title">
                            <i class="fas fa-list-check"></i>
                            Phase Documents
                        </h2>
                    </div>

                    <div class="content-section">
                        <textarea class="content-editor" id="phasesEditor" style="display: none;"></textarea>
                    </div>
                    
                    <div class="step-actions">
                        <button type="button" class="btn btn-outline-secondary" id="skipAIInstructionsBtn">
                            <i class="fas fa-forward"></i>
                            Skip AI Guide
                        </button>
                    </div>
                </div>
                
                 <!-- Step 6: AI Instruction Guide -->
                 <div class="wizard-step" data-step="6">
                     <div class="step-header">
                         <h2 class="step-title">
                             <i class="fas fa-robot"></i>
                             AI Instruction Guide
                         </h2>
                         <p class="step-description">
                             Guide for AI coding agents to understand and use your project documentation effectively.
                         </p>
                     </div>

                     <div class="content-section">
                         <textarea class="content-editor" id="aiInstructionsEditor" style="display: none;"></textarea>
                     </div>
                 </div>

                 <!-- Step 7: Generate Diagrams -->
                 <div class="wizard-step" data-step="7">
                     <div class="step-header">
                         <h2 class="step-title">
                             <i class="fas fa-project-diagram"></i>
                             Generate Diagrams
                         </h2>
                         <p class="step-description">
                             Create visual system architecture diagrams for your project documentation.
                         </p>
                     </div>

                      <div class="diagram-actions" id="diagramActions" style="display: none;">
                          <div class="step-actions">
                              <button type="button" class="btn btn-primary" id="generateSystemArchBtn">
                                  <i class="fas fa-project-diagram"></i>
                                  Generate System Architecture
                              </button>
                          </div>
                      </div>

                     <div class="diagram-section" id="systemArchSection" style="display: none;">
                         <h3 class="diagram-title">
                             <i class="fas fa-project-diagram"></i>
                             System Architecture Diagram
                         </h3>
                         <div class="diagram-container">
                             <img id="systemArchImg" alt="System Architecture Diagram" />
                             <p class="diagram-caption">Generated system architecture diagram showing the high-level components and data flow.</p>
                         </div>
                     </div>


                 </div>

                 <!-- Step 8: Download Documents -->
                 <div class="wizard-step" data-step="8">
                    <div class="step-header">
                        <h2 class="step-title">
                            <i class="fas fa-download"></i>
                            Download Documents
                        </h2>
                        <p class="step-description">
                            Your project documentation is ready! Download all documents as a ZIP file or start a new project.
                        </p>
                    </div>

                    <div class="download-section">
                        <div class="document-preview">
                            <div class="document-card">
                                <i class="fas fa-file-alt document-icon"></i>
                                <h3>Project Requirements Document</h3>
                                <p class="document-description">Complete PRD with all project specifications</p>
                            </div>
                            <div class="document-card">
                                <i class="fas fa-project-diagram document-icon"></i>
                                <h3>System Design Document</h3>
                                <p class="document-description">Technical architecture and design specifications</p>
                            </div>
                            <div class="document-card">
                                <i class="fas fa-tasks document-icon"></i>
                                <h3>Development Plan</h3>
                                <p class="document-description">Detailed technical roadmap and timeline</p>
                            </div>
                            <div class="document-card">
                                <i class="fas fa-list-check document-icon"></i>
                                <h3>Phase Documents</h3>
                                <p class="document-description">Actionable phase breakdowns with todo lists</p>
                            </div>
                             <div class="document-card" id="aiInstructionsCard" style="display: none;">
                                 <i class="fas fa-robot document-icon"></i>
                                 <h3>AI Instruction Guide</h3>
                                 <p class="document-description">Instructions for AI agents (Optional)</p>
                             </div>
                             <div class="document-card" id="diagramsCard" style="display: none;">
                                 <i class="fas fa-project-diagram document-icon"></i>
                                 <h3>System Architecture Diagram</h3>
                                 <p class="document-description">Visual system architecture diagram</p>
                             </div>
                        </div>
                        
                        <div class="download-buttons">
                            <button type="button" class="btn btn-primary btn-download" id="downloadBtn">
                                <i class="fas fa-download"></i>
                                Download All Documents (ZIP)
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="startAgainBtn">
                                <i class="fas fa-plus-circle"></i>
                                Start New Project
                            </button>
                        </div>
                    </div>
                </div>
             </div>
         </main>

         <!-- Loading Overlay -->
         <div id="loadingOverlay" class="loading-overlay">
             <div class="loading-content">
                 <div class="loading-spinner"></div>
                 <h2 class="loading-title">Generating Your Project Documentation</h2>
                 <p class="loading-subtitle">This may take a few moments while our AI processes your request...</p>
                 <div class="loading-progress">
                     <div class="progress-bar">
                         <div class="progress-fill"></div>
                     </div>
                     <span class="progress-text">Processing...</span>
                 </div>
             </div>
         </div>

        <!-- Navigation -->
        <nav class="app-navigation">
            <div class="container-fluid">
                <div class="nav-buttons">
                    <button type="button" class="btn btn-outline-secondary" id="backBtn" disabled>
                        <i class="fas fa-arrow-left"></i>
                        Back
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn">
                        <span class="btn-text">Generate PRD</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </nav>
    </div>

     <!-- Scripts -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
     <script src="assets/js/app.js?v=<?php echo time(); ?>"></script>
</body>
</html>
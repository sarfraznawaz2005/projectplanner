class ProjectPlanner {
      constructor() {
          this.currentStep = 1;
          this.progressCompleted = 0;
           this.totalSteps = 8; // 8 steps total
             this.projectData = {
                  name: '',
                  idea: '',
                  prd: '',
                  sdd: '',
                  systemArchDiagram: null,
                  plan: '',
                  phases: '',
                  aiInstructions: ''
              };
          this.editors = {};
          this.isGenerating = false;

          this.initializeElements();
          this.bindEvents();
          this.initializeEditors();
          this.setDefaultValues();
          this.validateStep();
     }

    setDefaultValues() {
        // No default values - form starts empty
    }

      initializeElements() {
           this.steps = document.querySelectorAll('.wizard-step');
           this.stepNumbers = document.querySelectorAll('.step-number');
           this.stepConnectors = document.querySelectorAll('.step-connector');
           this.backBtn = document.getElementById('backBtn');
           this.nextBtn = document.getElementById('nextBtn');
           this.nextBtnText = this.nextBtn.querySelector('.btn-text');
           this.appNameInput = document.getElementById('appName');
           this.appIdeaInput = document.getElementById('appIdea');
           this.loadingOverlay = document.getElementById('loadingOverlay');
       }

    bindEvents() {
         this.backBtn.addEventListener('click', () => this.previousStep());
         this.nextBtn.addEventListener('click', () => this.nextStep());
         this.appNameInput.addEventListener('input', () => this.validateStep());
         this.appIdeaInput.addEventListener('input', () => this.validateStep());

         // Bind events for Download Documents step buttons
         // These might not exist yet, so we'll bind them when the step is shown
     }

     bindDiagramButton() {
           const generateSystemArchBtn = document.getElementById('generateSystemArchBtn');

           if (generateSystemArchBtn && !generateSystemArchBtn.hasAttribute('data-bound')) {
               generateSystemArchBtn.addEventListener('click', () => this.generateSystemArch());
               generateSystemArchBtn.setAttribute('data-bound', 'true');
           }
       }

    initializeEditors() {
        // Initialize markdown editors when needed
        this.editors.prd = null;
        this.editors.plan = null;
        this.editors.phases = null;
    }
    
    /**
     * Show a notification to the user
     * @param {string} message - The message to display
     * @param {string} type - The type of notification ('success', 'error', 'warning', 'info')
     * @param {number} duration - How long to show the notification in milliseconds (default: 5000)
     */
    showNotification(message, type = 'info', duration = 5000) {
        // Remove any existing notifications
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="notification-icon"></i>
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        // Add to document
        document.body.appendChild(notification);
        
        // Trigger animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Close button event
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            this.hideNotification(notification);
        });
        
        // Auto-hide after duration
        if (duration > 0) {
            setTimeout(() => {
                this.hideNotification(notification);
            }, duration);
        }
    }
    
    /**
     * Hide a notification
     * @param {HTMLElement} notification - The notification element to hide
     */
    hideNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    validateStep() {
        const currentName = this.appNameInput.value.trim();
        const currentIdea = this.appIdeaInput.value.trim();
        const isValid = currentName !== '' && currentIdea !== '';

        if (this.currentStep === 1) {
            this.nextBtn.disabled = !isValid;
        }

        // Update form feedback
        const nameFeedback = this.appNameInput.parentElement.querySelector('.form-feedback');
        const ideaFeedback = this.appIdeaInput.parentElement.querySelector('.form-feedback');

        if (nameFeedback) {
            nameFeedback.textContent = currentName === '' ? 'Project name is required' : '✓ Project name is ready';
            nameFeedback.className = 'form-feedback' + (currentName === '' ? ' error' : ' success');
        }

        if (ideaFeedback) {
            ideaFeedback.textContent = currentIdea === '' ? 'Project idea is required' : '✓ Project idea is ready';
            ideaFeedback.className = 'form-feedback' + (currentIdea === '' ? ' error' : ' success');
        }
    }

     updateStepIndicator() {
           this.stepNumbers.forEach((step) => {
               const stepNumber = parseInt(step.getAttribute('data-step'));
               step.classList.remove('active', 'completed');

               if (stepNumber <= this.progressCompleted) {
                   step.classList.add('completed');
               } else if (stepNumber === this.progressCompleted + 1) {
                   step.classList.add('active');
               }
           });

           this.stepConnectors.forEach((connector, index) => {
               const stepNumber = index + 2;
               connector.classList.remove('active');

               if (stepNumber <= this.progressCompleted) {
                   connector.classList.add('active');
               }
           });
       }

     showStep(stepNumber) {
           this.steps.forEach(step => step.classList.remove('active'));
           const targetStep = Array.from(this.steps).find(step => step.getAttribute('data-step') == stepNumber);
           if (targetStep) {
               targetStep.classList.add('active');
               
                // Bind events for Generate Diagrams step when step 7 is shown
                if (stepNumber == 7) {
                    setTimeout(() => {
                        this.bindDiagramButton();
                        // Show diagram actions
                        const diagramActions = document.getElementById('diagramActions');
                        if (diagramActions) {
                            diagramActions.style.display = 'block';
                        }
                        // Show diagram if it exists
                        if (this.projectData.diagram) {
                            const diagramImg = document.getElementById('diagram');
                            const diagramSection = document.getElementById('diagramSection');
                            if (diagramImg && diagramSection) {
                                diagramImg.src = `data:image/png;base64,${this.projectData.diagram}`;
                                diagramSection.style.display = 'block';
                                // Update button text
                                const generateDiagramBtn = document.getElementById('generateDiagramBtn');
                                if (generateDiagramBtn) {
                                    generateDiagramBtn.innerHTML = '<i class="fas fa-redo"></i> Regenerate Diagram';
                                }
                            }
                        }
                    }, 0);
                }

                // Bind events for Download Documents step buttons when step 8 is shown
                if (stepNumber == 8) {
                    setTimeout(() => {
                        const downloadBtn = document.getElementById('downloadBtn');
                        const startAgainBtn = document.getElementById('startAgainBtn');

                        if (downloadBtn && !downloadBtn.hasAttribute('data-bound')) {
                            downloadBtn.addEventListener('click', () => this.handleDownloadClick());
                            downloadBtn.setAttribute('data-bound', 'true');
                        }

                        if (startAgainBtn && !startAgainBtn.hasAttribute('data-bound')) {
                            startAgainBtn.addEventListener('click', () => this.startAgain());
                            startAgainBtn.setAttribute('data-bound', 'true');
                        }

                          // Show/hide document cards based on generated content
                          const aiInstructionsCard = document.getElementById('aiInstructionsCard');
                          const diagramsCard = document.getElementById('diagramsCard');

                          if (aiInstructionsCard) {
                              aiInstructionsCard.style.display = this.projectData.aiInstructions ? 'block' : 'none';
                          }

                          if (diagramsCard) {
                              diagramsCard.style.display = this.projectData.systemArchDiagram ? 'block' : 'none';
                          }
                    }, 0);
                }

                // Validate form when showing step 1
                if (stepNumber == 1) {
                    setTimeout(() => {
                        this.validateStep();
                    }, 0);
                }
               
               // Bind events for AI Instructions skip button when step 5 is shown
               if (stepNumber == 5) {
                   setTimeout(() => {
                       const skipBtn = document.getElementById('skipAIInstructionsBtn');
                       
                       if (skipBtn && !skipBtn.hasAttribute('data-bound')) {
                           skipBtn.addEventListener('click', () => this.skipAIInstructions());
                           skipBtn.setAttribute('data-bound', 'true');
                       }
                   }, 0);
               }
           }

           this.updateStepIndicator();
           this.updateNavigation();
       }

     updateNavigation() {
         this.backBtn.disabled = this.currentStep === 1 || this.isGenerating;

         if (this.isGenerating) {
             this.nextBtn.disabled = true;
             this.nextBtn.classList.add('loading');
             this.nextBtnText.textContent = 'Generating...';
         } else {
             this.nextBtn.disabled = false;
             this.nextBtn.classList.remove('loading');
             this.nextBtn.style.display = 'flex'; // Ensure it's visible by default

              switch (this.currentStep) {
                  case 1:
                      this.nextBtnText.textContent = 'Generate PRD';
                      break;
                  case 2:
                      this.nextBtnText.textContent = 'Generate SDD';
                      break;
                  case 3:
                      this.nextBtnText.textContent = 'Generate Plan';
                      break;
                  case 4:
                      this.nextBtnText.textContent = 'Generate Phases';
                      break;
                   case 5:
                       this.nextBtnText.textContent = 'Generate AI Guide';
                       // Show skip button on step 5
                       const skipBtn = document.getElementById('skipAIInstructionsBtn');
                       if (skipBtn) {
                           skipBtn.style.display = 'inline-block';
                       }
                       break;
                   case 6:
                       this.nextBtnText.textContent = 'Generate Diagrams';
                       // Hide skip button on AI Guide step
                       const skipBtnOnAIStep = document.getElementById('skipAIInstructionsBtn');
                       if (skipBtnOnAIStep) {
                           skipBtnOnAIStep.style.display = 'none';
                       }
                       break;
                   case 7:
                       this.nextBtnText.textContent = 'Download';
                       // Hide skip button on Diagrams step
                       const skipBtnOnDiagramStep = document.getElementById('skipAIInstructionsBtn');
                       if (skipBtnOnDiagramStep) {
                           skipBtnOnDiagramStep.style.display = 'none';
                       }
                       break;
                   case 8:
                       this.nextBtnText.textContent = 'Download Files';
                       this.nextBtn.disabled = true;
                       this.nextBtn.style.display = 'none';
                       // Hide skip button on download step
                       const skipBtnOnDownloadStep = document.getElementById('skipAIInstructionsBtn');
                       if (skipBtnOnDownloadStep) {
                           skipBtnOnDownloadStep.style.display = 'none';
                       }
                       break;
              }
         }
     }

     async nextStep() {
         if (this.isGenerating) return;

         switch (this.currentStep) {
             case 1:
                 await this.generatePRD();
                 break;
             case 2:
                 await this.generateSDD();
                 break;
             case 3:
                 await this.generatePlan();
                 break;
             case 4:
                 await this.generatePhases();
                 break;
              case 5:
                  await this.generateAIInstructions();
                  break;
              case 6:
                  this.showDiagrams();
                  break;
              case 7:
                  this.progressCompleted = 7; // Mark Download Documents as completed
                  this.showStep(8); // Navigate to Download Documents screen (step 8)
                  this.currentStep = 8;
                  this.updateNavigation();
                  break;
              case 8:
                  this.downloadFiles();
                  break;
         }
     }

     previousStep() {
         if (this.currentStep > 1 && !this.isGenerating) {
             this.currentStep--;
             this.showStep(this.currentStep);
             this.updateNavigation();
         }
     }

     showLoadingOverlay(message = 'Generating Your Project Documentation') {
         if (this.loadingOverlay) {
             const title = this.loadingOverlay.querySelector('.loading-title');
             if (title) {
                 title.textContent = message;
             }
             this.loadingOverlay.style.display = 'flex'; // Ensure it's displayed
             this.loadingOverlay.classList.add('active');
         }
     }

     hideLoadingOverlay() {
         if (this.loadingOverlay) {
             this.loadingOverlay.classList.remove('active');
             this.loadingOverlay.style.display = 'none'; // Hide it completely
         }
     }

     async generatePRD() {
         this.projectData.name = this.appNameInput.value.trim();
         this.projectData.idea = this.appIdeaInput.value.trim();

         if (!this.projectData.name || !this.projectData.idea) {
             this.showNotification('Please fill in all fields', 'error');
             return;
         }

         this.isGenerating = true;
         this.updateNavigation();
         this.showLoadingOverlay('Generating Your Project Requirements Document');
         this.showStep(2);

         try {
             const response = await fetch('/api/generate-prd', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                 },
                 body: JSON.stringify(this.projectData)
             });

             if (!response.ok) {
                 throw new Error('Failed to generate PRD');
             }

             const result = await response.json();

             if (result.success) {
                 this.projectData.prd = result.content;
                 this.showPRD();
             } else {
                 throw new Error(result.error || 'Unknown error occurred');
             }
           } catch (error) {
               console.error('PRD generation error:', error);
               this.showNotification('Failed to generate PRD. Please try again.', 'error');
               this.showStep(1);
           } finally {
              this.isGenerating = false;
              this.updateNavigation();
              this.hideLoadingOverlay();
          }
    }

    showPRD() {
        const editorContainer = document.getElementById('prdEditor');
        editorContainer.style.display = 'block';

        // Ensure loading overlay is hidden
        this.hideLoadingOverlay();

        // Initialize EasyMDE editor
        if (!this.editors.prd) {
            this.editors.prd = new EasyMDE({
                element: editorContainer,
                spellChecker: false,
                renderingConfig: {
                    singleLineBreaks: false,
                    codeSyntaxHighlighting: true,
                },
                toolbar: [
                    'bold', 'italic', 'heading', '|',
                    'code', 'quote', 'unordered-list', 'ordered-list', '|',
                    'link', 'image', '|',
                    'preview', 'side-by-side', 'fullscreen', '|',
                    'guide'
                ],
                status: ['autosave', 'lines', 'words', 'cursor'],
                autofocus: true,
                placeholder: 'PRD content will appear here...'
            });
        }

        // Set the content
        if (this.editors.prd) {
            this.editors.prd.value(this.projectData.prd || '');
            if (this.editors.prd.codemirror) {
                this.editors.prd.codemirror.refresh();
            }
            // Switch to preview mode by default
            if (!this.editors.prd.isPreviewActive()) {
                this.editors.prd.togglePreview();
            }
        }

        this.progressCompleted = 1;
        this.currentStep = 2;
        this.showStep(2);
        this.updateNavigation();
        
        // Show success notification
        this.showNotification('Project Requirements Document generated successfully!', 'success');
    }

     async generatePlan() {
         this.isGenerating = true;
         this.updateNavigation();
         this.showLoadingOverlay('Creating Your Development Plan');
         this.showStep(3);

        try {
            const response = await fetch('/api/generate-plan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(this.projectData)
            });

            if (!response.ok) {
                throw new Error('Failed to generate plan');
            }

            const result = await response.json();

            if (result.success) {
                this.projectData.plan = result.content;
                this.showPlan();
            } else {
                throw new Error(result.error || 'Unknown error occurred');
            }
          } catch (error) {
              console.error('Plan generation error:', error);
              this.showNotification('Failed to generate development plan. Please try again.', 'error');
              this.showStep(2);
          } finally {
             this.isGenerating = false;
             this.updateNavigation();
             this.hideLoadingOverlay();
         }
    }

     async generateSDD() {
         this.isGenerating = true;
         this.updateNavigation();
          this.showLoadingOverlay('Creating Your System Design Document');
          this.showStep(3);

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 300000); // 5 minute timeout
            
            const response = await fetch('/api/generate-sdd', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(this.projectData),
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error('Failed to generate SDD');
            }

            const result = await response.json();

             if (result.success) {
                 this.projectData.sdd = result.content;
                 this.showSDD();
             } else {
                 throw new Error(result.error || 'Unknown error occurred');
             }
          } catch (error) {
              console.error('SDD generation error:', error);
              if (error.name === 'AbortError') {
                  this.showNotification('SDD generation timed out. Please try again.', 'error');
              } else {
                  this.showNotification('Failed to generate system design document. Please try again.', 'error');
              }
              this.showStep(3);
          } finally {
             this.isGenerating = false;
             this.updateNavigation();
             this.hideLoadingOverlay();
          }
     }

      async generateSystemArch() {
          const generateSystemArchBtn = document.getElementById('generateSystemArchBtn');
          const systemArchSection = document.getElementById('systemArchSection');
          const systemArchImg = document.getElementById('systemArchImg');

          // Disable button and show loading state
          generateSystemArchBtn.disabled = true;
          generateSystemArchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

          try {
              const response = await fetch('/api/generate-diagram', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                  },
                  body: JSON.stringify({
                      name: this.projectData.name,
                      idea: this.projectData.idea
                  })
              });

              if (!response.ok) {
                  throw new Error('Failed to generate diagram');
              }

              const result = await response.json();

              if (result.success) {
                  this.projectData.systemArchDiagram = result.diagram;

                  // Re-enable button, update text, show diagram
                  generateSystemArchBtn.disabled = false;
                  generateSystemArchBtn.innerHTML = '<i class="fas fa-redo"></i> Regenerate System Architecture';
                  systemArchImg.src = `data:image/png;base64,${this.projectData.systemArchDiagram}`;
                  systemArchSection.style.display = 'block';

                  this.showNotification('System Architecture Diagram generated successfully!', 'success');
              } else {
                  throw new Error(result.error || 'Unknown error occurred');
              }
          } catch (error) {
              console.error('System Architecture generation error:', error);
              this.showNotification('Failed to generate diagram. Please try again.', 'error');

              // Re-enable button
              generateSystemArchBtn.disabled = false;
              generateSystemArchBtn.innerHTML = '<i class="fas fa-project-diagram"></i> Generate System Architecture';
          }
      }



     showPlan() {
        const editorContainer = document.getElementById('planEditor');
        editorContainer.style.display = 'block';

        if (!this.editors.plan) {
            this.editors.plan = new EasyMDE({
                element: editorContainer,
                spellChecker: false,
                renderingConfig: {
                    singleLineBreaks: false,
                    codeSyntaxHighlighting: true,
                },
                toolbar: [
                    'bold', 'italic', 'heading', '|',
                    'code', 'quote', 'unordered-list', 'ordered-list', '|',
                    'link', 'image', '|',
                    'preview', 'side-by-side', 'fullscreen', '|',
                    'guide'
                ],
                status: ['autosave', 'lines', 'words', 'cursor'],
                autofocus: true,
                placeholder: 'Development plan will appear here...'
            });
        }

        // Set the content after ensuring the editor is initialized
        if (this.editors.plan) {
            this.editors.plan.value(this.projectData.plan || '');
            // Refresh the editor to ensure proper rendering
            if (this.editors.plan.codemirror) {
                this.editors.plan.codemirror.refresh();
            }
            // Switch to preview mode by default
            if (!this.editors.plan.isPreviewActive()) {
                this.editors.plan.togglePreview();
            }
        }

        this.progressCompleted = 3;
        this.currentStep = 4;
        this.showStep(4);
        this.updateNavigation();
        
        // Show success notification
        this.showNotification('Development Plan generated successfully!', 'success');
    }

    showSDD() {
        const editorContainer = document.getElementById('sddEditor');

        editorContainer.style.display = 'block';

        if (!this.editors.sdd) {
            this.editors.sdd = new EasyMDE({
                element: editorContainer,
                spellChecker: false,
                renderingConfig: {
                    singleLineBreaks: false,
                    codeSyntaxHighlighting: true,
                },
                toolbar: [
                    'bold', 'italic', 'heading', '|',
                    'code', 'quote', 'unordered-list', 'ordered-list', '|',
                    'link', 'image', '|',
                    'preview', 'side-by-side', 'fullscreen', '|',
                    'guide'
                ],
                status: ['autosave', 'lines', 'words', 'cursor'],
                autofocus: true,
                placeholder: 'System design document will appear here...'
            });
        }

        // Set the SDD content (without diagram)
        if (this.editors.sdd) {
            this.editors.sdd.value(this.projectData.sdd || '');
            // Refresh the editor to ensure proper rendering
            if (this.editors.sdd.codemirror) {
                this.editors.sdd.codemirror.refresh();
            }
            // Switch to preview mode by default
            if (!this.editors.sdd.isPreviewActive()) {
                this.editors.sdd.togglePreview();
            }
        }



        this.progressCompleted = 2;
        this.currentStep = 3; // Stay on step 3 (SDD)
        this.showStep(3);
        this.updateNavigation();

        // Show success notification
        this.showNotification('System Design Document generated successfully!', 'success');
    }

     async generatePhases() {
         this.isGenerating = true;
         this.updateNavigation();
          this.showLoadingOverlay('Breaking Down Into Manageable Phases');
          this.showStep(5);

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 300000); // 5 minute timeout
            
            const response = await fetch('/api/generate-phases', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(this.projectData),
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error('Failed to generate phases');
            }

            const result = await response.json();

            if (result.success) {
                this.projectData.phases = result.content;
                this.showPhases();
            } else {
                throw new Error(result.error || 'Unknown error occurred');
            }
          } catch (error) {
              console.error('Phases generation error:', error);
              if (error.name === 'AbortError') {
                  this.showNotification('Phases generation timed out. Please try again.', 'error');
              } else {
                  this.showNotification('Failed to generate phase documents. Please try again.', 'error');
              }
              this.showStep(5);
          } finally {
             this.isGenerating = false;
             this.updateNavigation();
             this.hideLoadingOverlay();
         }
    }

     async generateAIInstructions() {
         this.isGenerating = true;
         this.updateNavigation();
          this.showLoadingOverlay('Creating AI Coding Agent Guide');
          this.showStep(6);

        try {
            const response = await fetch('/api/generate-ai-instructions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: this.projectData.name
                })
            });

            if (!response.ok) {
                throw new Error('Failed to generate AI instructions');
            }

            const result = await response.json();

            if (result.success) {
                this.projectData.aiInstructions = result.content;
                this.showAIInstructions();
            } else {
                throw new Error(result.error || 'Unknown error occurred');
            }
          } catch (error) {
              console.error('AI Instructions generation error:', error);
              this.showNotification('Failed to generate AI coding agent guide. Please try again.', 'error');
              this.showStep(6);
          } finally {
             this.isGenerating = false;
             this.updateNavigation();
             this.hideLoadingOverlay();
         }
    }

    showPhases() {
        const editorContainer = document.getElementById('phasesEditor');
        editorContainer.style.display = 'block';

        if (!this.editors.phases) {
            this.editors.phases = new EasyMDE({
                element: editorContainer,
                spellChecker: false,
                renderingConfig: {
                    singleLineBreaks: false,
                    codeSyntaxHighlighting: true,
                },
                toolbar: [
                    'bold', 'italic', 'heading', '|',
                    'code', 'quote', 'unordered-list', 'ordered-list', '|',
                    'link', 'image', '|',
                    'preview', 'side-by-side', 'fullscreen', '|',
                    'guide'
                ],
                status: ['autosave', 'lines', 'words', 'cursor'],
                autofocus: true,
                placeholder: 'Phase documents will appear here...'
            });
        }

        // Set the content after ensuring the editor is initialized
        if (this.editors.phases) {
            this.editors.phases.value(this.projectData.phases || '');
            // Refresh the editor to ensure proper rendering
            if (this.editors.phases.codemirror) {
                this.editors.phases.codemirror.refresh();
            }
            // Switch to preview mode by default
            if (!this.editors.phases.isPreviewActive()) {
                this.editors.phases.togglePreview();
            }
        }

        this.progressCompleted = 4;
        this.currentStep = 5; // Stay on step 5 (Phase Documents)
        this.showStep(5);
        this.updateNavigation();
        
        // Show success notification
        this.showNotification('Phase Documents generated successfully!', 'success');
    }

    showAIInstructions() {
        const editorContainer = document.getElementById('aiInstructionsEditor');
        editorContainer.style.display = 'block';

        if (!this.editors.aiInstructions) {
            this.editors.aiInstructions = new EasyMDE({
                element: editorContainer,
                spellChecker: false,
                renderingConfig: {
                    singleLineBreaks: false,
                    codeSyntaxHighlighting: true,
                },
                toolbar: [
                    'bold', 'italic', 'heading', '|',
                    'code', 'quote', 'unordered-list', 'ordered-list', '|',
                    'link', 'image', '|',
                    'preview', 'side-by-side', 'fullscreen', '|',
                    'guide'
                ],
                status: ['autosave', 'lines', 'words', 'cursor'],
                autofocus: true,
                placeholder: 'AI coding agent guide will appear here...'
            });
        }

        // Set the content after ensuring the editor is initialized
        if (this.editors.aiInstructions) {
            this.editors.aiInstructions.value(this.projectData.aiInstructions || '');
            // Refresh the editor to ensure proper rendering
            if (this.editors.aiInstructions.codemirror) {
                this.editors.aiInstructions.codemirror.refresh();
            }
            // Switch to preview mode by default
            if (!this.editors.aiInstructions.isPreviewActive()) {
                this.editors.aiInstructions.togglePreview();
            }
        }

        this.progressCompleted = 5;
        this.currentStep = 6; // Stay on step 6 (AI Instructions)
        this.showStep(6);
        this.updateNavigation();
        
        // Show success notification
        this.showNotification('AI Coding Agent Guide generated successfully!', 'success');
    }

     showDiagrams() {
         // Show diagram actions
         const diagramActions = document.getElementById('diagramActions');
         if (diagramActions) {
             diagramActions.style.display = 'block';
         }

         // Show system architecture diagram if it exists
         if (this.projectData.systemArchDiagram) {
             const systemArchImg = document.getElementById('systemArchImg');
             const systemArchSection = document.getElementById('systemArchSection');
             if (systemArchImg && systemArchSection) {
                 systemArchImg.src = `data:image/png;base64,${this.projectData.systemArchDiagram}`;
                 systemArchSection.style.display = 'block';
                 // Update button text
                 const generateSystemArchBtn = document.getElementById('generateSystemArchBtn');
                 if (generateSystemArchBtn) {
                     generateSystemArchBtn.innerHTML = '<i class="fas fa-redo"></i> Regenerate System Architecture';
                 }
             }
         }

         this.progressCompleted = 6;
         this.currentStep = 7;
         this.showStep(7);
         this.updateNavigation();

         // Bind diagram generation buttons
         this.bindDiagramButton();

         // Show success notification
         this.showNotification('Ready to generate system architecture diagram!', 'info');
     }

    /**
     * Skip the AI Instructions step and proceed to download
     */
    skipAIInstructions() {
        // Mark the step as completed without generating content
        this.progressCompleted = 6; // Mark AI Instructions as completed (skipped)
        this.currentStep = 7; // Navigate to Generate Diagrams step
        this.showStep(7);
        this.updateNavigation();

        // Show notification
        this.showNotification('Skipped AI Instruction Guide. Proceeding to generate diagrams...', 'info');
    }

    /**
     * Handle download button click on the Download Documents screen
     */
    handleDownloadClick() {
        // Proceed with actual download
        this.downloadFiles();
    }
    
    /**
     * Reset the application and start a new project
     */
    startAgain() {
        // Reset all data
         this.projectData = {
             name: '',
             idea: '',
             prd: '',
             sdd: '',
             systemArchDiagram: null,
             plan: '',
             phases: '',
             aiInstructions: ''
         };
        
        // Reset editors
        this.editors = {
            prd: null,
            sdd: null,
            plan: null,
            phases: null,
            aiInstructions: null
        };
        
        // Reset progress
        this.currentStep = 1;
        this.progressCompleted = 0;
        
        // Clear form inputs
        if (this.appNameInput) {
            this.appNameInput.value = '';
        }
        if (this.appIdeaInput) {
            this.appIdeaInput.value = '';
        }

        // Validate form to update feedback messages
        this.validateStep();
        
        // Hide all editors
        const editors = document.querySelectorAll('.content-editor');
        editors.forEach(editor => {
            editor.style.display = 'none';
        });

        // Hide diagram sections
        const diagramSections = document.querySelectorAll('.diagram-section');
        diagramSections.forEach(section => {
            section.style.display = 'none';
        });

        // Reset diagram actions (button text)
        const diagramActions = document.querySelectorAll('.diagram-actions');
        diagramActions.forEach(action => {
            action.style.display = 'none';
            const systemArchBtn = action.querySelector('#generateSystemArchBtn');
            const dataFlowBtn = action.querySelector('#generateDataFlowBtn');
            const userJourneyBtn = action.querySelector('#generateUserJourneyBtn');
            if (systemArchBtn) {
                systemArchBtn.innerHTML = '<i class="fas fa-project-diagram"></i> Generate System Architecture';
                systemArchBtn.removeAttribute('data-bound');
            }
            if (dataFlowBtn) {
                dataFlowBtn.innerHTML = '<i class="fas fa-chart-line"></i> Generate Data Flow Diagram';
                dataFlowBtn.removeAttribute('data-bound');
            }
            if (userJourneyBtn) {
                userJourneyBtn.innerHTML = '<i class="fas fa-route"></i> Generate User Journey Flow';
                userJourneyBtn.removeAttribute('data-bound');
            }
        });
        
        // Navigate to first step
        this.showStep(1);
        this.updateNavigation();
        this.updateStepIndicator();
        
        // Show notification
        this.showNotification('Starting a new project. Please fill in the details below.', 'info');
    }
    
    /**
     * Download all project documents as a ZIP file
     */
    downloadFiles() {
        // Create a form to submit all data for file generation
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/download';

        const data = {
            name: this.projectData.name,
            idea: this.projectData.idea,
            prd: this.editors.prd ? this.editors.prd.value() : this.projectData.prd,
            sdd: this.editors.sdd ? this.editors.sdd.value() : this.projectData.sdd,
            systemArchDiagram: this.projectData.systemArchDiagram,
            plan: this.editors.plan ? this.editors.plan.value() : this.projectData.plan,
            phases: this.editors.phases ? this.editors.phases.value() : this.projectData.phases,
            aiInstructions: this.editors.aiInstructions ? this.editors.aiInstructions.value() : this.projectData.aiInstructions
        };

        Object.keys(data).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = data[key];
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }


}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.projectPlanner = new ProjectPlanner();
});
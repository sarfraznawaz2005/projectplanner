# ProjectPlanner PHP

A modern, web-based project documentation generator with AI-powered content creation and markdown support.

## Features

- ✨ **Modern UI**: Beautiful, responsive design with smooth animations
- 🤖 **AI-Powered**: Uses Google's Gemini AI for content generation
- 📝 **Markdown Support**: Full markdown editing and rendering
- 📁 **File Generation**: Automatic ZIP download with organized documents
- 🎯 **Wizard Interface**: Step-by-step project documentation process
- 🏗️ **System Diagrams**: Automatic architecture diagram generation

## Requirements

- PHP 8.1 or higher
- Composer
- Web server (Apache/Nginx)
- Google Gemini API key

## Installation

1. **Clone or download** this PHP folder to your web server

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Configure environment**:
   ```bash
   cp .env.example .env
   ```

   Edit `.env` with your settings. Examples for different providers:

   **Gemini (default):**
   ```env
   APP_NAME=ProjectPlanner
   APP_DEBUG=false
   APP_URL=http://localhost

   AI_PROVIDER=gemini
   AI_API_KEY=your_gemini_api_key_here
   AI_MODEL=gemini-2.0-flash
   AI_TEMPERATURE=0.7
   AI_MAX_TOKENS=8192
   ```

   **OpenAI:**
   ```env
   AI_PROVIDER=openai
   AI_API_KEY=your_openai_api_key_here
   AI_MODEL=gpt-4o
   ```

   **Anthropic:**
   ```env
   AI_PROVIDER=anthropic
   AI_API_KEY=your_anthropic_api_key_here
   AI_MODEL=claude-3-5-sonnet-20240620
   ```

   **Ollama:**
   ```env
   AI_PROVIDER=ollama
   AI_URL=http://localhost:11434/api
   AI_MODEL=llama3
   ```

4. **Set up directories**:
   ```bash
   mkdir -p projects logs temp
   chmod 755 projects logs temp
   ```

5. **Configure web server**:
   - Point your web server to the PHP folder
   - Ensure `.htaccess` is enabled for Apache
   - Set appropriate permissions

## Running the Application

You can run the application using PHP's built-in web server for development:

```bash
php -S localhost:8000 -t .
```

Then, open your browser and navigate to `http://localhost:8000`.

For production, it is recommended to use a dedicated web server like Apache or Nginx.

## Usage

1. **Access the application** in your browser
2. **Step 1**: Enter your project name and idea
3. **Step 2**: AI generates comprehensive PRD
4. **Step 3**: AI creates detailed development plan
5. **Step 4**: AI breaks down into phase documents
6. **Download**: Get all documents as organized ZIP file

## Project Structure

```
php/
├── api/                    # API endpoints
│   ├── generate-prd.php
│   ├── generate-plan.php
│   ├── generate-phases.php
│   └── download.php
├── assets/                 # Static assets
│   ├── css/
│   └── js/
├── views/                  # HTML templates
├── config.php             # Configuration
├── index.php              # Main entry point
├── composer.json          # Dependencies
└── README.md              # This file
```

## Generated Documents

Each project generates:
- `prd.md` - Project Requirements Document
- `plan.md` - Development Plan
- `phases.md` - Combined phase documents
- `Phase_1.md`, `Phase_2.md`, etc. - Individual phase files

## API Endpoints

- `POST /api/generate-prd` - Generate PRD
- `POST /api/generate-plan` - Generate development plan
- `POST /api/generate-phases` - Generate phase documents
- `POST /download` - Download project files

## Configuration Options

### AI Settings
- `AI_PROVIDER`: AI service provider (supports 'gemini', 'openai', 'anthropic', 'ollama')
- `AI_API_KEY`: Your API key for the selected provider (not required for Ollama)
- `AI_MODEL`: Model to use for the selected provider
- `AI_TEMPERATURE`: Response creativity (0.0-1.0)
- `AI_MAX_TOKENS`: Maximum response length
- `AI_URL`: Ollama API URL (only required for Ollama provider)

### App Settings
- `APP_NAME`: Application name
- `APP_DEBUG`: Enable debug mode
- `APP_URL`: Base application URL

## Troubleshooting

### Common Issues

1. **API Key Error**: Ensure your API key is valid and has sufficient quota for the selected provider
2. **Permission Error**: Check that `projects/`, `logs/`, and `temp/` directories are writable
3. **Composer Error**: Run `composer install --no-dev` for production
4. **Provider Configuration**: Ensure you've set the correct `AI_PROVIDER` value and corresponding configuration options

### Debug Mode

Enable debug mode in `.env`:
```env
APP_DEBUG=true
```

Check logs in `logs/` directory for detailed error information.

## Security

- API keys are stored in environment variables
- Input validation on all endpoints
- File permissions are restricted
- CSRF protection recommended for production

## License

This project is open source and available under the MIT License.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

class Config {
    private static $instance = null;
    private $config = [];

    private function __construct() {
        $this->loadEnvironment();
        $this->setDefaults();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadEnvironment() {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }

    private function setDefaults() {
        $this->config = [
            'app' => [
                'name' => $_SERVER['APP_NAME'] ?? $_ENV['APP_NAME'] ?? 'Project Planner',
                'version' => '2.0.0',
                'debug' => filter_var($_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'url' => $_SERVER['APP_URL'] ?? $_ENV['APP_URL'] ?? 'http://localhost'
            ],
             'ai' => [
                 'provider' => $_SERVER['AI_PROVIDER'] ?? $_ENV['AI_PROVIDER'] ?? 'gemini',
                 'api_key' => $_SERVER['AI_API_KEY'] ?? $_ENV['AI_API_KEY'] ?? '',
                 'model' => $_SERVER['AI_MODEL'] ?? $_ENV['AI_MODEL'] ?? 'gemini-2.5-flash',
                 'temperature' => floatval($_SERVER['AI_TEMPERATURE'] ?? $_ENV['AI_TEMPERATURE'] ?? 0.7),
                 'max_tokens' => intval($_SERVER['AI_MAX_TOKENS'] ?? $_ENV['AI_MAX_TOKENS'] ?? 15000)
             ],
              'paths' => [
                  'projects' => __DIR__ . '/projects'
              ],
              'graphviz' => [
                  'path' => $_SERVER['GRAPHVIZ_PATH'] ?? $_ENV['GRAPHVIZ_PATH'] ?? 'C:\\Program Files\\Graphviz\\bin\\dot.exe'
              ]
        ];
    }

    public function get($key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function has($key) {
        return $this->get($key) !== null;
    }

    public function set($key, $value) {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }
}

function config($key, $default = null) {
    return Config::getInstance()->get($key, $default);
}

function app() {
    return Config::getInstance();
}
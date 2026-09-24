<?php

namespace Dotenv;

class Dotenv {
    public static function createImmutable($path) {
        return new self($path);
    }
    
    private $path;
    
    public function __construct($path) {
        $this->path = $path;
    }
    
    public function load() {
        $envFile = $this->path . '/.env';
        if (!file_exists($envFile)) {
            return;
        }
        
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Skip lines without =
            if (strpos($line, '=') === false) {
                continue;
            }
            
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            if (!array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
    
    public function safeLoad() {
        try {
            $this->load();
        } catch (\Exception $e) {
            // Ignore errors
        }
    }
}

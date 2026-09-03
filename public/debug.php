<?php
echo "=== DEBUG INFO ===<br>";
echo "ROOT_PATH: " . dirname(__DIR__) . "<br>";
echo "Autoload file exists: " . (file_exists(dirname(__DIR__) . '/vendor/autoload.php') ? 'YES' : 'NO') . "<br>";
echo ".env file exists: " . (file_exists(dirname(__DIR__) . '/.env') ? 'YES' : 'NO') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require dirname(__DIR__) . '/vendor/autoload.php';
    echo "Autoload loaded successfully<br>";
    if (class_exists('Dotenv\Dotenv')) {
        echo "Dotenv class exists<br>";
        try {
            $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
            $dotenv->load();
            echo "Environment loaded successfully<br>";
            echo "APP_NAME: " . ($_ENV['APP_NAME'] ?? 'NOT SET') . "<br>";
        } catch (Exception $e) {
            echo "ERROR loading .env: " . $e->getMessage() . "<br>";
        }
    }
}

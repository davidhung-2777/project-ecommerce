<?php

// Simple Autoloader for DecorNest E-commerce

// Autoload classes from app/ and config/
spl_autoload_register(function ($class) {
    // Chuyển đổi namespace thành đường dẫn file
    $prefix_app = 'App\\';
    $prefix_config = 'Config\\';
    $base_dir_app = __DIR__ . '/../app/';
    $base_dir_config = __DIR__ . '/../config/';

    // Kiểm tra prefix App
    $len = strlen($prefix_app);
    if (strncmp($prefix_app, $class, $len) === 0) {
        $relative_class = substr($class, $len);
        $file = $base_dir_app . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }

    // Kiểm tra prefix Config
    $len = strlen($prefix_config);
    if (strncmp($prefix_config, $class, $len) === 0) {
        $relative_class = substr($class, $len);
        $file = $base_dir_config . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

// Load Dotenv class
require_once __DIR__ . '/dotenv.php';

return true;

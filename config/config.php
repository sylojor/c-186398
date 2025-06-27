
<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'php_ai_website');
define('DB_USER', 'root');
define('DB_PASS', '');

// AI API configuration
define('OPENAI_API_KEY', 'your-openai-api-key-here');
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');

// Site configuration
define('SITE_NAME', 'PHP AI Website');
define('SITE_URL', 'http://localhost');

// Upload configuration
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');

// Include required files
require_once 'database.php';
require_once '../classes/User.php';
require_once '../classes/AIChat.php';
require_once '../classes/FileManager.php';
require_once '../includes/functions.php';
?>

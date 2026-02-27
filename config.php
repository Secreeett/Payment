<?php
/**
 * Payment System Configuration
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'payment_system');

// Application Configuration
define('BASE_URL', 'http://localhost/Payment/');
define('APP_NAME', 'Payment System');
define('APP_VERSION', '1.0.0');

// Paths
define('ROOT_PATH', __DIR__);
define('DOCUMENTS_PATH', ROOT_PATH . '/Documents/');
define('EXCEL_TEMPLATE', DOCUMENTS_PATH . 'PAYMENT FORM NEW BUILDING.xlsx');
define('PDF_EXPORT_PATH', DOCUMENTS_PATH . 'pdfs/');

// Create directories if they don't exist
if (!file_exists(DOCUMENTS_PATH)) {
    mkdir(DOCUMENTS_PATH, 0777, true);
}
if (!file_exists(PDF_EXPORT_PATH)) {
    mkdir(PDF_EXPORT_PATH, 0777, true);
}

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Manila');

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/Controllers/',
        ROOT_PATH . '/Models/',
        ROOT_PATH . '/Core/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load ProjectTypes class
if (file_exists(ROOT_PATH . '/Core/ProjectTypes.php')) {
    require_once ROOT_PATH . '/Core/ProjectTypes.php';
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


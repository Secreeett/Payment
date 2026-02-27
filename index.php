<?php
/**
 * Payment System - Main Router
 */

// Load configuration
require_once __DIR__ . '/config.php';

// Initialize routing
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Handle login/logout separately
if ($page === 'login') {
    $authController = new AuthController();
    $authController->login();
    exit;
}

if ($page === 'logout') {
    $authController = new AuthController();
    $authController->logout();
    exit;
}

// Check authentication for all other pages
$authController = new AuthController();
$authController->checkAuth();

// Route to appropriate controller
switch ($page) {
    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;
        
    case 'payment':
        $controller = new PaymentController();
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'view':
                $controller->view();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'search':
                $controller->search();
                break;
            case 'markAsPaid':
                $controller->markAsPaid();
                break;
            case 'calculator':
                $controller->calculator();
                break;
            default:
                $controller->index();
        }
        break;
        
    case 'pdf':
        $controller = new PDFController();
        switch ($action) {
            case 'excel':
                $controller->exportExcel();
                break;
            default:
                header('Location: ' . BASE_URL . '?page=dashboard');
                exit;
        }
        break;
        
    case 'admin':
        $authController->checkRole(['admin']);
        // Admin functionality can be added here
        header('Location: ' . BASE_URL . '?page=dashboard');
        exit;
        
    default:
        header('Location: ' . BASE_URL . '?page=dashboard');
        exit;
}


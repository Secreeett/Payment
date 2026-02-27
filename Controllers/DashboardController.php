<?php
/**
 * Dashboard Controller
 */
class DashboardController {
    private $paymentModel;
    private $authController;
    
    public function __construct() {
        $this->paymentModel = new Payment();
        $this->authController = new AuthController();
        $this->authController->checkAuth();
    }
    
    public function index() {
        $payments = $this->paymentModel->getAllPaymentForms(10);
        // Pass payments data to view
        $GLOBALS['payments'] = $payments;
        require_once ROOT_PATH . '/Views/Pages/Dashboard.php';
    }
}


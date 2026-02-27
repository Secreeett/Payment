<?php
/**
 * Authentication Controller
 */
class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $this->userModel->login($username, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                
                header('Location: ' . BASE_URL . '?page=dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Invalid username or password';
                header('Location: ' . BASE_URL . '?page=login');
                exit;
            }
        }
        
        require_once ROOT_PATH . '/Views/User/Layouts/Login.php';
    }
    
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '?page=login');
        exit;
    }
    
    public function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?page=login');
            exit;
        }
    }
    
    public function checkRole($allowedRoles = []) {
        $this->checkAuth();
        if (!empty($allowedRoles) && !in_array($_SESSION['role'], $allowedRoles)) {
            header('Location: ' . BASE_URL . '?page=dashboard');
            exit;
        }
    }
}


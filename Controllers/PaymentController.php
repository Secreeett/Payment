<?php
/**
 * Payment Controller
 */
class PaymentController {
    private $paymentModel;
    private $authController;
    
    public function __construct() {
        $this->paymentModel = new Payment();
        $this->authController = new AuthController();
        $this->authController->checkAuth();
    }
    
    public function index() {
        $payments = $this->paymentModel->getAllPaymentForms();
        // Pass payments data to view
        $GLOBALS['payments'] = $payments;
        require_once ROOT_PATH . '/Views/Pages/PaymentList.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once ROOT_PATH . '/Core/ProjectTypes.php';
            
            $floorArea = floatval($_POST['floor_area'] ?? 0);
            $additionalLotArea = floatval($_POST['additional_lot_area'] ?? 0);
            // Use provided total_area or calculate if not provided
            $totalArea = !empty($_POST['total_area']) ? floatval($_POST['total_area']) : ($floorArea + $additionalLotArea);
            $surchargePercentage = intval($_POST['surcharge_percentage'] ?? 0);
            $projectType = $_POST['project_type'] ?? null;
            $projectCost = floatval($_POST['project_cost'] ?? 0);
            $multiplier = floatval($_POST['multiplier'] ?? 0);
            $calculatedCost = floatval($_POST['calculated_cost'] ?? 0);
            
            // Extract floor and lot multipliers and costs
            $floorMultiplier = floatval($_POST['floor_multiplier'] ?? 0);
            $lotMultiplier = floatval($_POST['lot_multiplier'] ?? 0);
            $floorAreaCost = floatval($_POST['floor_area_cost'] ?? 0);
            $lotAreaCost = floatval($_POST['lot_area_cost'] ?? 0);
            
            // Extract locational fees
            $lotLocationalFee = floatval($_POST['lot_locational_fee'] ?? 0);
            $floorLocationalFee = floatval($_POST['floor_locational_fee'] ?? 0);
            $grandTotalLocational = floatval($_POST['grand_total_locational'] ?? 0);
            
            // Extract additional fees
            $fillingFees = floatval($_POST['filling_fees'] ?? 50.00);
            $preliminaryInspectionFees = floatval($_POST['preliminary_inspection_fees'] ?? 1500.00);
            $lineAndGradeFees = floatval($_POST['line_and_grade_fees'] ?? 0);
            $zoningFees = floatval($_POST['zoning_fees'] ?? 720.00);
            $esfFees = floatval($_POST['esf_fees'] ?? 20.00);
            $developmentFees = floatval($_POST['development_fees'] ?? 0);
            $certificationFees = floatval($_POST['certification_fees'] ?? 0);
            
            // Parse and calculate fees
            $parsedFees = $this->parseFees($_POST);
            
            // Add the new fixed fees to the parsed fees if they're not already there
            $additionalFees = [
                ['name' => 'Filling fees', 'amount' => $fillingFees],
                ['name' => 'Preliminary inspection & verification fees', 'amount' => $preliminaryInspectionFees],
                ['name' => 'Locational clearance', 'amount' => $grandTotalLocational],
                ['name' => 'Line and grade fees', 'amount' => $lineAndGradeFees],
                ['name' => 'Zoning fees', 'amount' => $zoningFees],
                ['name' => 'ESF', 'amount' => $esfFees],
                ['name' => 'Development Fees', 'amount' => $developmentFees],
                ['name' => 'Certification', 'amount' => $certificationFees]
            ];
            
            // Merge additional fees with parsed fees (avoid duplicates)
            $existingFeeNames = array_map(function($fee) { return strtolower(trim($fee['name'])); }, $parsedFees);
            foreach ($additionalFees as $additionalFee) {
                $feeNameLower = strtolower(trim($additionalFee['name']));
                if (!in_array($feeNameLower, $existingFeeNames) && $additionalFee['amount'] > 0) {
                    $parsedFees[] = $additionalFee;
                }
            }
            
            $totalFees = 0;
            foreach ($parsedFees as $fee) {
                $totalFees += $fee['amount'];
            }
            $surchargeAmount = $totalFees * ($surchargePercentage / 100);
            $grandTotal = $totalFees + $surchargeAmount;
            
            $data = [
                'owner_applicant_name' => $_POST['owner_applicant_name'] ?? '',
                'project_title' => $_POST['project_title'] ?? '',
                'location' => $_POST['location'] ?? '',
                'date' => $_POST['date'] ?? date('Y-m-d'),
                'division' => $_POST['division'] ?? '',
                'project_type' => $projectType,
                'project_cost' => $projectCost,
                'multiplier' => $multiplier,
                'calculated_cost' => $calculatedCost,
                'floor_area' => $floorArea,
                'floor_multiplier' => $floorMultiplier,
                'floor_area_cost' => $floorAreaCost,
                'additional_lot_area' => $additionalLotArea,
                'lot_multiplier' => $lotMultiplier,
                'lot_area_cost' => $lotAreaCost,
                'lot_locational_fee' => $lotLocationalFee,
                'floor_locational_fee' => $floorLocationalFee,
                'grand_total_locational' => $grandTotalLocational,
                'total_area' => $totalArea,
                'total_fees' => $totalFees,
                'surcharge_percentage' => $surchargePercentage,
                'surcharge_amount' => $surchargeAmount,
                'grand_total' => $grandTotal,
                'prepared_by' => 'LOVELY LAXA',
                'assessed_by' => 'ENP. Mark Andrei L. Gubac',
                'created_by' => $_SESSION['user_id'],
                'status' => $_POST['status'] ?? 'draft',
                'fees' => $parsedFees
            ];
            
            $paymentId = $this->paymentModel->createPaymentForm($data);
            
            if ($paymentId) {
                $_SESSION['success'] = 'Payment form created successfully';
                header('Location: ' . BASE_URL . '?page=payment&action=view&id=' . $paymentId);
                exit;
            } else {
                $_SESSION['error'] = 'Failed to create payment form';
            }
        }
        
        require_once ROOT_PATH . '/Views/Pages/PaymentForm.php';
    }
    
    public function view() {
        $id = $_GET['id'] ?? 0;
        $payment = $this->paymentModel->getPaymentForm($id);
        
        if (!$payment) {
            $_SESSION['error'] = 'Payment form not found';
            header('Location: ' . BASE_URL . '?page=payment');
            exit;
        }
        
        // Pass payment data to view
        $GLOBALS['payment'] = $payment;
        require_once ROOT_PATH . '/Views/Pages/PaymentView.php';
    }
    
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $payment = $this->paymentModel->getPaymentForm($id);
        
        if (!$payment) {
            $_SESSION['error'] = 'Payment form not found';
            header('Location: ' . BASE_URL . '?page=payment');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once ROOT_PATH . '/Core/ProjectTypes.php';
            
            $floorArea = floatval($_POST['floor_area'] ?? 0);
            $additionalLotArea = floatval($_POST['additional_lot_area'] ?? 0);
            // Use provided total_area or calculate if not provided
            $totalArea = !empty($_POST['total_area']) ? floatval($_POST['total_area']) : ($floorArea + $additionalLotArea);
            $surchargePercentage = intval($_POST['surcharge_percentage'] ?? 0);
            $projectType = $_POST['project_type'] ?? null;
            $projectCost = floatval($_POST['project_cost'] ?? 0);
            $multiplier = floatval($_POST['multiplier'] ?? 0);
            $calculatedCost = floatval($_POST['calculated_cost'] ?? 0);
            
            // Extract floor and lot multipliers and costs
            $floorMultiplier = floatval($_POST['floor_multiplier'] ?? 0);
            $lotMultiplier = floatval($_POST['lot_multiplier'] ?? 0);
            $floorAreaCost = floatval($_POST['floor_area_cost'] ?? 0);
            $lotAreaCost = floatval($_POST['lot_area_cost'] ?? 0);
            
            // Extract locational fees
            $lotLocationalFee = floatval($_POST['lot_locational_fee'] ?? 0);
            $floorLocationalFee = floatval($_POST['floor_locational_fee'] ?? 0);
            $grandTotalLocational = floatval($_POST['grand_total_locational'] ?? 0);
            
            // Extract additional fees
            $fillingFees = floatval($_POST['filling_fees'] ?? 50.00);
            $preliminaryInspectionFees = floatval($_POST['preliminary_inspection_fees'] ?? 1500.00);
            $lineAndGradeFees = floatval($_POST['line_and_grade_fees'] ?? 0);
            $zoningFees = floatval($_POST['zoning_fees'] ?? 720.00);
            $esfFees = floatval($_POST['esf_fees'] ?? 20.00);
            $developmentFees = floatval($_POST['development_fees'] ?? 0);
            $certificationFees = floatval($_POST['certification_fees'] ?? 0);
            
            // Parse and calculate fees
            $parsedFees = $this->parseFees($_POST);
            
            // Add the new fixed fees to the parsed fees if they're not already there
            $additionalFees = [
                ['name' => 'Filling fees', 'amount' => $fillingFees],
                ['name' => 'Preliminary inspection & verification fees', 'amount' => $preliminaryInspectionFees],
                ['name' => 'Locational clearance', 'amount' => $grandTotalLocational],
                ['name' => 'Line and grade fees', 'amount' => $lineAndGradeFees],
                ['name' => 'Zoning fees', 'amount' => $zoningFees],
                ['name' => 'ESF', 'amount' => $esfFees],
                ['name' => 'Development Fees', 'amount' => $developmentFees],
                ['name' => 'Certification', 'amount' => $certificationFees]
            ];
            
            // Merge additional fees with parsed fees (avoid duplicates)
            $existingFeeNames = array_map(function($fee) { return strtolower(trim($fee['name'])); }, $parsedFees);
            foreach ($additionalFees as $additionalFee) {
                $feeNameLower = strtolower(trim($additionalFee['name']));
                if (!in_array($feeNameLower, $existingFeeNames) && $additionalFee['amount'] > 0) {
                    $parsedFees[] = $additionalFee;
                }
            }
            
            $totalFees = 0;
            foreach ($parsedFees as $fee) {
                $totalFees += $fee['amount'];
            }
            $surchargeAmount = $totalFees * ($surchargePercentage / 100);
            $grandTotal = $totalFees + $surchargeAmount;
            
            $data = [
                'owner_applicant_name' => $_POST['owner_applicant_name'] ?? '',
                'project_title' => $_POST['project_title'] ?? '',
                'location' => $_POST['location'] ?? '',
                'date' => $_POST['date'] ?? date('Y-m-d'),
                'division' => $_POST['division'] ?? '',
                'project_type' => $projectType,
                'project_cost' => $projectCost,
                'multiplier' => $multiplier,
                'calculated_cost' => $calculatedCost,
                'floor_area' => $floorArea,
                'floor_multiplier' => $floorMultiplier,
                'floor_area_cost' => $floorAreaCost,
                'additional_lot_area' => $additionalLotArea,
                'lot_multiplier' => $lotMultiplier,
                'lot_area_cost' => $lotAreaCost,
                'lot_locational_fee' => $lotLocationalFee,
                'floor_locational_fee' => $floorLocationalFee,
                'grand_total_locational' => $grandTotalLocational,
                'total_area' => $totalArea,
                'total_fees' => $totalFees,
                'surcharge_percentage' => $surchargePercentage,
                'surcharge_amount' => $surchargeAmount,
                'grand_total' => $grandTotal,
                'prepared_by' => 'LOVELY LAXA',
                'assessed_by' => 'ENP. Mark Andrei L. Gubac',
                'status' => $_POST['status'] ?? 'draft',
                'fees' => $parsedFees
            ];
            
            if ($this->paymentModel->updatePaymentForm($id, $data)) {
                $_SESSION['success'] = 'Payment form updated successfully';
                header('Location: ' . BASE_URL . '?page=payment&action=view&id=' . $id);
                exit;
            } else {
                $_SESSION['error'] = 'Failed to update payment form';
            }
        }
        
        // Pass payment data to view
        $GLOBALS['payment'] = $payment;
        require_once ROOT_PATH . '/Views/Pages/PaymentForm.php';
    }
    
    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        if ($this->paymentModel->deletePaymentForm($id)) {
            $_SESSION['success'] = 'Payment form deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete payment form';
        }
        
        header('Location: ' . BASE_URL . '?page=payment');
        exit;
    }
    
    public function search() {
        $searchTerm = $_GET['search'] ?? '';
        $payments = [];
        
        if (!empty($searchTerm)) {
            $payments = $this->paymentModel->searchPaymentForms($searchTerm);
        } else {
            $payments = $this->paymentModel->getAllPaymentForms();
        }
        
        // Pass payments data to view
        $GLOBALS['payments'] = $payments;
        require_once ROOT_PATH . '/Views/Pages/PaymentList.php';
    }
    
    public function markAsPaid() {
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'Invalid payment form ID';
            header('Location: ' . BASE_URL . '?page=payment');
            exit;
        }
        
        // Check if payment exists
        $payment = $this->paymentModel->getPaymentForm($id);
        if (!$payment) {
            $_SESSION['error'] = 'Payment form not found';
            header('Location: ' . BASE_URL . '?page=payment');
            exit;
        }
        
        // Check if already paid
        if ($payment['status'] === 'paid') {
            $_SESSION['error'] = 'This payment form is already marked as paid';
            header('Location: ' . BASE_URL . '?page=payment&action=view&id=' . $id);
            exit;
        }
        
        if ($this->paymentModel->markAsPaid($id)) {
            $_SESSION['success'] = 'Payment form marked as paid successfully';
        } else {
            $_SESSION['error'] = 'Failed to mark payment form as paid';
        }
        
        // Redirect back to the page that called this action
        $redirect = $_GET['redirect'] ?? 'payment';
        if ($redirect === 'dashboard') {
            header('Location: ' . BASE_URL . '?page=dashboard');
        } else {
            header('Location: ' . BASE_URL . '?page=payment&action=view&id=' . $id);
        }
        exit;
    }
    
    public function calculator() {
        // Display the area and cost calculator form
        require_once ROOT_PATH . '/Views/Pages/AreaCostCalculator.php';
    }
    
    private function parseFees($postData) {
        $fees = [];
        
        if (isset($postData['fee_name']) && is_array($postData['fee_name'])) {
            foreach ($postData['fee_name'] as $index => $feeName) {
                if (!empty($feeName) && isset($postData['fee_amount'][$index])) {
                    $fees[] = [
                        'name' => $feeName,
                        'amount' => floatval($postData['fee_amount'][$index])
                    ];
                }
            }
        }
        
        return $fees;
    }
}


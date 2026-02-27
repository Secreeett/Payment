<?php
require_once ROOT_PATH . '/Views/User/Layouts/Header.php';

// Get payment data from global scope (set by controller)
$payment = $GLOBALS['payment'] ?? null;

if (!$payment) {
    header('Location: ' . BASE_URL . '?page=payment');
    exit;
}
?>

<style>
    .page-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }
    
    .page-header h2 {
        margin: 0;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .content-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-header-custom h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }
    
    .info-section {
        padding: 2rem;
    }
    
    .info-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    .info-group {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }
    
    .info-group h6 {
        color: #667eea;
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 500;
        color: #6c757d;
        flex: 0 0 45%;
    }
    
    .info-value {
        color: #2c3e50;
        font-weight: 500;
        text-align: right;
        flex: 1;
    }
    
    .info-value.highlight {
        color: #667eea;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .fee-table-section {
        padding: 2rem;
        border-top: 1px solid #e9ecef;
    }
    
    .fee-table-section h5 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #667eea;
    }
    
    .table-custom {
        margin: 0;
    }
    
    .table-custom thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .table-custom thead th {
        border: none;
        padding: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .table-custom tbody tr {
        transition: background-color 0.2s;
    }
    
    .table-custom tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .table-custom tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s;
    }
    
    .btn-action i {
        font-size: 1rem;
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.875rem;
    }
    
    @media (max-width: 768px) {
        .info-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .action-buttons {
            width: 100%;
        }
        
        .action-buttons .btn {
            flex: 1;
        }
    }
</style>

<div class="page-container">
    <div class="page-header">
        <h2><i class="bi bi-file-earmark-text"></i> Payment Form Details</h2>
        <div class="action-buttons">
            <a href="<?php echo BASE_URL; ?>?page=pdf&action=excel&id=<?php echo $payment['id']; ?>" 
               class="btn btn-success btn-action">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <?php if ($payment['status'] !== 'paid'): ?>
                <a href="<?php echo BASE_URL; ?>?page=payment&action=markAsPaid&id=<?php echo $payment['id']; ?>" 
                   class="btn btn-success btn-action" 
                   onclick="return confirm('Mark this payment as paid? This action cannot be undone.')">
                    <i class="bi bi-check-circle"></i> Mark as Paid
                </a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>?page=payment&action=edit&id=<?php echo $payment['id']; ?>" 
               class="btn btn-warning btn-action">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="<?php echo BASE_URL; ?>?page=payment&action=delete&id=<?php echo $payment['id']; ?>" 
               class="btn btn-danger btn-action" 
               onclick="return confirm('Are you sure you want to delete this payment form?')">
                <i class="bi bi-trash"></i> Delete
            </a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="content-card">
        <div class="card-header-custom">
            <h5><i class="bi bi-receipt"></i> Order of Payment - <?php echo htmlspecialchars($payment['official_receipt_no']); ?></h5>
            <span class="status-badge badge bg-<?php echo $payment['status'] === 'approved' ? 'success' : ($payment['status'] === 'paid' ? 'primary' : 'warning'); ?>">
                <?php echo ucfirst($payment['status']); ?>
            </span>
        </div>
        
        <div class="info-section">
            <div class="info-row">
                <div class="info-group">
                    <h6><i class="bi bi-info-circle"></i> General Information</h6>
                    <div class="info-item">
                        <span class="info-label">Official Receipt No.:</span>
                        <span class="info-value"><?php echo htmlspecialchars($payment['official_receipt_no']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date:</span>
                        <span class="info-value"><?php echo date('F d, Y', strtotime($payment['date'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Owner/Applicant Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($payment['owner_applicant_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Project Title:</span>
                        <span class="info-value"><?php echo htmlspecialchars($payment['project_title']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Location:</span>
                        <span class="info-value"><?php echo htmlspecialchars($payment['location']); ?></span>
                    </div>
                    <?php if (!empty($payment['division'])): ?>
                    <div class="info-item">
                        <span class="info-label">Division:</span>
                        <span class="info-value"><?php echo htmlspecialchars($payment['division']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($payment['project_type'])): 
                        require_once ROOT_PATH . '/Core/ProjectTypes.php';
                        $projectType = ProjectTypes::getProjectType($payment['project_type']);
                        if ($projectType): ?>
                        <div class="info-item">
                            <span class="info-label">Project Type:</span>
                            <span class="info-value"><?php echo htmlspecialchars($payment['project_type'] . '. ' . $projectType['name']); ?></span>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (!empty($payment['project_cost']) && $payment['project_cost'] > 0): ?>
                    <div class="info-item">
                        <span class="info-label">Project Cost:</span>
                        <span class="info-value highlight">₱ <?php echo number_format($payment['project_cost'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="info-group">
                    <h6><i class="bi bi-calculator"></i> Area and Cost Calculation</h6>
                    <div class="info-item">
                        <span class="info-label">Floor Area:</span>
                        <span class="info-value"><?php echo number_format($payment['floor_area'], 2); ?> sq.m.</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Additional Lot Area:</span>
                        <span class="info-value"><?php echo number_format($payment['additional_lot_area'], 2); ?> sq.m.</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Area:</span>
                        <span class="info-value"><?php echo number_format($payment['total_area'], 2); ?> sq.m.</span>
                    </div>
                    <?php if (!empty($payment['multiplier']) && $payment['multiplier'] > 0): ?>
                    <div class="info-item">
                        <span class="info-label">Multiplier:</span>
                        <span class="info-value">₱ <?php echo number_format($payment['multiplier'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($payment['calculated_cost']) && $payment['calculated_cost'] > 0): ?>
                    <div class="info-item">
                        <span class="info-label">Calculated Cost:</span>
                        <span class="info-value highlight">₱ <?php echo number_format($payment['calculated_cost'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-item">
                        <span class="info-label">Total Fees:</span>
                        <span class="info-value highlight">₱ <?php echo number_format($payment['total_fees'], 2); ?></span>
                    </div>
                    <?php if ($payment['surcharge_percentage'] > 0): ?>
                    <div class="info-item">
                        <span class="info-label">Surcharge (<?php echo $payment['surcharge_percentage']; ?>%):</span>
                        <span class="info-value">₱ <?php echo number_format($payment['surcharge_amount'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-item">
                        <span class="info-label">Grand Total:</span>
                        <span class="info-value highlight" style="color: #28a745; font-size: 1.2rem;">₱ <?php echo number_format($payment['grand_total'], 2); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Prepared By:</span>
                        <span class="info-value"><?php echo htmlspecialchars($payment['prepared_by']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Assessed By:</span>
                        <span class="info-value"><?php echo htmlspecialchars($payment['assessed_by']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="fee-table-section">
            <h5><i class="bi bi-list-ul"></i> Fee Breakdown</h5>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Fee Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Filter to show only Additional Fees
                        $additionalFeeNames = [
                            'filing fees', 'filing fee',
                            'preliminary inspection & verification fees', 'preliminary inspection and verification fees',
                            'locational clearance',
                            'line and grade fees', 'line and grade fee',
                            'zoning fees', 'zoning fee',
                            'esf',
                            'development fees', 'development fee',
                            'certification', 'certification fee'
                        ];
                        
                        $filteredFees = [];
                        if (!empty($payment['fees'])) {
                            foreach ($payment['fees'] as $fee) {
                                $feeNameLower = strtolower(trim($fee['fee_name'] ?? ''));
                                foreach ($additionalFeeNames as $additionalName) {
                                    if (strpos($feeNameLower, $additionalName) !== false || strpos($additionalName, $feeNameLower) !== false) {
                                        $filteredFees[] = $fee;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        if (!empty($filteredFees)): ?>
                            <?php foreach ($filteredFees as $index => $fee): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($fee['fee_name']); ?></td>
                                    <td class="text-end"><strong>₱ <?php echo number_format($fee['fee_amount'], 2); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 2rem; color: #dee2e6;"></i>
                                    <p class="text-muted mt-2 mb-0">No additional fees</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
require_once ROOT_PATH . '/Views/User/Layouts/Footer.php';
?>


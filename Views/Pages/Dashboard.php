<?php
require_once ROOT_PATH . '/Views/User/Layouts/Header.php';

// Get payments data from global scope (set by controller)
$payments = $GLOBALS['payments'] ?? [];
?>

<style>
    .page-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }
    .welcome-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        color: white;
    }
    .welcome-section h2 {
        margin: 0 0 0.5rem 0;
        font-weight: 600;
        color: white;
    }
    .welcome-section p {
        margin: 0;
        opacity: 0.95;
    }
    .content-card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-radius: 10px;
        overflow: hidden;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1.25rem 1.5rem;
    }
    .card-header h5 {
        margin: 0;
        font-weight: 600;
        color: #212529;
    }
    .card-body {
        padding: 1.5rem;
    }
    .table {
        margin-bottom: 0;
    }
    .table thead th {
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        padding: 1rem;
        background-color: #f8f9fa;
    }
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    .empty-state i {
        font-size: 3rem;
        color: #dee2e6;
        display: block;
        margin-bottom: 1rem;
    }
    @media (max-width: 768px) {
        .page-container {
            padding: 1rem;
        }
    }
</style>

<div class="page-container">
    <div class="welcome-section">
        <h2>Dashboard</h2>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>!</p>
    </div>
    
    <div class="card content-card">
        <div class="card-header">
            <h5>Recent Payment Forms</h5>
        </div>
        <div class="card-body">
            <?php if (empty($payments)): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p class="text-muted mb-3">No payment forms found.</p>
                    <a href="<?php echo BASE_URL; ?>?page=payment&action=create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create one now
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Receipt No.</th>
                                        <th>Owner/Applicant</th>
                                        <th>Project Title</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($payment['official_receipt_no']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['owner_applicant_name']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['project_title']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($payment['date'])); ?></td>
                                            <td>₱ <?php echo number_format($payment['grand_total'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $payment['status'] === 'approved' ? 'success' : ($payment['status'] === 'paid' ? 'primary' : 'warning'); ?>">
                                                    <?php echo ucfirst($payment['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="<?php echo BASE_URL; ?>?page=payment&action=view&id=<?php echo $payment['id']; ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                    <a href="<?php echo BASE_URL; ?>?page=pdf&action=excel&id=<?php echo $payment['id']; ?>" class="btn btn-sm btn-success">
                                                        <i class="bi bi-file-earmark-excel"></i> Excel
                                                    </a>
                                                    <?php if ($payment['status'] !== 'paid'): ?>
                                                        <a href="<?php echo BASE_URL; ?>?page=payment&action=markAsPaid&id=<?php echo $payment['id']; ?>&redirect=dashboard" 
                                                           class="btn btn-sm btn-success" 
                                                           onclick="return confirm('Mark this payment as paid? This action cannot be undone.')">
                                                            <i class="bi bi-check-circle"></i> Mark as Paid
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
</div>

<?php
require_once ROOT_PATH . '/Views/User/Layouts/Footer.php';
?>


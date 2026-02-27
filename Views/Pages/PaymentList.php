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
    .page-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }
    .page-header h2 {
        margin: 0;
        font-weight: 600;
        color: #212529;
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
    .btn-action-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
    }
    @media (max-width: 768px) {
        .page-container {
            padding: 1rem;
        }
        .page-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start !important;
        }
    }
</style>

<div class="page-container">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h2>Payment Forms</h2>
        <a href="<?php echo BASE_URL; ?>?page=payment&action=create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Payment Form
        </a>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="card content-card">
        <div class="card-header">
            <form method="GET" action="<?php echo BASE_URL; ?>?page=payment&action=search" class="d-flex gap-2">
                <input type="hidden" name="page" value="payment">
                <input type="hidden" name="action" value="search">
                <input type="text" class="form-control" name="search" 
                       placeholder="Search by name, project, or receipt number..." 
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="<?php echo BASE_URL; ?>?page=payment" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </form>
        </div>
        <div class="card-body">
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
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payments)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6; display: block; margin-bottom: 1rem;"></i>
                                            <p class="text-muted mb-0">No payment forms found</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
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
                                            <td><?php echo htmlspecialchars($payment['created_by_name'] ?? 'N/A'); ?></td>
                                            <td>
                                                <div class="btn-action-group">
                                                    <a href="<?php echo BASE_URL; ?>?page=payment&action=view&id=<?php echo $payment['id']; ?>" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                    <a href="<?php echo BASE_URL; ?>?page=pdf&action=excel&id=<?php echo $payment['id']; ?>" 
                                                        class="btn btn-sm btn-success">
                                                        <i class="bi bi-file-earmark-excel"></i> Excel
                                                    </a>
                                                    <?php if ($payment['status'] !== 'paid'): ?>
                                                        <a href="<?php echo BASE_URL; ?>?page=payment&action=markAsPaid&id=<?php echo $payment['id']; ?>" 
                                                           class="btn btn-sm btn-success" 
                                                           onclick="return confirm('Mark this payment as paid? This action cannot be undone.')">
                                                            <i class="bi bi-check-circle"></i> Mark as Paid
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $payment['created_by'] == $_SESSION['user_id'])): ?>
                                                        <a href="<?php echo BASE_URL; ?>?page=payment&action=edit&id=<?php echo $payment['id']; ?>" 
                                                           class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                        <a href="<?php echo BASE_URL; ?>?page=payment&action=delete&id=<?php echo $payment['id']; ?>" 
                                                           class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this payment form?')">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>

<?php
require_once ROOT_PATH . '/Views/User/Layouts/Footer.php';
?>


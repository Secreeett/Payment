<?php
require_once ROOT_PATH . '/Views/User/Layouts/Header.php';
require_once ROOT_PATH . '/Core/ProjectTypes.php';

// Get payment data from global scope (set by controller)
$payment = $GLOBALS['payment'] ?? null;
$isEdit = isset($payment) && !empty($payment);
$payment = $isEdit ? $payment : [
    'owner_applicant_name' => '',
    'project_title' => '',
    'location' => '',
    'date' => date('Y-m-d'),
    'division' => '',
    'project_type' => '',
    'project_cost' => '',
    'multiplier' => '',
    'calculated_cost' => '',
    'floor_area' => '',
    'additional_lot_area' => '',
    'total_area' => '',
    'total_fees' => 0,
    'surcharge_percentage' => 0,
    'surcharge_amount' => 0,
    'grand_total' => 0,
    'prepared_by' => 'LOVELY LAXA',
    'assessed_by' => 'ENP. Mark Andrei L. Gubac',
    'fees' => []
];

$fees = $payment['fees'] ?? [];
if (empty($fees)) {
    $fees = [['fee_name' => '', 'fee_amount' => 0]];
}

$projectTypes = ProjectTypes::getProjectTypes();
?>

<style>
.payment-form-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.form-section-box {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-section-title {
    font-size: 16px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #007bff;
}

.form-group-compact {
    margin-bottom: 15px;
}

.form-label-compact {
    font-size: 13px;
    font-weight: 500;
    color: #495057;
    margin-bottom: 5px;
}

.form-control-compact {
    font-size: 14px;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.project-type-option {
    padding: 12px;
    margin: 5px 0;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s;
    background: #fff;
}

.project-type-option:hover {
    border-color: #007bff;
    background: #f8f9fa;
}

.project-type-option.selected {
    border-color: #007bff;
    background: #e7f3ff;
    box-shadow: 0 2px 4px rgba(0,123,255,0.2);
}

.project-type-option input[type="radio"] {
    margin-right: 10px;
}

.calculation-box {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 15px;
    margin-top: 10px;
}

.calculation-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #dee2e6;
}

.calculation-row:last-child {
    border-bottom: none;
    font-weight: 600;
    color: #007bff;
}

.fee-table-compact {
    font-size: 13px;
}

.fee-table-compact th {
    background: #f8f9fa;
    font-weight: 600;
    padding: 10px;
}

.fee-table-compact td {
    padding: 8px;
}

.summary-box {
    background: #e7f3ff;
    border: 2px solid #007bff;
    border-radius: 6px;
    padding: 15px;
    margin-top: 15px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
}

.summary-label {
    font-weight: 600;
    color: #495057;
}

.summary-value {
    font-weight: 700;
    color: #007bff;
    font-size: 16px;
}
</style>

<div class="payment-form-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?php echo $isEdit ? 'Edit' : 'Create'; ?> Payment Form</h3>
        <a href="<?php echo BASE_URL; ?>?page=dashboard" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo BASE_URL; ?>?page=payment&action=<?php echo $isEdit ? 'edit&id=' . $payment['id'] : 'create'; ?>" id="paymentForm">
        
        <!-- Basic Information -->
        <div class="form-section-box">
            <div class="form-section-title">
                <i class="bi bi-info-circle"></i> Basic Information
            </div>
            <div class="row">
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Owner/Applicant Name *</label>
                    <input type="text" class="form-control form-control-compact" id="owner_applicant_name" 
                           name="owner_applicant_name" value="<?php echo htmlspecialchars($payment['owner_applicant_name']); ?>" required>
                </div>
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Project Title *</label>
                    <input type="text" class="form-control form-control-compact" id="project_title" 
                           name="project_title" value="<?php echo htmlspecialchars($payment['project_title']); ?>" required>
                </div>
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Location *</label>
                    <input type="text" class="form-control form-control-compact" id="location" 
                           name="location" value="<?php echo htmlspecialchars($payment['location']); ?>" required>
                </div>
                <div class="col-md-3 form-group-compact">
                    <label class="form-label-compact">Date *</label>
                    <input type="date" class="form-control form-control-compact" id="date" 
                           name="date" value="<?php echo $payment['date']; ?>" required>
                </div>
                <div class="col-md-3 form-group-compact">
                    <label class="form-label-compact">Division</label>
                    <input type="text" class="form-control form-control-compact" id="division" 
                           name="division" value="<?php echo htmlspecialchars($payment['division']); ?>">
                </div>
            </div>
        </div>
        
        <!-- Additional Fees Section -->
        <div class="form-section-box">
            <div class="form-section-title">
                <i class="bi bi-cash-stack"></i> Additional Fees
            </div>
            <div class="row">
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Filling fees</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="filling_fees" 
                           name="filling_fees" value="<?php echo $payment['filling_fees'] ?? 50.00; ?>" 
                           oninput="updateAdditionalFees()">
                </div>
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Preliminary inspection & verification fees</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="preliminary_inspection_fees" 
                           name="preliminary_inspection_fees" value="<?php echo $payment['preliminary_inspection_fees'] ?? 1500.00; ?>" 
                           oninput="updateAdditionalFees()">
                </div>
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Locational clearance</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="locational_clearance_display" 
                           name="locational_clearance_display" value="<?php echo $payment['grand_total_locational'] ?? 0; ?>" 
                           readonly style="background-color: #f8f9fa; font-weight: bold;">
                    <input type="hidden" id="locational_clearance_fee" name="locational_clearance_fee" value="<?php echo $payment['grand_total_locational'] ?? 0; ?>">
                    <small class="text-muted">Auto-calculated from Floor Area and Lot Area</small>
                </div>
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Line and grade fees</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="line_and_grade_fees" 
                           name="line_and_grade_fees" value="<?php echo $payment['line_and_grade_fees'] ?? 0; ?>" 
                           oninput="updateAdditionalFees()">
                </div>
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Zoning fees</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="zoning_fees" 
                           name="zoning_fees" value="<?php echo $payment['zoning_fees'] ?? 720.00; ?>" 
                           oninput="updateAdditionalFees()">
                </div>
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">ESF</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="esf_fees" 
                           name="esf_fees" value="<?php echo $payment['esf_fees'] ?? 20.00; ?>" 
                           oninput="updateAdditionalFees()">
                </div>
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Development Fees</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="development_fees" 
                           name="development_fees" value="<?php echo $payment['development_fees'] ?? 0; ?>" 
                           oninput="updateAdditionalFees()">
                </div>
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Certification</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="certification_fees" 
                           name="certification_fees" value="<?php echo $payment['certification_fees'] ?? 0; ?>" 
                           oninput="updateAdditionalFees()">
                </div>
            </div>
        </div>
        
        <!-- Project Type Selection -->
        <div class="form-section-box">
            <div class="form-section-title">
                <i class="bi bi-tag"></i> Project Type Selection
            </div>
            <div id="projectTypeSelection">
                <?php foreach ($projectTypes as $key => $type): ?>
                    <div class="project-type-option" onclick="selectProjectType('<?php echo $key; ?>')">
                        <input type="radio" name="project_type" id="type_<?php echo $key; ?>" 
                               value="<?php echo $key; ?>" 
                               <?php echo ($payment['project_type'] === $key) ? 'checked' : ''; ?>
                               onchange="onProjectTypeChange()">
                        <label for="type_<?php echo $key; ?>" style="cursor: pointer; margin: 0;">
                            <strong><?php echo $key; ?>.</strong> <?php echo htmlspecialchars($type['name']); ?>
                            <?php if (isset($type['description'])): ?>
                                <br><small class="text-muted"><?php echo htmlspecialchars($type['description']); ?></small>
                            <?php endif; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Area and Cost Information -->
        <div class="form-section-box">
            <div class="form-section-title">
                <i class="bi bi-calculator"></i> Area and Cost Calculation
            </div>
            <div class="row">
                <div class="col-md-3 form-group-compact">
                    <label class="form-label-compact">Floor Area (sq.m.)</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="floor_area" 
                           name="floor_area" value="<?php echo $payment['floor_area'] ?? ''; ?>" oninput="calculateCost(); calculateLocationalClearance();">
                </div>
                <div class="col-md-3 form-group-compact">
                    <label class="form-label-compact">Floor Multiplier (₱/m²)</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="floor_multiplier" 
                           name="floor_multiplier" value="<?php echo $payment['floor_multiplier'] ?? ''; ?>" oninput="calculateCost(); calculateLocationalClearance();">
                </div>
                <div class="col-md-3 form-group-compact">
                    <label class="form-label-compact">Lot Area (sq.m.)</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="additional_lot_area" 
                           name="additional_lot_area" value="<?php echo $payment['additional_lot_area'] ?? ''; ?>" oninput="calculateCost(); calculateLocationalClearance();">
                </div>
                <div class="col-md-3 form-group-compact">
                    <label class="form-label-compact">Lot Multiplier (₱/m²)</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="lot_multiplier" 
                           name="lot_multiplier" value="<?php echo $payment['lot_multiplier'] ?? ''; ?>" oninput="calculateCost(); calculateLocationalClearance();">
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Floor Area Cost:</label>
                    <strong id="floor_area_cost_display" style="font-size: 14px; color: #28a745;">₱ 0.00</strong>
                    <input type="hidden" id="floor_area_cost" name="floor_area_cost" value="<?php echo $payment['floor_area_cost'] ?? ''; ?>">
                </div>
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Lot Area Cost:</label>
                    <strong id="lot_area_cost_display" style="font-size: 14px; color: #28a745;">₱ 0.00</strong>
                    <input type="hidden" id="lot_area_cost" name="lot_area_cost" value="<?php echo $payment['lot_area_cost'] ?? ''; ?>">
                </div>
            </div>
            <div class="calculation-box">
                <div class="calculation-row">
                    <span>Calculated Cost:</span>
                    <strong id="calculated_cost_display">₱ 0.00</strong>
                    <input type="hidden" id="calculated_cost" name="calculated_cost" value="<?php echo $payment['calculated_cost'] ?? ''; ?>">
                </div>
                <div class="calculation-row">
                    <span>Project Cost:</span>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="project_cost" 
                           name="project_cost" value="<?php echo $payment['project_cost'] ?? ''; ?>" 
                           style="width: 200px; display: inline-block; font-weight: bold;">
                </div>
            </div>
        </div>
        
        <!-- Locational Clearance Calculation -->
        <div class="form-section-box" id="locationalClearanceSection" style="display: none;">
            <div class="form-section-title">
                <i class="bi bi-geo-alt"></i> Locational Clearance Fee Calculation
            </div>
            <div class="info-box" style="background: #fff3cd; border-left: 4px solid #ffc107; margin-bottom: 15px;">
                <p style="margin: 0; font-size: 12px; color: #856404;">
                    <strong>Note:</strong> Locational Clearance fee is calculated separately for Lot Cost and Floor Cost, then combined.
                </p>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 10px;">
                        <h6 style="color: #495057; margin-bottom: 10px;">Lot Area Calculation</h6>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span>Lot Area Cost:</span>
                            <strong id="loc_lot_cost_display">₱ 0.00</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Lot Locational Fee:</span>
                            <strong id="lot_locational_fee_display" style="color: #28a745;">₱ 0.00</strong>
                        </div>
                        <input type="hidden" id="lot_locational_fee" name="lot_locational_fee" value="<?php echo $payment['lot_locational_fee'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 10px;">
                        <h6 style="color: #495057; margin-bottom: 10px;">Floor Area Calculation</h6>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span>Floor Area Cost:</span>
                            <strong id="loc_floor_cost_display">₱ 0.00</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Floor Locational Fee:</span>
                            <strong id="floor_locational_fee_display" style="color: #28a745;">₱ 0.00</strong>
                        </div>
                        <input type="hidden" id="floor_locational_fee" name="floor_locational_fee" value="<?php echo $payment['floor_locational_fee'] ?? ''; ?>">
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 15px;">
                <div class="col-md-12">
                    <div style="background: #d4edda; border: 2px solid #28a745; padding: 15px; border-radius: 5px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: bold; font-size: 16px;">Grand Total Locational Clearance:</span>
                            <strong id="grand_total_locational_display" style="font-size: 20px; color: #28a745;">₱ 0.00</strong>
                        </div>
                        <input type="hidden" id="grand_total_locational" name="grand_total_locational" value="<?php echo $payment['grand_total_locational'] ?? ''; ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Fee Breakdown - Hidden, only Additional Fees are used -->
        <div class="form-section-box" style="display: none;">
            <div class="form-section-title d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-check"></i> Fee Breakdown</span>
                <button type="button" class="btn btn-sm btn-success" onclick="addFeeRow()">
                    <i class="bi bi-plus"></i> Add Fee
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered fee-table-compact" id="feesTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th>Fee Description</th>
                            <th style="width: 150px;">Amount</th>
                            <th style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="feesTableBody">
                        <?php foreach ($fees as $index => $fee): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <input type="text" class="form-control form-control-compact" name="fee_name[]" 
                                           value="<?php echo htmlspecialchars($fee['fee_name'] ?? ''); ?>" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-compact fee-amount" 
                                           name="fee_amount[]" value="<?php echo $fee['fee_amount'] ?? 0; ?>" 
                                           oninput="calculateTotalFees()" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFeeRow(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f8f9fa; font-weight: bold;">
                            <td colspan="2" class="text-end" style="padding: 1rem;"><strong>Total Fees:</strong></td>
                            <td style="padding: 1rem;">
                                <input type="number" step="0.01" class="form-control form-control-compact" id="total_fees" 
                                       name="total_fees" value="0.00" readonly 
                                       style="font-weight: bold; font-size: 1.1rem; text-align: right;">
                            </td>
                            <td style="padding: 1rem;"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Surcharge and Summary -->
        <div class="form-section-box">
            <div class="form-section-title">
                <i class="bi bi-percent"></i> Surcharge
            </div>
            <div class="row">
                <div class="col-md-4 form-group-compact">
                    <label class="form-label-compact">Surcharge Percentage</label>
                    <select class="form-control form-control-compact" id="surcharge_percentage" 
                            name="surcharge_percentage" onchange="calculateSurcharge()">
                        <option value="0" <?php echo $payment['surcharge_percentage'] == 0 ? 'selected' : ''; ?>>0%</option>
                        <option value="10" <?php echo $payment['surcharge_percentage'] == 10 ? 'selected' : ''; ?>>10%</option>
                        <option value="25" <?php echo $payment['surcharge_percentage'] == 25 ? 'selected' : ''; ?>>25%</option>
                        <option value="50" <?php echo $payment['surcharge_percentage'] == 50 ? 'selected' : ''; ?>>50%</option>
                        <option value="100" <?php echo $payment['surcharge_percentage'] == 100 ? 'selected' : ''; ?>>100%</option>
                    </select>
                </div>
                <div class="col-md-4 form-group-compact">
                    <label class="form-label-compact">Surcharge Amount</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="surcharge_amount" 
                           name="surcharge_amount" value="<?php echo $payment['surcharge_amount']; ?>" readonly>
                </div>
                <div class="col-md-4 form-group-compact">
                    <label class="form-label-compact">Total Area (sq.m.)</label>
                    <input type="number" step="0.01" class="form-control form-control-compact" id="total_area" 
                           name="total_area" value="<?php echo $payment['total_area'] ?? ''; ?>">
                </div>
            </div>
        </div>
        
        <!-- Grand Total Summary -->
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Grand Total:</span>
                <span class="summary-value" id="grand_total_display">₱ 0.00</span>
                <input type="hidden" id="grand_total" name="grand_total" value="<?php echo $payment['grand_total']; ?>">
            </div>
        </div>
        
        <!-- Signature Information -->
        <div class="form-section-box">
            <div class="form-section-title">
                <i class="bi bi-pen"></i> Signature Information
            </div>
            <div class="row">
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Prepared By</label>
                    <input type="text" class="form-control form-control-compact" id="prepared_by" 
                           name="prepared_by" value="LOVELY LAXA" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="col-md-6 form-group-compact">
                    <label class="form-label-compact">Assessed By</label>
                    <input type="text" class="form-control form-control-compact" id="assessed_by" 
                           name="assessed_by" value="ENP. Mark Andrei L. Gubac" readonly style="background-color: #f8f9fa;">
                </div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="<?php echo BASE_URL; ?>?page=dashboard" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> <?php echo $isEdit ? 'Update' : 'Create'; ?> Payment Form
            </button>
        </div>
    </form>
</div>

<script>
// Convert PHP project types to JavaScript, handling PHP_INT_MAX
const projectTypesRaw = <?php echo json_encode($projectTypes); ?>;
const projectTypes = {};
// Process each project type and fix PHP_INT_MAX values
Object.keys(projectTypesRaw).forEach(key => {
    projectTypes[key] = {
        ...projectTypesRaw[key],
        fee_ranges: projectTypesRaw[key].fee_ranges.map(range => ({
            ...range,
            // Replace very large numbers (PHP_INT_MAX) with JavaScript's safe max
            max: range.max > 1e15 ? Number.MAX_SAFE_INTEGER : range.max
        }))
    };
});
let feeRowCounter = <?php echo count($fees); ?>;

function selectProjectType(type) {
    document.getElementById('type_' + type).checked = true;
    onProjectTypeChange();
    updateProjectTypeSelection();
}

function updateProjectTypeSelection() {
    document.querySelectorAll('.project-type-option').forEach(option => {
        option.classList.remove('selected');
        if (option.querySelector('input[type="radio"]').checked) {
            option.classList.add('selected');
        }
    });
}

function onProjectTypeChange() {
    calculateLocationalClearance();
    updateProjectTypeSelection();
    // Automatically calculate fees when project type changes
    console.log('Project type changed, calculating fees...');
    calculateFees();
    
    // Also trigger cost calculation to ensure project cost is set if calculated cost exists
    const calculatedCost = parseFloat(document.getElementById('calculated_cost').value) || 0;
    if (calculatedCost > 0 && (!document.getElementById('project_cost').value || document.getElementById('project_cost').value == 0)) {
        document.getElementById('project_cost').value = calculatedCost.toFixed(2);
        calculateFees(); // Recalculate fees with the cost
    }
}

function calculateCost() {
    const floorArea = parseFloat(document.getElementById('floor_area').value) || 0;
    const floorMultiplier = parseFloat(document.getElementById('floor_multiplier').value) || 0;
    const lotArea = parseFloat(document.getElementById('additional_lot_area').value) || 0;
    const lotMultiplier = parseFloat(document.getElementById('lot_multiplier').value) || 0;
    
    // Floor Area × Floor Multiplier = Floor Area Cost
    const floorAreaCost = floorArea * floorMultiplier;
    
    // Lot Area × Lot Multiplier = Lot Area Cost
    const lotAreaCost = lotArea * lotMultiplier;
    
    // Total Cost = Floor Area Cost + Lot Area Cost
    const calculatedCost = floorAreaCost + lotAreaCost;
    
    // Display individual costs
    document.getElementById('floor_area_cost').value = floorAreaCost.toFixed(2);
    document.getElementById('floor_area_cost_display').textContent = '₱ ' + floorAreaCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    document.getElementById('lot_area_cost').value = lotAreaCost.toFixed(2);
    document.getElementById('lot_area_cost_display').textContent = '₱ ' + lotAreaCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    document.getElementById('calculated_cost').value = calculatedCost.toFixed(2);
    document.getElementById('calculated_cost_display').textContent = '₱ ' + calculatedCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Auto-set project cost if not manually entered
    const projectCostInput = document.getElementById('project_cost');
    const currentProjectCost = parseFloat(projectCostInput.value) || 0;
    if (!projectCostInput.value || projectCostInput.value == 0) {
        projectCostInput.value = calculatedCost.toFixed(2);
    }
    
    // Removed auto-calculation of total_area - user can type it manually
    
    // Always recalculate fees when cost changes
    calculateFees();
}

// Locational Clearance Fee Calculation
function calculateLocationalClearanceFee(category, cost) {
    cost = parseFloat(cost) || 0;
    
    // Map category codes to names
    const categoryMap = {
        'A': 'Single Residential',
        'B': 'Apartments/Townhouses',
        'C': 'Dormitories',
        'D': 'Institutional',
        'E': 'Commercial/Industrial',
        'F': 'Special Uses',
        'G': 'Alteration/Expansion'
    };
    
    const categoryName = categoryMap[category] || category;
    
    switch (categoryName) {
        case 'Single Residential':
        case 'SINGLE RESIDENTIAL STRUCTURE ATTACHED OR DETACHED':
            if (cost <= 100000) {
                return 298.00;
            } else if (cost <= 200000) {
                return 576.00;
            } else {
                // ₱720 + 1/10 of 1% of cost in excess of ₱200,000
                return 720.00 + (0.001 * (cost - 200000));
            }
            
        case 'Apartments/Townhouses':
        case 'APARTMENTS/TOWNHOUSES':
            if (cost <= 500000) {
                return 1440.00;
            } else if (cost <= 2000000) {
                return 2160.00;
            } else {
                // ₱3600 + 1/10 of 1% of cost in excess of ₱2,000,000
                return 3600.00 + (0.001 * (cost - 2000000));
            }
            
        case 'Dormitories':
        case 'DORMITORIES':
            if (cost <= 2000000) {
                return 3600.00;
            } else {
                // ₱3600 + 1/10 of 1% of cost in excess of ₱2,000,000
                return 3600.00 + (0.001 * (cost - 2000000));
            }
            
        case 'Institutional':
        case 'INSTITUTIONAL':
            if (cost <= 2000000) {
                return 2800.00;
            } else {
                // ₱2800 + 1/10 of 1% of cost in excess of ₱2,000,000
                return 2800.00 + (0.001 * (cost - 2000000));
            }
            
        case 'Commercial/Industrial':
        case 'COMMERCIAL, INDUSTRIAL AND AGRO-INDUSTRIAL PROJECTS':
            if (cost <= 100000) {
                return 1440.00;
            } else if (cost <= 500000) {
                return 2160.00;
            } else if (cost <= 1000000) {
                return 3600.00;
            } else if (cost <= 2000000) {
                return 5200.00;
            } else {
                // ₱5200 + 1/10 of 1% of cost in excess of ₱2,000,000
                return 5200.00 + (0.001 * (cost - 2000000));
            }
            
        case 'Special Uses':
        case 'SPECIAL USES/SPECIAL PROJECTS':
            if (cost <= 2000000) {
                return 3600.00;
            } else {
                // ₱5200 + 1/10 of 1% of cost in excess of ₱2,000,000
                return 5200.00 + (0.001 * (cost - 2000000));
            }
            
        case 'Alteration/Expansion':
        case 'ALTERATION/EXPANSION':
            // Based on affected areas/cost only - same as Single Residential
            if (cost <= 100000) {
                return 298.00;
            } else if (cost <= 200000) {
                return 576.00;
            } else {
                return 720.00 + (0.001 * (cost - 200000));
            }
            
        default:
            return 0.00;
    }
}

function calculateLocationalClearance() {
    const projectType = document.querySelector('input[name="project_type"]:checked')?.value;
    
    if (!projectType) {
        document.getElementById('locationalClearanceSection').style.display = 'none';
        return;
    }
    
    // Get costs
    const floorAreaCost = parseFloat(document.getElementById('floor_area_cost').value) || 0;
    const lotAreaCost = parseFloat(document.getElementById('lot_area_cost').value) || 0;
    
    if (floorAreaCost === 0 && lotAreaCost === 0) {
        document.getElementById('locationalClearanceSection').style.display = 'none';
        return;
    }
    
    // Show section
    document.getElementById('locationalClearanceSection').style.display = 'block';
    
    // Calculate locational fees separately
    const lotLocationalFee = calculateLocationalClearanceFee(projectType, lotAreaCost);
    const floorLocationalFee = calculateLocationalClearanceFee(projectType, floorAreaCost);
    
    // Grand Total
    const grandTotalLocational = lotLocationalFee + floorLocationalFee;
    
    // Display results
    document.getElementById('loc_lot_cost_display').textContent = '₱ ' + lotAreaCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('lot_locational_fee').value = lotLocationalFee.toFixed(2);
    document.getElementById('lot_locational_fee_display').textContent = '₱ ' + lotLocationalFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    document.getElementById('loc_floor_cost_display').textContent = '₱ ' + floorAreaCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('floor_locational_fee').value = floorLocationalFee.toFixed(2);
    document.getElementById('floor_locational_fee_display').textContent = '₱ ' + floorLocationalFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    document.getElementById('grand_total_locational').value = grandTotalLocational.toFixed(2);
    document.getElementById('grand_total_locational_display').textContent = '₱ ' + grandTotalLocational.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Update locational clearance in additional fees section
    const locationalDisplay = document.getElementById('locational_clearance_display');
    const locationalHidden = document.getElementById('locational_clearance_fee');
    if (locationalDisplay) {
        locationalDisplay.value = grandTotalLocational.toFixed(2);
    }
    if (locationalHidden) {
        locationalHidden.value = grandTotalLocational.toFixed(2);
    }
    
    // Update additional fees in fee breakdown
    updateAdditionalFees();
}

function calculateTotalArea() {
    const floorArea = parseFloat(document.getElementById('floor_area').value) || 0;
    const additionalLotArea = parseFloat(document.getElementById('additional_lot_area').value) || 0;
    const totalArea = floorArea + additionalLotArea;
    document.getElementById('total_area').value = totalArea.toFixed(2);
}

function calculateFees() {
    const projectType = document.querySelector('input[name="project_type"]:checked')?.value;
    const projectCost = parseFloat(document.getElementById('project_cost').value) || 0;
    const tbody = document.getElementById('feesTableBody');
    
    console.log('=== calculateFees called ===');
    console.log('Project Type:', projectType);
    console.log('Project Cost:', projectCost, '(type:', typeof projectCost, ')');
    const projectCostInput = document.getElementById('project_cost');
    console.log('Project Cost input element:', projectCostInput);
    console.log('Project Cost input value:', projectCostInput ? projectCostInput.value : 'NOT FOUND');
    console.log('Available project types:', Object.keys(projectTypes));
    
    if (projectType && projectTypes[projectType]) {
        const type = projectTypes[projectType];
        let feeRows = [];
        
        console.log('Project type found:', type.name);
        console.log('Number of fee ranges:', type.fee_ranges.length);
        
        // Special handling for Type B, E, and F: 
        // - Type B and E: Only the first range is always applicable
        // - Type F: Only the second range (Over 2 Million) is always applicable
        const isTypeB = (projectType === 'B');
        const isTypeE = (projectType === 'E');
        const isTypeF = (projectType === 'F');
        
        // Show ALL fee ranges for the selected project type
        type.fee_ranges.forEach((range, index) => {
            let amount = '';
            let isApplicable = false;
            
            if (isTypeB || isTypeE) {
                // For Type B and E: Only the first range (index 0) is applicable, regardless of project cost
                isApplicable = (index === 0);
                const typeName = isTypeB ? 'B' : 'E';
                console.log(`Range ${index + 1} (${range.label}): Type ${typeName} - ${isApplicable ? 'APPLICABLE (first range)' : 'NOT APPLICABLE'}`);
            } else if (isTypeF) {
                // For Type F: Only the second range (index 1, Over 2 Million) is applicable, regardless of project cost
                isApplicable = (index === 1);
                console.log(`Range ${index + 1} (${range.label}): Type F - ${isApplicable ? 'APPLICABLE (second range)' : 'NOT APPLICABLE'}`);
            } else if (projectCost > 0) {
                // Check if this range applies to the current project cost
                // For the last range (max is very large), just check if cost >= min
                const isMaxValue = (range.max >= Number.MAX_SAFE_INTEGER || range.max > 1e15);
                if (isMaxValue) {
                    isApplicable = (projectCost >= range.min);
                    console.log(`Range ${index + 1} (${range.label}): Checking MAX range - cost ${projectCost} >= min ${range.min}? ${isApplicable}`);
                } else {
                    isApplicable = (projectCost >= range.min && projectCost <= range.max);
                    console.log(`Range ${index + 1} (${range.label}): Checking normal range - cost ${projectCost} >= min ${range.min} && <= max ${range.max}? ${isApplicable}`);
                }
                console.log(`Range ${index + 1} (${range.label}): min=${range.min}, max=${isMaxValue ? 'MAX' : range.max}, fee=${range.fee}, applicable=${isApplicable}, cost=${projectCost}`);
            } else {
                console.log(`Range ${index + 1} (${range.label}): min=${range.min}, max=${range.max > 1e15 ? 'MAX' : range.max}, fee=${range.fee}, cost=0 (not set)`);
            }
            
            // Determine the amount to display based on user requirements:
            // - If applicable: show the fee amount (even if 0)
            // - If not applicable and fee > 0: show "-"
            // - If not applicable and fee = 0: show "0" (for middle ranges) or "-" (for last range with max = PHP_INT_MAX)
            if (isApplicable) {
                // Applicable range: show the fee amount
                amount = range.fee.toFixed(2);
                console.log(`  -> Range ${index + 1} APPLICABLE: amount = ${amount}`);
            } else {
                // Not applicable range
                const isLastRange = (range.max >= Number.MAX_SAFE_INTEGER || range.max > 1e15);
                if (range.fee > 0) {
                    // Non-applicable range with fee > 0: show "-"
                    amount = '-';
                    console.log(`  -> Range ${index + 1} NOT APPLICABLE (fee>0): amount = -`);
                } else if (isLastRange) {
                    // Last range with fee = 0: show "-"
                    amount = '-';
                    console.log(`  -> Range ${index + 1} NOT APPLICABLE (last range, fee=0): amount = -`);
                } else {
                    // Middle range with fee = 0: show "0"
                    amount = '0';
                    console.log(`  -> Range ${index + 1} NOT APPLICABLE (middle range, fee=0): amount = 0`);
                }
            }
            
            feeRows.push({
                name: range.label,
                amount: amount,
                isApplicable: isApplicable,
                actualFee: range.fee
            });
        });
        
        console.log('Total fee rows to create:', feeRows.length);
        
        // Clear all existing fee rows
        tbody.innerHTML = '';
        feeRowCounter = 0;
        
        // Add all fee ranges as rows
        feeRows.forEach((feeRow, index) => {
            feeRowCounter++;
            const row = tbody.insertRow();
            
            console.log(`Creating row ${feeRowCounter}: ${feeRow.name} = ${feeRow.amount}`);
            
            // Create amount input or display
            // If amount is "-", show as readonly text with hidden input for calculation
            // Otherwise, show as editable number input
            let amountInput = '';
            if (feeRow.amount === '-') {
                // Show dash as readonly text
                amountInput = `<input type="text" class="form-control form-control-compact fee-amount-readonly" 
                                    value="-" readonly style="background-color: #f8f9fa; text-align: center;">
                              <input type="hidden" class="fee-amount" name="fee_amount[]" value="0" data-applicable="false">`;
            } else {
                // Show editable amount (for "0" or actual fee amounts)
                const applicableAttr = feeRow.isApplicable ? 'data-applicable="true"' : 'data-applicable="false"';
                amountInput = `<input type="number" step="0.01" class="form-control form-control-compact fee-amount" 
                                     name="fee_amount[]" value="${feeRow.amount}" 
                                     ${applicableAttr}
                                     oninput="calculateTotalFees()" required>`;
            }
            
            row.innerHTML = `
                <td>${feeRowCounter}</td>
                <td>
                    <input type="text" class="form-control form-control-compact" name="fee_name[]" 
                           value="${feeRow.name}" required>
                </td>
                <td>
                    ${amountInput}
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFeeRow(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
        });
        
        console.log('All rows created. Total rows in table:', tbody.querySelectorAll('tr').length);
        
        // Log summary of what was created
        console.log('=== Fee Rows Summary ===');
        feeRows.forEach((row, idx) => {
            console.log(`Row ${idx + 1}: ${row.name} = ${row.amount} (applicable: ${row.isApplicable}, actualFee: ${row.actualFee})`);
        });
        console.log('=== End Summary ===');
        
        // Calculate total fees after DOM is updated
        // Use requestAnimationFrame for better timing
        requestAnimationFrame(() => {
            setTimeout(() => {
                calculateTotalFees();
            }, 100);
        });
    } else {
        // If no project type selected, ensure at least one empty row exists
        if (tbody.querySelectorAll('tr').length === 0) {
            feeRowCounter = 1;
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>1</td>
                <td>
                    <input type="text" class="form-control form-control-compact" name="fee_name[]" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control form-control-compact fee-amount" name="fee_amount[]" 
                           value="0" oninput="calculateTotalFees()" required>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFeeRow(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
        }
        
        // Calculate total fees after DOM is updated (for else block)
        requestAnimationFrame(() => {
            setTimeout(() => {
                calculateTotalFees();
            }, 100);
        });
    }
    
    // Total fees calculation is handled by setTimeout in both if and else blocks above
}

function addFeeRow() {
    feeRowCounter++;
    const tbody = document.getElementById('feesTableBody');
    const row = tbody.insertRow();
    row.innerHTML = `
        <td>${feeRowCounter}</td>
        <td>
            <input type="text" class="form-control form-control-compact" name="fee_name[]" required>
        </td>
        <td>
            <input type="number" step="0.01" class="form-control form-control-compact fee-amount" name="fee_amount[]" 
                   value="0" oninput="calculateTotalFees()" required>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeFeeRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    updateRowNumbers();
}

function removeFeeRow(btn) {
    const row = btn.closest('tr');
    row.remove();
    updateRowNumbers();
    calculateTotalFees();
}

function updateRowNumbers() {
    const rows = document.getElementById('feesTableBody').querySelectorAll('tr');
    rows.forEach((row, index) => {
        row.cells[0].textContent = index + 1;
    });
    feeRowCounter = rows.length;
}

function updateAdditionalFees() {
    // Get all additional fee values
    const fillingFees = parseFloat(document.getElementById('filling_fees')?.value || 50.00);
    const preliminaryInspectionFees = parseFloat(document.getElementById('preliminary_inspection_fees')?.value || 1500.00);
    const locationalClearance = parseFloat(document.getElementById('locational_clearance_display')?.value || 0);
    const lineAndGradeFees = parseFloat(document.getElementById('line_and_grade_fees')?.value || 0);
    const zoningFees = parseFloat(document.getElementById('zoning_fees')?.value || 720.00);
    const esfFees = parseFloat(document.getElementById('esf_fees')?.value || 20.00);
    const developmentFees = parseFloat(document.getElementById('development_fees')?.value || 0);
    const certificationFees = parseFloat(document.getElementById('certification_fees')?.value || 0);
    
    const additionalFees = [
        { name: 'Filling fees', amount: fillingFees },
        { name: 'Preliminary inspection & verification fees', amount: preliminaryInspectionFees },
        { name: 'Locational clearance', amount: locationalClearance },
        { name: 'Line and grade fees', amount: lineAndGradeFees },
        { name: 'Zoning fees', amount: zoningFees },
        { name: 'ESF', amount: esfFees },
        { name: 'Development Fees', amount: developmentFees },
        { name: 'Certification', amount: certificationFees }
    ];
    
    const tbody = document.getElementById('feesTableBody');
    if (!tbody) return;
    
    // Get existing fee names (case-insensitive)
    const existingRows = tbody.querySelectorAll('tr');
    const existingFeeNames = Array.from(existingRows).map(row => {
        const nameInput = row.querySelector('input[name="fee_name[]"]');
        return nameInput ? nameInput.value.toLowerCase().trim() : '';
    });
    
    // Update or add additional fees
    additionalFees.forEach(fee => {
        const feeNameLower = fee.name.toLowerCase().trim();
        const existingIndex = existingFeeNames.indexOf(feeNameLower);
        
        if (existingIndex >= 0 && fee.amount > 0) {
            // Update existing row
            const row = existingRows[existingIndex];
            const amountInput = row.querySelector('input[name="fee_amount[]"]');
            if (amountInput) {
                amountInput.value = fee.amount.toFixed(2);
            }
        } else if (fee.amount > 0) {
            // Add new row if amount > 0
            const row = tbody.insertRow();
            const rowNum = tbody.querySelectorAll('tr').length;
            row.innerHTML = `
                <td>${rowNum}</td>
                <td>
                    <input type="text" class="form-control form-control-compact" name="fee_name[]" 
                           value="${fee.name}" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control form-control-compact fee-amount" 
                           name="fee_amount[]" value="${fee.amount.toFixed(2)}" 
                           oninput="calculateTotalFees()" required>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFeeRow(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            existingFeeNames.push(feeNameLower);
        }
    });
    
    // Update row numbers
    updateRowNumbers();
    
    // Recalculate total fees
    calculateTotalFees();
}

function calculateTotalFees() {
    try {
        // Select all visible number inputs in the fee table (exclude hidden inputs used for dashes)
        const tbody = document.getElementById('feesTableBody');
        if (!tbody) {
            console.error('feesTableBody not found!');
            return;
        }
        
        // Get all rows in the table
        const rows = tbody.querySelectorAll('tr');
        let total = 0;
        
        console.log('=== Calculating Total Fees ===');
        console.log('Found', rows.length, 'fee rows');
        
        // Loop through each row and get the fee amount
        // Only sum rows that are applicable (data-applicable="true")
        rows.forEach((row, index) => {
            // Find the amount cell (3rd column)
            const amountCell = row.cells[2]; // Use cells array for more reliable access
            if (amountCell) {
                // Get the number input in the cell
                const numberInput = amountCell.querySelector('input[type="number"]');
                if (numberInput) {
                    // Check if this row is applicable (for project type fees)
                    // For additional fees, always include them
                    const isApplicable = numberInput.getAttribute('data-applicable') === 'true';
                    const feeNameInput = row.querySelector('input[name="fee_name[]"]');
                    const feeName = feeNameInput ? feeNameInput.value.toLowerCase().trim() : '';
                    
                    // Additional fees should always be included
                    const isAdditionalFee = ['filling fees', 'preliminary inspection & verification fees', 
                        'locational clearance', 'line and grade fees', 'zoning fees', 'esf', 
                        'development fees', 'certification'].includes(feeName);
                    
                    if (isApplicable || isAdditionalFee) {
                        const value = parseFloat(numberInput.value);
                        if (!isNaN(value)) {
                            console.log(`Row ${index + 1}: Amount = ${value} (${isAdditionalFee ? 'ADDITIONAL FEE' : 'APPLICABLE'} - adding to total)`);
                            total += value;
                        } else {
                            console.log(`Row ${index + 1}: Invalid number value`);
                        }
                    } else {
                        console.log(`Row ${index + 1}: Amount = ${numberInput.value} (NOT APPLICABLE - skipping)`);
                    }
                } else {
                    console.log(`Row ${index + 1}: No number input found`);
                }
            } else {
                console.log(`Row ${index + 1}: No amount cell found`);
            }
        });
        
        console.log('=== Total Fees calculated:', total, '===');
        
        // Update the Total Fees input
        const totalFeesInput = document.getElementById('total_fees');
        if (!totalFeesInput) {
            console.error('ERROR: total_fees input element not found in DOM!');
            // Try alternative selectors
            const altInput = document.querySelector('input[name="total_fees"]');
            if (altInput) {
                console.log('Found total_fees using name selector');
                altInput.value = total.toFixed(2);
                return;
            }
            return;
        }
        
        const formattedTotal = total.toFixed(2);
        totalFeesInput.value = formattedTotal;
        console.log('Total Fees input updated to:', formattedTotal);
        
        // Force visual update
        totalFeesInput.style.color = total > 0 ? '#000' : '#666';
        totalFeesInput.style.backgroundColor = total > 0 ? '#fff' : '#f8f9fa';
        
        // Also update the display text if there's a display element
        if (totalFeesInput.nextElementSibling) {
            totalFeesInput.nextElementSibling.textContent = '₱ ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        
        // Trigger change event to ensure any listeners are notified
        totalFeesInput.dispatchEvent(new Event('change', { bubbles: true }));
        totalFeesInput.dispatchEvent(new Event('input', { bubbles: true }));
        
    } catch (error) {
        console.error('Error in calculateTotalFees:', error);
    }
    
    calculateSurcharge();
}

function calculateSurcharge() {
    const totalFees = parseFloat(document.getElementById('total_fees').value) || 0;
    const surchargePercentage = parseFloat(document.getElementById('surcharge_percentage').value) || 0;
    const surchargeAmount = totalFees * (surchargePercentage / 100);
    document.getElementById('surcharge_amount').value = surchargeAmount.toFixed(2);
    
    const grandTotal = totalFees + surchargeAmount;
    document.getElementById('grand_total').value = grandTotal.toFixed(2);
    document.getElementById('grand_total_display').textContent = '₱ ' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Test function for debugging - can be called from console
window.testTotalFees = function() {
    console.log('=== Testing Total Fees Calculation ===');
    const tbody = document.getElementById('feesTableBody');
    const totalFeesInput = document.getElementById('total_fees');
    
    console.log('feesTableBody exists:', !!tbody);
    console.log('total_fees input exists:', !!totalFeesInput);
    
    if (tbody) {
        const rows = tbody.querySelectorAll('tr');
        console.log('Number of rows:', rows.length);
        rows.forEach((row, idx) => {
            console.log(`Row ${idx + 1}:`, row.innerHTML.substring(0, 100));
        });
    }
    
    if (totalFeesInput) {
        console.log('Total Fees current value:', totalFeesInput.value);
        console.log('Total Fees element:', totalFeesInput);
    }
    
    calculateTotalFees();
    console.log('=== Test Complete ===');
};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing...');
    
    // Calculate cost first to ensure all values are set
    calculateCost();
    
    // Update project type selection
    updateProjectTypeSelection();
    
    // Calculate fees after a short delay to ensure DOM is ready
    setTimeout(() => {
        calculateFees();
        calculateTotalFees();
        calculateSurcharge();
        calculateLocationalClearance();
        updateAdditionalFees();
    }, 300);
    
    console.log('Initialization complete. You can test by calling: testTotalFees()');
});
</script>

<?php
require_once ROOT_PATH . '/Views/User/Layouts/Footer.php';
?>

<?php
require_once ROOT_PATH . '/Views/User/Layouts/Header.php';
?>

<style>
.calculator-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.calculator-box {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.calculator-title {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 10px;
    text-align: center;
}

.calculator-subtitle {
    font-size: 14px;
    color: #6c757d;
    text-align: center;
    margin-bottom: 30px;
}

.form-group-calc {
    margin-bottom: 20px;
}

.form-label-calc {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #495057;
    font-size: 14px;
}

.form-control-calc {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    font-size: 14px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control-calc:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.result-box {
    background: #f8f9fa;
    border: 2px solid #28a745;
    border-radius: 8px;
    padding: 20px;
    margin-top: 30px;
}

.result-title {
    font-size: 18px;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 15px;
    text-align: center;
}

.result-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #dee2e6;
}

.result-row:last-child {
    border-bottom: none;
}

.result-label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.result-value {
    font-size: 16px;
    font-weight: bold;
    color: #2c3e50;
}

.result-value.highlight {
    color: #28a745;
    font-size: 20px;
}

.btn-calculate {
    width: 100%;
    padding: 12px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
    margin-top: 20px;
}

.btn-calculate:hover {
    background: #0056b3;
}

.btn-calculate:active {
    transform: translateY(1px);
}

.btn-copy {
    padding: 8px 15px;
    background: #6c757d;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 12px;
    cursor: pointer;
    margin-left: 10px;
}

.btn-copy:hover {
    background: #5a6268;
}

.info-box {
    background: #e7f3ff;
    border-left: 4px solid #007bff;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.info-box p {
    margin: 0;
    font-size: 13px;
    color: #004085;
}

.row-calc {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.col-calc {
    flex: 1;
}
</style>

<div class="calculator-container">
    <div class="calculator-box">
        <h2 class="calculator-title">
            <i class="bi bi-calculator"></i> Area & Cost Calculator
        </h2>
        <p class="calculator-subtitle">Calculate area and project cost for payment forms</p>
        
        <div class="info-box">
            <p><strong>Instructions:</strong> Enter the floor area, lot area, and multiplier to calculate the total area and project cost. You can copy the results to use in the payment form.</p>
        </div>
        
        <form id="calculatorForm">
            <div class="row-calc">
                <div class="col-calc">
                    <div class="form-group-calc">
                        <label class="form-label-calc">Floor Area (sq.m.)</label>
                        <input type="number" step="0.01" class="form-control-calc" id="calc_floor_area" 
                               placeholder="Enter floor area" value="">
                    </div>
                </div>
                <div class="col-calc">
                    <div class="form-group-calc">
                        <label class="form-label-calc">Additional Lot Area (sq.m.)</label>
                        <input type="number" step="0.01" class="form-control-calc" id="calc_additional_lot_area" 
                               placeholder="Enter lot area" value="">
                    </div>
                </div>
            </div>
            
            <div class="row-calc">
                <div class="col-calc">
                    <div class="form-group-calc">
                        <label class="form-label-calc">Multiplier (pesos per sq.m.)</label>
                        <input type="number" step="0.01" class="form-control-calc" id="calc_multiplier" 
                               placeholder="Enter multiplier" value="">
                        <small style="color: #6c757d; font-size: 12px;">Used for both Floor Area and Lot Area</small>
                    </div>
                </div>
            </div>
            
            <button type="button" class="btn-calculate" onclick="calculateAreaCost()">
                <i class="bi bi-calculator"></i> Calculate
            </button>
        </form>
        
        <div class="result-box" id="resultBox" style="display: none;">
            <h3 class="result-title">Calculation Results</h3>
            
            <div class="result-row">
                <span class="result-label">Total Area (sq.m.):</span>
                <span class="result-value" id="result_total_area">0.00</span>
                <button type="button" class="btn-copy" onclick="copyToClipboard('result_total_area')">
                    <i class="bi bi-clipboard"></i> Copy
                </button>
            </div>
            
            <div class="result-row" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;">
                <div style="width: 100%;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span class="result-label">Floor Area Cost:</span>
                        <span class="result-value" id="result_floor_cost">₱ 0.00</span>
                    </div>
                    <div style="font-size: 12px; color: #6c757d; text-align: right;">
                        (Floor Area × Multiplier)
                    </div>
                </div>
            </div>
            
            <div class="result-row" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;">
                <div style="width: 100%;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span class="result-label">Lot Area Cost:</span>
                        <span class="result-value" id="result_lot_cost">₱ 0.00</span>
                    </div>
                    <div style="font-size: 12px; color: #6c757d; text-align: right;">
                        (Lot Area × Multiplier)
                    </div>
                </div>
            </div>
            
            <div class="result-row">
                <span class="result-label">Total Calculated Cost:</span>
                <span class="result-value highlight" id="result_calculated_cost">₱ 0.00</span>
                <button type="button" class="btn-copy" onclick="copyToClipboard('result_calculated_cost')">
                    <i class="bi bi-clipboard"></i> Copy
                </button>
            </div>
            
            <div class="result-row">
                <span class="result-label">Project Cost:</span>
                <span class="result-value highlight" id="result_project_cost">₱ 0.00</span>
                <button type="button" class="btn-copy" onclick="copyToClipboard('result_project_cost')">
                    <i class="bi bi-clipboard"></i> Copy
                </button>
            </div>
        </div>
    </div>
    
    <div class="calculator-box">
        <h3 style="margin-bottom: 20px; color: #2c3e50;">
            <i class="bi bi-info-circle"></i> Quick Actions
        </h3>
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo BASE_URL; ?>?page=payment&action=create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Payment Form
            </a>
            <a href="<?php echo BASE_URL; ?>?page=dashboard" class="btn btn-secondary">
                <i class="bi bi-house"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<script>
function calculateAreaCost() {
    const floorArea = parseFloat(document.getElementById('calc_floor_area').value) || 0;
    const additionalLotArea = parseFloat(document.getElementById('calc_additional_lot_area').value) || 0;
    const multiplier = parseFloat(document.getElementById('calc_multiplier').value) || 0;
    
    // Calculate total area
    const totalArea = floorArea + additionalLotArea;
    
    // Calculate costs separately
    // Floor Area × Multiplier = Floor Area Cost
    const floorAreaCost = floorArea * multiplier;
    
    // Lot Area × Multiplier = Lot Area Cost
    const lotAreaCost = additionalLotArea * multiplier;
    
    // Total Cost = Floor Area Cost + Lot Area Cost
    const calculatedCost = floorAreaCost + lotAreaCost;
    
    // Display results
    document.getElementById('result_total_area').textContent = totalArea.toFixed(2);
    document.getElementById('result_floor_cost').textContent = '₱ ' + floorAreaCost.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    document.getElementById('result_lot_cost').textContent = '₱ ' + lotAreaCost.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    document.getElementById('result_calculated_cost').textContent = '₱ ' + calculatedCost.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    document.getElementById('result_project_cost').textContent = '₱ ' + calculatedCost.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    // Store values in data attributes for copying
    document.getElementById('result_total_area').setAttribute('data-value', totalArea.toFixed(2));
    document.getElementById('result_floor_cost').setAttribute('data-value', floorAreaCost.toFixed(2));
    document.getElementById('result_lot_cost').setAttribute('data-value', lotAreaCost.toFixed(2));
    document.getElementById('result_calculated_cost').setAttribute('data-value', calculatedCost.toFixed(2));
    document.getElementById('result_project_cost').setAttribute('data-value', calculatedCost.toFixed(2));
    
    // Show result box
    document.getElementById('resultBox').style.display = 'block';
}

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const value = element.getAttribute('data-value') || element.textContent.replace('₱ ', '').replace(/,/g, '');
    
    // Create temporary textarea
    const textarea = document.createElement('textarea');
    textarea.value = value;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        
        // Show feedback
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check"></i> Copied!';
        button.style.background = '#28a745';
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = '#6c757d';
        }, 2000);
    } catch (err) {
        console.error('Failed to copy:', err);
        alert('Failed to copy to clipboard');
    }
    
    document.body.removeChild(textarea);
}

// Allow Enter key to trigger calculation
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-control-calc');
    inputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                calculateAreaCost();
            }
        });
    });
});
</script>

<?php
require_once ROOT_PATH . '/Views/User/Layouts/Footer.php';
?>


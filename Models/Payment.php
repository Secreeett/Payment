<?php
/**
 * Payment Model
 */
class Payment {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function createPaymentForm($data) {
        // Generate official receipt number
        $receiptNo = $this->generateReceiptNo();
        
        $this->db->query("INSERT INTO payment_forms (
            official_receipt_no, owner_applicant_name, project_title, location,
            date, division, project_type, project_cost, multiplier, calculated_cost,
            floor_area, additional_lot_area, total_area,
            total_fees, surcharge_percentage, surcharge_amount, grand_total,
            prepared_by, assessed_by, created_by, status
        ) VALUES (
            :receipt_no, :owner_name, :project_title, :location,
            :date, :division, :project_type, :project_cost, :multiplier, :calculated_cost,
            :floor_area, :additional_lot_area, :total_area,
            :total_fees, :surcharge_percentage, :surcharge_amount, :grand_total,
            :prepared_by, :assessed_by, :created_by, :status
        )");
        
        $this->db->bind(':receipt_no', $receiptNo);
        $this->db->bind(':owner_name', $data['owner_applicant_name']);
        $this->db->bind(':project_title', $data['project_title']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':division', $data['division'] ?? '');
        $this->db->bind(':project_type', $data['project_type'] ?? null);
        $this->db->bind(':project_cost', $data['project_cost'] ?? 0);
        $this->db->bind(':multiplier', $data['multiplier'] ?? 0);
        $this->db->bind(':calculated_cost', $data['calculated_cost'] ?? 0);
        $this->db->bind(':floor_area', $data['floor_area'] ?? 0);
        $this->db->bind(':additional_lot_area', $data['additional_lot_area'] ?? 0);
        $this->db->bind(':total_area', $data['total_area'] ?? 0);
        $this->db->bind(':total_fees', $data['total_fees'] ?? 0);
        $this->db->bind(':surcharge_percentage', $data['surcharge_percentage'] ?? 0);
        $this->db->bind(':surcharge_amount', $data['surcharge_amount'] ?? 0);
        $this->db->bind(':grand_total', $data['grand_total'] ?? 0);
        $this->db->bind(':prepared_by', $data['prepared_by'] ?? '');
        $this->db->bind(':assessed_by', $data['assessed_by'] ?? '');
        $this->db->bind(':created_by', $data['created_by'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'draft');
        
        if ($this->db->execute()) {
            $paymentId = $this->db->lastInsertId();
            
            // Save individual fees
            if (isset($data['fees']) && is_array($data['fees'])) {
                foreach ($data['fees'] as $fee) {
                    $this->saveFee($paymentId, $fee['name'], $fee['amount']);
                }
            }
            
            return $paymentId;
        }
        return false;
    }
    
    public function saveFee($paymentId, $feeName, $feeAmount) {
        $this->db->query("INSERT INTO payment_fees (payment_form_id, fee_name, fee_amount) VALUES (:payment_id, :fee_name, :fee_amount)");
        $this->db->bind(':payment_id', $paymentId);
        $this->db->bind(':fee_name', $feeName);
        $this->db->bind(':fee_amount', $feeAmount);
        return $this->db->execute();
    }
    
    public function getPaymentForm($id) {
        $this->db->query("SELECT * FROM payment_forms WHERE id = :id");
        $this->db->bind(':id', $id);
        $payment = $this->db->single();
        
        if ($payment) {
            $payment['fees'] = $this->getFees($id);
        }
        
        return $payment;
    }
    
    public function getFees($paymentId) {
        $this->db->query("SELECT * FROM payment_fees WHERE payment_form_id = :payment_id ORDER BY id");
        $this->db->bind(':payment_id', $paymentId);
        return $this->db->resultSet();
    }
    
    public function getAllPaymentForms($limit = 100, $offset = 0) {
        $this->db->query("SELECT pf.*, u.full_name as created_by_name 
                         FROM payment_forms pf 
                         LEFT JOIN users u ON pf.created_by = u.id 
                         ORDER BY pf.created_at DESC 
                         LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
    public function searchPaymentForms($searchTerm) {
        $this->db->query("SELECT pf.*, u.full_name as created_by_name 
                         FROM payment_forms pf 
                         LEFT JOIN users u ON pf.created_by = u.id 
                         WHERE pf.owner_applicant_name LIKE :search 
                         OR pf.project_title LIKE :search 
                         OR pf.official_receipt_no LIKE :search
                         ORDER BY pf.created_at DESC");
        $this->db->bind(':search', "%{$searchTerm}%");
        return $this->db->resultSet();
    }
    
    public function updatePaymentForm($id, $data) {
        $this->db->query("UPDATE payment_forms SET
            owner_applicant_name = :owner_name,
            project_title = :project_title,
            location = :location,
            date = :date,
            division = :division,
            project_type = :project_type,
            project_cost = :project_cost,
            multiplier = :multiplier,
            calculated_cost = :calculated_cost,
            floor_area = :floor_area,
            additional_lot_area = :additional_lot_area,
            total_area = :total_area,
            total_fees = :total_fees,
            surcharge_percentage = :surcharge_percentage,
            surcharge_amount = :surcharge_amount,
            grand_total = :grand_total,
            prepared_by = :prepared_by,
            assessed_by = :assessed_by,
            status = :status
            WHERE id = :id");
        
        $this->db->bind(':id', $id);
        $this->db->bind(':owner_name', $data['owner_applicant_name']);
        $this->db->bind(':project_title', $data['project_title']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':division', $data['division'] ?? '');
        $this->db->bind(':project_type', $data['project_type'] ?? null);
        $this->db->bind(':project_cost', $data['project_cost'] ?? 0);
        $this->db->bind(':multiplier', $data['multiplier'] ?? 0);
        $this->db->bind(':calculated_cost', $data['calculated_cost'] ?? 0);
        $this->db->bind(':floor_area', $data['floor_area'] ?? 0);
        $this->db->bind(':additional_lot_area', $data['additional_lot_area'] ?? 0);
        $this->db->bind(':total_area', $data['total_area'] ?? 0);
        $this->db->bind(':total_fees', $data['total_fees'] ?? 0);
        $this->db->bind(':surcharge_percentage', $data['surcharge_percentage'] ?? 0);
        $this->db->bind(':surcharge_amount', $data['surcharge_amount'] ?? 0);
        $this->db->bind(':grand_total', $data['grand_total'] ?? 0);
        $this->db->bind(':prepared_by', $data['prepared_by'] ?? '');
        $this->db->bind(':assessed_by', $data['assessed_by'] ?? '');
        $this->db->bind(':status', $data['status'] ?? 'draft');
        
        if ($this->db->execute()) {
            // Update fees
            $this->db->query("DELETE FROM payment_fees WHERE payment_form_id = :payment_id");
            $this->db->bind(':payment_id', $id);
            $this->db->execute();
            
            if (isset($data['fees']) && is_array($data['fees'])) {
                foreach ($data['fees'] as $fee) {
                    $this->saveFee($id, $fee['name'], $fee['amount']);
                }
            }
            
            return true;
        }
        return false;
    }
    
    public function deletePaymentForm($id) {
        $this->db->query("DELETE FROM payment_forms WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function markAsPaid($id) {
        $this->db->query("UPDATE payment_forms SET status = 'paid' WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    private function generateReceiptNo() {
        $year = date('Y');
        $this->db->query("SELECT COUNT(*) as count FROM payment_forms WHERE YEAR(created_at) = :year");
        $this->db->bind(':year', $year);
        $result = $this->db->single();
        $count = $result['count'] + 1;
        return "OP-{$year}-" . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}


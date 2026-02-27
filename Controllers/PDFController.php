<?php
require_once ROOT_PATH . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Mpdf\Mpdf;

/**
 * PDF Controller
 */
class PDFController {
    private $paymentModel;
    private $authController;
    
    public function __construct() {
        $this->paymentModel = new Payment();
        $this->authController = new AuthController();
        $this->authController->checkAuth();
    }
    
    public function generate() {
        $id = $_GET['id'] ?? 0;
        $payment = $this->paymentModel->getPaymentForm($id);
        
        if (!$payment) {
            $_SESSION['error'] = 'Payment form not found';
            header('Location: ' . BASE_URL . '?page=payment');
            exit;
        }
        
        // Generate PDF HTML
        $html = $this->generateHTML($payment);
        
        // Create PDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'Letter',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);
        
        $mpdf->WriteHTML($html);
        
        // Output PDF
        $filename = 'Order_of_Payment_' . $payment['official_receipt_no'] . '.pdf';
        $mpdf->Output($filename, 'D'); // D = download, I = inline
    }
    
    public function preview() {
        $id = $_GET['id'] ?? 0;
        $payment = $this->paymentModel->getPaymentForm($id);
        
        if (!$payment) {
            $_SESSION['error'] = 'Payment form not found';
            header('Location: ' . BASE_URL . '?page=payment');
            exit;
        }
        
        $html = $this->generateHTML($payment);
        // Output HTML for preview
        echo $html;
        exit;
    }
    
    public function exportExcel() {
        $id = $_GET['id'] ?? 0;
        $payment = $this->paymentModel->getPaymentForm($id);
        
        if (!$payment) {
            $_SESSION['error'] = 'Payment form not found';
            header('Location: ' . BASE_URL . '?page=payment');
            exit;
        }
        
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set page orientation and size
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
        
        // Fit to 1 page width
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        
        // Set print area to A1:L42 (rows 2 to 42, columns A to L) - to include Note
        $sheet->getPageSetup()->setPrintArea('A1:L42');
        
        // Set margins for better fit
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setBottom(0.5);
        $sheet->getPageMargins()->setLeft(0.3);
        $sheet->getPageMargins()->setRight(0.3);
        $sheet->getPageMargins()->setHeader(0.2);
        $sheet->getPageMargins()->setFooter(0.2);
        
        // Header - Government Header (Row 2-4, Column G) - matching Book2.xlsx
        $sheet->setCellValue('G2', 'Republic of the Philippines');
        $sheet->getStyle('G2')->getFont()->setSize(9)->setBold(false);
        $sheet->getStyle('G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('G3', 'Municipality of Lian');
        $sheet->getStyle('G3')->getFont()->setSize(9)->setBold(false);
        $sheet->getStyle('G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('G4', 'Province of Batangas');
        $sheet->getStyle('G4')->getFont()->setSize(9)->setBold(false);
        $sheet->getStyle('G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Office Name (Row 6, Column G) - matching Book2.xlsx
        $sheet->setCellValue('G6', 'OFFICE OF THE MPDC');
        $sheet->getStyle('G6')->getFont()->setSize(12)->setBold(true);
        $sheet->getStyle('G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Owner/Applicant, Project, Location (Row 8-10) - matching Book2.xlsx format
        $sheet->setCellValue('A8', 'Owner/Applicant:');
        $sheet->getStyle('A8')->getFont()->setSize(9);
        $ownerName = strtoupper($payment['owner_applicant_name'] ?? '');
        $sheet->setCellValue('D8', $ownerName);
        $sheet->mergeCells('D8:G8');
        $sheet->getStyle('D8:G8')->getFont()->setBold(true)->setSize(9)->setUnderline(true);
        $sheet->getStyle('D8:G8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        // Date (Row 8, Column J-K) - matching Book2.xlsx
        $date = !empty($payment['date']) ? date('F d, Y', strtotime($payment['date'])) : date('F d, Y');
        $sheet->setCellValue('J8', 'Date:');
        $sheet->getStyle('J8')->getFont()->setSize(9);
        $sheet->setCellValue('K8', $date);
        $sheet->mergeCells('K8:L8');
        $sheet->getStyle('K8:L8')->getFont()->setSize(9)->setUnderline(true);
        // Remove borders/box from Date
        $sheet->getStyle('K8:L8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A9', 'Project:');
        $sheet->getStyle('A9')->getFont()->setSize(9);
        $projectTitle = strtoupper($payment['project_title'] ?? '');
        $sheet->setCellValue('D9', $projectTitle);
        $sheet->getStyle('D9')->getFont()->setBold(true)->setSize(9)->setUnderline(true);
        // Remove borders/box from Project
        
        // Division (Row 9, Column J)
        $sheet->setCellValue('J9', 'Division:');
        $sheet->getStyle('J9')->getFont()->setSize(9);
        // Division value - if exists, add underline
        if (!empty($payment['division'])) {
            $sheet->setCellValue('K9', $payment['division']);
            $sheet->mergeCells('K9:L9');
            $sheet->getStyle('K9:L9')->getFont()->setSize(9)->setUnderline(true);
            // Remove borders/box from Division
        }
        
        $sheet->setCellValue('A10', 'Location:');
        $sheet->getStyle('A10')->getFont()->setSize(9);
        $location = strtoupper($payment['location'] ?? '');
        $sheet->setCellValue('D10', $location);
        $sheet->getStyle('D10')->getFont()->setBold(true)->setSize(9)->setUnderline(true);
        // Remove borders/box from Location
        
        // Title - ORDER OF PAYMENT (Row 11, Column G) - matching Book2.xlsx
        $sheet->setCellValue('G11', 'ORDER OF PAYMENT');
        $sheet->getStyle('G11')->getFont()->setSize(12)->setBold(true);
        $sheet->getStyle('G11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Instruction (Row 13, Column A) - matching Book2.xlsx
        $sheet->setCellValue('A13', 'TO:   TREASURER/CASHIER: Please collect the corresponding amount fees specified below:');
        $sheet->getStyle('A13')->getFont()->setSize(8);
        
        // Fee Breakdown - Only Additional Fees (a-h) - always show all even if 0
        $fees = $payment['fees'] ?? [];
        
        // Create a map of fee names to amounts for quick lookup
        $feeMap = [];
        foreach ($fees as $fee) {
            $feeNameLower = strtolower(trim($fee['fee_name'] ?? ''));
            $feeMap[$feeNameLower] = floatval($fee['fee_amount'] ?? 0);
        }
        
        // Helper function to find fee amount by matching name variations
        $findFeeAmount = function($searchTerms) use ($feeMap) {
            foreach ($searchTerms as $term) {
                $termLower = strtolower(trim($term));
                foreach ($feeMap as $key => $amount) {
                    if (strpos($key, $termLower) !== false || strpos($termLower, $key) !== false) {
                        return $amount;
                    }
                }
            }
            return 0;
        };
        
        // Prepare all fee items - only Additional Fees in order, always show all even if 0
        $allFeeItems = [];
        
        // Always show all Additional Fees (a-h) in order, even if amount is 0
        $allFeeItems[] = [
            'letter' => 'a',
            'name' => 'FILING FEES',
            'amount' => $findFeeAmount(['filing fees', 'filing fee'])
        ];
        $allFeeItems[] = [
            'letter' => 'b',
            'name' => 'PRELIMINARY INSPECTION & VERIFICATION FEES',
            'amount' => $findFeeAmount(['preliminary inspection', 'preliminary inspection & verification'])
        ];
        $allFeeItems[] = [
            'letter' => 'c',
            'name' => 'LOCATIONAL CLEARANCE',
            'amount' => $findFeeAmount(['locational clearance'])
        ];
        $allFeeItems[] = [
            'letter' => 'd',
            'name' => 'LINE AND GRADE FEES',
            'amount' => $findFeeAmount(['line and grade', 'line and grade fees'])
        ];
        $allFeeItems[] = [
            'letter' => 'e',
            'name' => 'ZONING FEES',
            'amount' => $findFeeAmount(['zoning fees', 'zoning fee'])
        ];
        $allFeeItems[] = [
            'letter' => 'f',
            'name' => 'ESF',
            'amount' => $findFeeAmount(['esf'])
        ];
        $allFeeItems[] = [
            'letter' => 'g',
            'name' => 'DEVELOPMENT FEES',
            'amount' => $findFeeAmount(['development fees', 'development fee'])
        ];
        $allFeeItems[] = [
            'letter' => 'h',
            'name' => 'CERTIFICATION',
            'amount' => $findFeeAmount(['certification', 'certification fee'])
        ];
        
        // Add surcharge (i) with checkboxes - always at position 8 (index 8, letter 'i')
        if ($payment['surcharge_percentage'] > 0 || $payment['surcharge_amount'] > 0) {
            $allFeeItems[] = [
                'letter' => 'i',
                'name' => 'SURCHARGE',
                'amount' => $payment['surcharge_amount'] > 0 ? number_format($payment['surcharge_amount'], 2) : '',
                'has_checkboxes' => true
            ];
        } else {
            $allFeeItems[] = [
                'letter' => 'i',
                'name' => 'SURCHARGE',
                'amount' => '',
                'has_checkboxes' => true
            ];
        }
        
        // Add j and k
        $allFeeItems[] = [
            'letter' => 'j',
            'name' => 'PENALTIES/ADMINISTRATIVES FINES (Spe_ 1st Notice_2nd_3rd_',
            'amount' => ''
        ];
        $allFeeItems[] = [
            'letter' => 'k',
            'name' => 'OTHERS (Specify',
            'amount' => ''
        ];
        
        // Write fees - matching Book2.xlsx format (Column A for letters, B for names, I for amounts)
        $feeRow = 15;
        foreach ($allFeeItems as $item) {
            // Letter in column A - no underline
            $sheet->setCellValue('A' . $feeRow, $item['letter'] . '.');
            $sheet->getStyle('A' . $feeRow)->getFont()->setSize(10);
            $sheet->getStyle('A' . $feeRow)->getFont()->setUnderline(false);
            
            // Fee name in column B - no underline
            $sheet->setCellValue('B' . $feeRow, $item['name']);
            $sheet->getStyle('B' . $feeRow)->getFont()->setSize(10);
            $sheet->getStyle('B' . $feeRow)->getFont()->setUnderline(false);
            
            // Amount in column J (merged J:K, centered, with bottom border only)
            // Always show amount even if 0
            $amountValue = floatval($item['amount'] ?? 0);
            $sheet->setCellValue('J' . $feeRow, $amountValue > 0 ? number_format($amountValue, 2) : '');
            $sheet->mergeCells('J' . $feeRow . ':K' . $feeRow);
            $sheet->getStyle('J' . $feeRow)->getFont()->setSize(10);
            $sheet->getStyle('J' . $feeRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            // Bottom border on both J and K columns
            $sheet->getStyle('J' . $feeRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('K' . $feeRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
            
            // Remove fill colors (no box/highlight) - only borders
            
            // Add checkboxes for surcharge (row 23, column E) - matching Book2.xlsx
            if (isset($item['has_checkboxes']) && $item['has_checkboxes']) {
                $sheet->setCellValue('E' . $feeRow, '     10%        25%         50%        100%');
                $sheet->getStyle('E' . $feeRow)->getFont()->setSize(10);
            }
            
            // For item j (row 24), add specification field in column F - matching Book2.xlsx
            if ($item['letter'] === 'j') {
                $sheet->setCellValue('F' . $feeRow, '__ 1st Notice___2nd ___3rd ____');
                $sheet->mergeCells('J' . $feeRow . ':K' . $feeRow);
                $sheet->getStyle('F' . $feeRow)->getFont()->setSize(10);
            }
            
            $feeRow++;
        }
        
        // Area Section (Row 26-29) - matching Book2.xlsx format
        $areaRow = 26;
        $sheet->setCellValue('A' . $areaRow, 'l.');
        $sheet->getStyle('A' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('A' . $areaRow)->getFont()->setUnderline(false);
        $sheet->setCellValue('B' . $areaRow, 'FLOOR AREA');
        $sheet->getStyle('B' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('B' . $areaRow)->getFont()->setUnderline(false);
        $sheet->setCellValue('E' . $areaRow, number_format($payment['floor_area'] ?? 0, 2));
        $sheet->mergeCells('J' . $areaRow . ':K' . $areaRow);
        $sheet->getStyle('E' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('E' . $areaRow)->getFont()->setUnderline(false);
        $sheet->setCellValue('F' . $areaRow, 'sq.m.');
        $sheet->getStyle('F' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('F' . $areaRow)->getFont()->setUnderline(false);
        // Bottom border on both J and K columns
        $sheet->getStyle('J' . $areaRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('K' . $areaRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        
        $areaRow++;
        $sheet->setCellValue('A' . $areaRow, 'm.');
        $sheet->getStyle('A' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('A' . $areaRow)->getFont()->setUnderline(false);
        $sheet->setCellValue('B' . $areaRow, 'ADDITIONAL LOT AREA');
        $sheet->getStyle('B' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('B' . $areaRow)->getFont()->setUnderline(false);
        $sheet->setCellValue('E' . $areaRow, number_format($payment['additional_lot_area'] ?? 0, 2));
        $sheet->mergeCells('J' . $areaRow . ':K' . $areaRow);
        $sheet->getStyle('E' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('E' . $areaRow)->getFont()->setUnderline(false);
        $sheet->setCellValue('F' . $areaRow, 'sq.m.');
        $sheet->getStyle('F' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('F' . $areaRow)->getFont()->setUnderline(false);
        // Bottom border on both J and K columns
        $sheet->getStyle('J' . $areaRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('K' . $areaRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        
        $totalArea = ($payment['floor_area'] ?? 0) + ($payment['additional_lot_area'] ?? 0);
        $areaRow++;
        $sheet->setCellValue('A' . $areaRow, 'n.');
        $sheet->getStyle('A' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('A' . $areaRow)->getFont()->setUnderline(false);
        $sheet->setCellValue('B' . $areaRow, 'TOTAL AREA');
        $sheet->getStyle('B' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('B' . $areaRow)->getFont()->setUnderline(false);
        $sheet->setCellValue('E' . $areaRow, number_format($totalArea, 2));
        $sheet->mergeCells('J' . $areaRow . ':K' . $areaRow);
        $sheet->getStyle('E' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('E' . $areaRow)->getFont()->setUnderline(false);
        $sheet->setCellValue('F' . $areaRow, 'sq.m.');
        $sheet->getStyle('F' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('F' . $areaRow)->getFont()->setUnderline(false);
        // Bottom border on both J and K columns
        $sheet->getStyle('J' . $areaRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('K' . $areaRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        
        $areaRow++;
        $sheet->setCellValue('B' . $areaRow, 'pool');
        $sheet->getStyle('B' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('B' . $areaRow)->getFont()->setUnderline(false);
        $sheet->setCellValue('F' . $areaRow, 'cu.m.');
        $sheet->getStyle('F' . $areaRow)->getFont()->setSize(10);
        $sheet->getStyle('F' . $areaRow)->getFont()->setUnderline(false);
        
        // Total Amount (Row 29, H = "Total", J = amount merged J:K) - matching Book2.xlsx
        $sheet->setCellValue('H' . $areaRow, 'Total');
        $sheet->getStyle('H' . $areaRow)->getFont()->setBold(true)->setSize(10);
        $sheet->setCellValue('J' . $areaRow, number_format($payment['grand_total'] ?? 0, 2));
        $sheet->mergeCells('J' . $areaRow . ':K' . $areaRow);
        $sheet->getStyle('J' . $areaRow)->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('J' . $areaRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Bottom border on both J and K columns
        $sheet->getStyle('J' . $areaRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('K' . $areaRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        
        // FOR PAYMENT APPROVAL (Row 32, Column F merged F:H) - matching Book2.xlsx
        $sheet->setCellValue('F32', 'FOR PAYMENT APPROVAL:');
        $sheet->mergeCells('F32:H32');
        $sheet->getStyle('F32')->getFont()->setSize(8);
        $sheet->getStyle('F32')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Official Receipt No (Row 35, left side) - matching Book2.xlsx
        $sheet->setCellValue('A35', 'Official Receipt No:            ');
        $sheet->getStyle('A35')->getFont()->setSize(8);
        if (!empty($payment['official_receipt_no'])) {
            // Receipt number can be added after the label
        }
        
        // Date (Row 36, left side) - matching Book2.xlsx
        $sheet->setCellValue('A36', 'Date:');
        $sheet->getStyle('A36')->getFont()->setSize(9);
        
        // Prepared by and Assessed by (Row 38-40) - matching Book2.xlsx
        $sheet->setCellValue('B38', 'Prepared by:');
        $sheet->getStyle('B38')->getFont()->setSize(9);
        
        $sheet->setCellValue('H38', 'Assesed by:');
        $sheet->mergeCells('H38:I38');
        $sheet->getStyle('H38')->getFont()->setSize(9);
        $sheet->getStyle('H38')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('D39', $payment['prepared_by'] ?? 'LOVELY LAXA');
        $sheet->getStyle('D39')->getFont()->setBold(true)->setSize(10);
        // Remove underline from name
        $sheet->getStyle('D39')->getFont()->setUnderline(false);
        
        $sheet->setCellValue('I39', $payment['assessed_by'] ?? 'ENP. Mark Andrei L. Gubac');
        $sheet->mergeCells('I39:L39');
        $sheet->getStyle('I39')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('I39')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Remove underline from name
        $sheet->getStyle('I39')->getFont()->setUnderline(false);
        
        $sheet->setCellValue('D40', 'MPDC STAFF');
        $sheet->getStyle('D40')->getFont()->setSize(8);
        
        $sheet->setCellValue('I40', 'MPDC');
        $sheet->mergeCells('I40:L40');
        $sheet->getStyle('I40')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('I40')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Note (Row 42, merged F42:I42, yellow background) - matching Book2.xlsx
        $sheet->setCellValue('F42', 'Note: Draft only if without signature');
        $sheet->mergeCells('F42:I42');
        $sheet->getStyle('F42')->getFont()->setSize(9);
        $sheet->getStyle('F42')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFFF00');
        $sheet->getStyle('F42')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        // Set column widths (A to L) - matching Book2.xlsx format
        $sheet->getColumnDimension('A')->setWidth(3.43);   // Letters (a., b., c., etc.)
        $sheet->getColumnDimension('B')->setWidth(4.71);  // Fee names, labels
        $sheet->getColumnDimension('C')->setWidth(6.71);  // Spacer
        $sheet->getColumnDimension('D')->setWidth(8.86);  // Owner, Project, Location values
        $sheet->getColumnDimension('E')->setWidth(8.86);  // Area values, checkboxes
        $sheet->getColumnDimension('F')->setWidth(9.43);  // Units (sq.m., cu.m.), specifications
        $sheet->getColumnDimension('G')->setWidth(8.29);  // Header, title
        $sheet->getColumnDimension('H')->setWidth(7.71);  // Total label, signatures
        $sheet->getColumnDimension('I')->setWidth(3.57);  // Amounts (part 1)
        $sheet->getColumnDimension('J')->setWidth(8.43);  // Amounts (part 2, merged with I)
        $sheet->getColumnDimension('K')->setWidth(7.29);  // Date value
        $sheet->getColumnDimension('L')->setWidth(11.57); // Date continuation, signatures
        
        // Hide columns beyond L if any
        for ($col = 'M'; $col <= 'Z'; $col++) {
            $sheet->getColumnDimension($col)->setVisible(false);
        }
        
        // Output Excel file
        $filename = 'Order_of_Payment_' . ($payment['official_receipt_no'] ?? $id) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Sort fees to prioritize specific fees in order
     */
    private function sortFees($fees) {
        // Define the priority order for specific fees (with variations)
        $priorityFees = [
            'filling fees' => 1,
            'filling fee' => 1,
            'preliminary inspection & verification fees' => 2,
            'preliminary inspection and verification fees' => 2,
            'preliminary inspection & verification fee' => 2,
            'locational clearance' => 3,
            'line and grade fees' => 4,
            'line and grade fee' => 4,
            'zoning fees' => 5,
            'zoning fee' => 5,
            'esf' => 6,
            'development fees' => 7,
            'development fee' => 7,
            'certification' => 8,
            'certification fee' => 8
        ];
        
        $priorityList = [];
        $otherFees = [];
        
        foreach ($fees as $fee) {
            $feeNameLower = strtolower(trim($fee['fee_name'] ?? ''));
            $matched = false;
            
            // Check for exact match or partial match
            foreach ($priorityFees as $key => $priority) {
                if ($feeNameLower === $key || strpos($feeNameLower, $key) !== false || strpos($key, $feeNameLower) !== false) {
                    // Use the first matching priority
                    if (!isset($priorityList[$priority])) {
                        $priorityList[$priority] = $fee;
                        $matched = true;
                        break;
                    }
                }
            }
            
            if (!$matched) {
                $otherFees[] = $fee;
            }
        }
        
        // Sort priority list by order
        ksort($priorityList);
        
        // Combine: priority fees first, then other fees
        return array_merge(array_values($priorityList), $otherFees);
    }
    
    private function generateHTML($payment) {
        $totalArea = $payment['floor_area'] + $payment['additional_lot_area'];
        $surchargePercent = $payment['surcharge_percentage'];
        $surchargeAmount = $payment['surcharge_amount'];
        $grandTotal = $payment['grand_total'];
        $fees = $payment['fees'] ?? [];
        
        // Sort fees to prioritize specific fees
        $fees = $this->sortFees($fees);
        
        // Map fee letters (a-z)
        $feeLetters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                @page {
                    margin: 15mm;
                }
                body {
                    font-family: Arial, sans-serif;
                    font-size: 10pt;
                    line-height: 1.4;
                    padding: 0;
                    margin: 0;
                }
                .container {
                    max-width: 100%;
                    margin: 0 auto;
                    padding: 20px 25px;
                    border: 2px solid #000;
                    box-sizing: border-box;
                }
                .gov-header {
                    text-align: center;
                    font-size: 10pt;
                    margin-bottom: 12px;
                    margin-top: 5px;
                    line-height: 1.4;
                }
                .gov-header hr {
                    border: none;
                    border-top: 1px solid #000;
                    margin: 8px 0;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    margin-top: 5px;
                }
                .office-name {
                    font-size: 12pt;
                    font-weight: bold;
                    margin: 15px 0;
                    text-transform: uppercase;
                }
                .title {
                    font-size: 14pt;
                    font-weight: bold;
                    text-decoration: underline;
                    margin: 18px 0;
                    text-align: center;
                }
                .instruction {
                    font-size: 10pt;
                    margin: 12px 0;
                    text-align: center;
                }
                .info-section {
                    margin: 18px 0;
                    display: table;
                    width: 100%;
                }
                .info-left {
                    display: table-cell;
                    width: 60%;
                    vertical-align: top;
                    padding-right: 25px;
                }
                .info-right {
                    display: table-cell;
                    width: 40%;
                    vertical-align: top;
                    padding-left: 15px;
                }
                .info-row {
                    margin: 8px 0;
                    display: flex;
                    align-items: baseline;
                }
                .info-label {
                    font-weight: bold;
                    min-width: 130px;
                }
                .info-value {
                    flex: 1;
                    border-bottom: 1px solid #000;
                    padding: 2px 5px;
                    min-height: 20px;
                    margin-left: 5px;
                }
                .fee-section {
                    margin: 18px 0;
                }
                .fee-columns-table {
                    width: 100%;
                    border-collapse: collapse;
                    border: none;
                }
                .fee-columns-table td {
                    width: 50%;
                    vertical-align: top;
                    padding: 0;
                    border: none;
                }
                .fee-column-left {
                    padding-right: 15px;
                }
                .fee-column-right {
                    padding-left: 15px;
                }
                .fee-item {
                    margin: 6px 0;
                    line-height: 1.6;
                }
                .fee-row {
                    width: 100%;
                    table-layout: fixed;
                }
                .fee-letter {
                    font-weight: bold;
                    width: 25px;
                    padding-right: 5px;
                }
                .fee-name {
                    border-bottom: 1px solid #000;
                    padding: 2px 5px;
                    min-height: 20px;
                    width: auto;
                }
                .fee-amount {
                    text-align: right;
                    border-bottom: 1px solid #000;
                    padding: 2px 5px;
                    min-height: 20px;
                    width: 120px;
                }
                .area-section {
                    margin: 18px 0;
                }
                .area-item {
                    margin: 5px 0;
                    display: flex;
                    align-items: baseline;
                    line-height: 1.6;
                }
                .area-label {
                    font-weight: bold;
                    min-width: 25px;
                    margin-right: 5px;
                }
                .area-name {
                    min-width: 180px;
                }
                .area-value {
                    border-bottom: 1px solid #000;
                    padding: 0 5px;
                    min-height: 18px;
                    display: inline-block;
                }
                .total-section {
                    margin: 20px 0;
                    text-align: right;
                    padding-right: 0;
                }
                .total-row {
                    display: inline-block;
                    margin: 5px 0;
                }
                .total-label {
                    font-weight: bold;
                    display: inline-block;
                    min-width: 80px;
                    text-align: right;
                    margin-right: 10px;
                }
                .total-value {
                    font-weight: bold;
                    display: inline-block;
                    min-width: 120px;
                    text-align: right;
                    border-bottom: 1px solid #000;
                    padding: 0 5px;
                }
                .signature-section {
                    margin-top: 35px;
                    display: table;
                    width: 100%;
                }
                .signature-box {
                    display: table-cell;
                    width: 33.33%;
                    vertical-align: top;
                    padding: 0 12px;
                }
                .signature-box.left {
                    text-align: left;
                }
                .signature-box.center {
                    text-align: center;
                }
                .signature-box.right {
                    text-align: right;
                }
                .signature-label {
                    font-weight: bold;
                    margin-bottom: 8px;
                }
                .signature-name {
                    border-bottom: 1px solid #000;
                    padding: 2px 5px;
                    min-height: 20px;
                    margin-bottom: 8px;
                }
                .signature-title {
                    font-size: 9pt;
                    margin-top: 3px;
                }
                .receipt-section {
                    margin-top: 0;
                }
                .receipt-row {
                    margin: 6px 0;
                    display: flex;
                    align-items: baseline;
                }
                .receipt-label {
                    font-weight: bold;
                    min-width: 140px;
                }
                .receipt-value {
                    flex: 1;
                    border-bottom: 1px solid #000;
                    padding: 2px 5px;
                    min-height: 20px;
                }
                .note-section {
                    margin-top: 25px;
                    padding: 10px 15px;
                    background-color: #ffffcc;
                    border: 1px solid #000;
                    font-size: 9pt;
                    display: inline-block;
                    width: auto;
                }
                .note-title {
                    font-weight: bold;
                    display: inline;
                }
                .surcharge-checkboxes {
                    display: inline-block;
                    margin-left: 10px;
                }
                .checkbox-item {
                    display: inline-block;
                    margin: 0 5px;
                }
            </style>
        </head>
        <body>
            <div class="container">
            <div class="gov-header">
                <div>Republic of the Philippines</div>
                <div>Municipality of Lian</div>
                <div>Province of Batangas</div>
                <hr>
            </div>
            
            <div class="header">
                <div class="office-name">OFFICE OF THE MPDC</div>
            </div>
            
            <div class="info-section">
                <div class="info-left">
                    <div class="info-row">
                        <span class="info-label">Owner/Applicant:</span>
                        <span class="info-value">' . htmlspecialchars($payment['owner_applicant_name']) . '</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Project:</span>
                        <span class="info-value">' . htmlspecialchars($payment['project_title']) . '</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Location:</span>
                        <span class="info-value">' . htmlspecialchars($payment['location']) . '</span>
                    </div>
                </div>
                <div class="info-right">
                    <div class="info-row">
                        <span class="info-label">Date:</span>
                        <span class="info-value">' . date('F d, Y', strtotime($payment['date'])) . '</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Division:</span>
                        <span class="info-value">' . htmlspecialchars($payment['division'] ?? '') . '</span>
                    </div>
                </div>
            </div>
            
            <div class="title" style="text-align: center;">ORDER OF PAYMENT</div>
            
            <div class="instruction">
                TO: TREASURERS/CASHIERS: Please collect the corresponding amount fees specified below:
            </div>
            
            <div class="fee-section">
                <table class="fee-columns-table">
                    <tr>
                        <td class="fee-column-left">';
        
        // Prepare all fee items - include ALL fees from a to k
        $allFeeItems = [];
        $feeIndex = 0;
        
        // Add fees from database (a-h) - fees are already sorted by sortFees()
        foreach ($fees as $fee) {
            if ($feeIndex < 8) { // Only add up to 'h' (index 7)
                $letter = $feeLetters[$feeIndex] ?? '';
                $allFeeItems[] = [
                    'letter' => $letter,
                    'name' => htmlspecialchars($fee['fee_name']),
                    'amount' => $fee['fee_amount'] > 0 ? number_format($fee['fee_amount'], 2) : '',
                    'has_checkboxes' => false
                ];
                $feeIndex++;
            }
        }
        
        // Fill remaining fee slots (a-h) if not enough fees
        while ($feeIndex < 8) {
            $letter = $feeLetters[$feeIndex] ?? '';
            $allFeeItems[] = [
                'letter' => $letter,
                'name' => '',
                'amount' => '',
                'has_checkboxes' => false
            ];
            $feeIndex++;
        }
        
        // Add surcharge (i) - always show with checkboxes
        $allFeeItems[] = [
            'letter' => 'i',
            'name' => 'SURCHARGE',
            'amount' => $surchargeAmount > 0 ? number_format($surchargeAmount, 2) : '',
            'has_checkboxes' => true,
            'surcharge_percent' => $surchargePercent
        ];
        
        // Add j and k
        $allFeeItems[] = [
            'letter' => 'j',
            'name' => 'PENALTIES/ADMINISTRATIVES FINES',
            'amount' => '',
            'has_checkboxes' => false,
            'has_penalty_fields' => true
        ];
        $allFeeItems[] = [
            'letter' => 'k',
            'name' => 'OTHERS(Specify):',
            'amount' => '',
            'has_checkboxes' => false,
            'has_specify_field' => true
        ];
        
        // Split fees into two columns - first 6 items (a-f) in left, remaining (g-k) in right
        $itemsPerColumn = 6; // Left column: a-f, Right column: g-k
        
        // Left column (a-f)
        for ($i = 0; $i < $itemsPerColumn && $i < count($allFeeItems); $i++) {
            $item = $allFeeItems[$i];
            $html .= '
                            <div class="fee-item">
                                <table class="fee-row" style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                                    <tr>
                                        <td style="width: 25px; font-weight: bold; padding-right: 5px; vertical-align: top;">' . $item['letter'] . '.</td>
                                        <td style="padding-right: 10px; vertical-align: top;">' . $item['name'] . '</td>
                                        <td style="width: 120px; text-align: right; border-bottom: 1px solid #000; padding: 2px 5px; min-height: 20px; vertical-align: top;">' . ($item['amount'] ? $item['amount'] : '') . '</td>
                                    </tr>';
            if ($item['has_checkboxes']) {
                $checked10 = ($item['surcharge_percent'] == 10) ? '☑' : '☐';
                $checked20 = ($item['surcharge_percent'] == 20) ? '☑' : '☐';
                $checked50 = ($item['surcharge_percent'] == 50) ? '☑' : '☐';
                $checked100 = ($item['surcharge_percent'] == 100) ? '☑' : '☐';
                $html .= '
                                    <tr>
                                        <td></td>
                                        <td colspan="2" style="padding-left: 0; padding-top: 2px; font-size: 9pt;">
                                            <span style="margin-right: 8px;">' . $checked10 . '10%</span>
                                            <span style="margin-right: 8px;">' . $checked20 . '20%</span>
                                            <span style="margin-right: 8px;">' . $checked50 . '50%</span>
                                            <span>' . $checked100 . '100%</span>
                                        </td>
                                    </tr>';
            } elseif (isset($item['has_penalty_fields']) && $item['has_penalty_fields']) {
                $html .= '
                                    <tr>
                                        <td></td>
                                        <td colspan="2" style="padding-left: 0; padding-top: 2px;">
                                            <span>___1st Notice ___2nd ___3rd</span>
                                        </td>
                                    </tr>';
            }
            $html .= '
                                </table>
                            </div>';
        }
        
        $html .= '
                        </td>
                        <td class="fee-column-right">';
        
        // Right column (g-k)
        for ($i = $itemsPerColumn; $i < count($allFeeItems); $i++) {
            $item = $allFeeItems[$i];
            $html .= '
                            <div class="fee-item">
                                <table class="fee-row" style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                                    <tr>
                                        <td style="width: 25px; font-weight: bold; padding-right: 5px; vertical-align: top;">' . $item['letter'] . '.</td>
                                        <td style="padding-right: 10px; vertical-align: top;">' . $item['name'] . '</td>
                                        <td style="width: 120px; text-align: right; border-bottom: 1px solid #000; padding: 2px 5px; min-height: 20px; vertical-align: top;">' . ($item['amount'] ? $item['amount'] : '') . '</td>
                                    </tr>';
            if ($item['has_checkboxes']) {
                $checked10 = ($item['surcharge_percent'] == 10) ? '☑' : '☐';
                $checked20 = ($item['surcharge_percent'] == 20) ? '☑' : '☐';
                $checked50 = ($item['surcharge_percent'] == 50) ? '☑' : '☐';
                $checked100 = ($item['surcharge_percent'] == 100) ? '☑' : '☐';
                $html .= '
                                    <tr>
                                        <td></td>
                                        <td colspan="2" style="padding-left: 0; padding-top: 2px; font-size: 9pt;">
                                            <span style="margin-right: 8px;">' . $checked10 . '10%</span>
                                            <span style="margin-right: 8px;">' . $checked20 . '20%</span>
                                            <span style="margin-right: 8px;">' . $checked50 . '50%</span>
                                            <span>' . $checked100 . '100%</span>
                                        </td>
                                    </tr>';
            } elseif (isset($item['has_penalty_fields']) && $item['has_penalty_fields']) {
                $html .= '
                                    <tr>
                                        <td></td>
                                        <td colspan="2" style="padding-left: 0; padding-top: 2px;">
                                            <span>___1st Notice ___2nd ___3rd</span>
                                        </td>
                                    </tr>';
            } elseif (isset($item['has_specify_field']) && $item['has_specify_field']) {
                $html .= '
                                    <tr>
                                        <td></td>
                                        <td style="border-bottom: 1px solid #000; padding: 2px 5px; min-height: 20px; padding-left: 0;"></td>
                                        <td style="width: 120px; text-align: right; border-bottom: 1px solid #000; padding: 2px 5px; min-height: 20px;"></td>
                                    </tr>';
            }
            $html .= '
                                </table>
                            </div>';
        }
        
        $html .= '
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="area-section">
                <div class="area-item">
                    <span class="area-label">l.</span>
                    <span class="area-name">FLOOR AREA:</span>
                    <span class="area-value" style="min-width: 100px; margin-left: 5px;"></span>
                    <span style="margin-left: 5px;">sq.m.</span>
                </div>
                <div class="area-item">
                    <span class="area-label">m.</span>
                    <span class="area-name">ADDITIONAL LOT AREA:</span>
                    <span class="area-value" style="min-width: 100px; margin-left: 5px;"></span>
                    <span style="margin-left: 5px;">sq.m.</span>
                </div>
                <div class="area-item">
                    <span class="area-label">n.</span>
                    <span class="area-name">TOTAL AREA:</span>
                    <span class="area-value" style="min-width: 100px; margin-left: 5px;"></span>
                    <span style="margin-left: 5px;">sq.m.</span>
                </div>
                <div class="area-item">
                    <span class="area-label">pool:</span>
                    <span class="area-name"></span>
                    <span class="area-value" style="min-width: 100px; margin-left: 5px;"></span>
                    <span style="margin-left: 5px;">cu.m.</span>
                </div>
            </div>
            
            <div class="total-section">
                <div class="total-row">
                    <span class="total-label">Total -</span>
                    <span class="total-value"></span>
                </div>
            </div>
            
            <div style="margin-top: 30px; text-align: center;">
                <div style="font-weight: bold; margin-bottom: 8px;">FOR PAYMENT APPROVAL:</div>
            </div>
            
            <div class="signature-section" style="margin-top: 20px;">
                <div class="signature-box left">
                    <div class="receipt-row">
                        <span class="receipt-label">Official Receipt No:</span>
                        <span class="receipt-value">' . htmlspecialchars($payment['official_receipt_no'] ?? '') . '</span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Date:</span>
                        <span class="receipt-value"></span>
                    </div>
                </div>
                <div class="signature-box center" style="width: 33.33%;">
                    <!-- Empty center space -->
                </div>
                <div class="signature-box right">
                    <!-- Empty right space -->
                </div>
            </div>
            
            <div class="signature-section" style="margin-top: 25px;">
                <div class="signature-box left">
                    <div class="signature-label">Prepared by:</div>
                    <div class="signature-name" style="min-height: 25px; margin-bottom: 5px;"></div>
                    <div class="signature-title" style="font-weight: bold;">' . htmlspecialchars($payment['prepared_by'] ?? 'LOVELY LAXA') . '</div>
                    <div class="signature-title">MPDC STAFF</div>
                </div>
                <div class="signature-box center" style="width: 33.33%;">
                    <!-- Empty center space -->
                </div>
                <div class="signature-box right">
                    <div class="signature-label">Assessed by:</div>
                    <div class="signature-name" style="min-height: 25px; margin-bottom: 5px;"></div>
                    <div class="signature-title" style="font-weight: bold;">' . htmlspecialchars($payment['assessed_by'] ?? 'ENP. Mark Andrei L. Gubac.') . '</div>
                    <div class="signature-title">MPDC</div>
                </div>
            </div>
            
            <div class="note-section">
                <span class="note-title">Note:</span> Draft only if without signature
            </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}



<?php
/**
 * Project Types Configuration
 * Defines project types and their fee structures based on cost ranges
 */
class ProjectTypes {
    
    public static function getProjectTypes() {
        return [
            'A' => [
                'name' => 'SINGLE RESIDENTIAL STRUCTURE ATTACHED OR DETACHED',
                'short' => 'Single Residential',
                'fee_ranges' => [
                    ['min' => 0, 'max' => 100000, 'fee' => 970.00, 'label' => 'P100,000 and below'],
                    ['min' => 100001, 'max' => 200000, 'fee' => 0, 'label' => 'Over P100,000 to P200,000'],
                    ['min' => 200001, 'max' => PHP_INT_MAX, 'fee' => 970.00, 'label' => 'Over P200,000']
                ]
            ],
            'B' => [
                'name' => 'APARTMENTS/TOWNHOUSES',
                'short' => 'Apartments/Townhouses',
                'multiplier' => 1200, // Per sq.m. or unit
                'fee_ranges' => [
                    ['min' => 0, 'max' => 500000, 'fee' => 1440, 'label' => 'P500,000 and below'],
                    ['min' => 500001, 'max' => 2000000, 'fee' => 0, 'label' => 'Over P500,000 to 2 Million'],
                    ['min' => 2000001, 'max' => PHP_INT_MAX, 'fee' => 0, 'label' => 'Over 2 Million']
                ]
            ],
            'C' => [
                'name' => 'DORMITORIES',
                'short' => 'Dormitories',
                'fee_ranges' => [
                    ['min' => 0, 'max' => 2000000, 'fee' => 3600, 'label' => 'P 2 Million and below'],
                    ['min' => 2000001, 'max' => PHP_INT_MAX, 'fee' => 0, 'label' => 'Over 2 Million']
                ]
            ],
            'D' => [
                'name' => 'INSTITUTIONAL',
                'short' => 'Institutional',
                'fee_ranges' => [
                    ['min' => 0, 'max' => 2000000, 'fee' => 2800, 'label' => 'P 2 Million and below'],
                    ['min' => 2000001, 'max' => PHP_INT_MAX, 'fee' => 0, 'label' => 'Over 2 Million']
                ]
            ],
            'E' => [
                'name' => 'COMMERCIAL, INDUSTRIAL AND AGRO-INDUSTRIAL PROJECTS',
                'short' => 'Commercial/Industrial',
                'fee_ranges' => [
                    ['min' => 0, 'max' => 100000, 'fee' => 1440, 'label' => 'Below P100,000'],
                    ['min' => 100001, 'max' => 500000, 'fee' => 0, 'label' => 'Over P100,000 - P500,000'],
                    ['min' => 500001, 'max' => 1000000, 'fee' => 0, 'label' => 'Over P500,000'],
                    ['min' => 1000001, 'max' => 2000000, 'fee' => 0, 'label' => 'Over P1 Million - P2 Million'],
                    ['min' => 2000001, 'max' => PHP_INT_MAX, 'fee' => 0, 'label' => 'Over 2 Million']
                ]
            ],
            'F' => [
                'name' => 'SPECIAL USES/SPECIAL PROJECTS',
                'short' => 'Special Uses',
                'description' => '(Gasoline Station, Cell Sites, Slaughter House, Treatment Plants, etc.)',
                'fee_ranges' => [
                    ['min' => 0, 'max' => 2000000, 'fee' => 0, 'label' => 'P 2 Million and below'],
                    ['min' => 2000001, 'max' => PHP_INT_MAX, 'fee' => 5200.00, 'label' => 'Over 2 Million']
                ]
            ],
            'G' => [
                'name' => 'ALTERATION/EXPANSION',
                'short' => 'Alteration/Expansion',
                'description' => '(affected areas/cost only)',
                'fee_ranges' => [
                    ['min' => 0, 'max' => PHP_INT_MAX, 'fee' => 0, 'label' => 'Based on affected areas/cost']
                ]
            ]
        ];
    }
    
    public static function getProjectType($type) {
        $types = self::getProjectTypes();
        return $types[$type] ?? null;
    }
    
    public static function calculateFee($projectType, $projectCost) {
        $type = self::getProjectType($projectType);
        if (!$type) {
            return 0;
        }
        
        $projectCost = floatval($projectCost);
        
        // Find the fee range that matches the project cost
        foreach ($type['fee_ranges'] as $range) {
            if ($projectCost >= $range['min'] && $projectCost <= $range['max']) {
                return $range['fee'];
            }
        }
        
        return 0;
    }
    
    public static function calculateCost($floorArea, $multiplier, $lotArea = 0, $lotMultiplier = 0) {
        $floorCost = floatval($floorArea) * floatval($multiplier);
        $lotCost = floatval($lotArea) * floatval($lotMultiplier);
        return $floorCost + $lotCost;
    }
    
    public static function getMultipliers() {
        return [
            'residential' => [
                'lot' => 5000,
                'floor' => 25000,
                'formula' => '30000/35'
            ],
            'commercial' => [
                'lot' => 10000,
                'floor' => 35000,
                'formula' => '45000'
            ]
        ];
    }
    
    /**
     * Calculate Locational Clearance Fee based on category and cost
     * Formula: Base + (1/10 of 1% of cost in excess) = Base + 0.001 * (cost - threshold)
     */
    public static function calculateLocationalClearanceFee($category, $cost) {
        $cost = floatval($cost);
        
        // Map category codes to names
        $categoryMap = [
            'A' => 'Single Residential',
            'B' => 'Apartments/Townhouses',
            'C' => 'Dormitories',
            'D' => 'Institutional',
            'E' => 'Commercial/Industrial',
            'F' => 'Special Uses',
            'G' => 'Alteration/Expansion'
        ];
        
        $categoryName = is_numeric($category) ? ($categoryMap[$category] ?? '') : $category;
        
        switch ($categoryName) {
            case 'Single Residential':
            case 'SINGLE RESIDENTIAL STRUCTURE ATTACHED OR DETACHED':
                if ($cost <= 100000) {
                    return 298.00;
                } elseif ($cost <= 200000) {
                    return 576.00;
                } else {
                    // ₱720 + 1/10 of 1% of cost in excess of ₱200,000
                    return 720.00 + (0.001 * ($cost - 200000));
                }
                
            case 'Apartments/Townhouses':
            case 'APARTMENTS/TOWNHOUSES':
                if ($cost <= 500000) {
                    return 1440.00;
                } elseif ($cost <= 2000000) {
                    return 2160.00;
                } else {
                    // ₱3600 + 1/10 of 1% of cost in excess of ₱2,000,000
                    return 3600.00 + (0.001 * ($cost - 2000000));
                }
                
            case 'Dormitories':
            case 'DORMITORIES':
                if ($cost <= 2000000) {
                    return 3600.00;
                } else {
                    // ₱3600 + 1/10 of 1% of cost in excess of ₱2,000,000
                    return 3600.00 + (0.001 * ($cost - 2000000));
                }
                
            case 'Institutional':
            case 'INSTITUTIONAL':
                if ($cost <= 2000000) {
                    return 2800.00;
                } else {
                    // ₱2800 + 1/10 of 1% of cost in excess of ₱2,000,000
                    return 2800.00 + (0.001 * ($cost - 2000000));
                }
                
            case 'Commercial/Industrial':
            case 'COMMERCIAL, INDUSTRIAL AND AGRO-INDUSTRIAL PROJECTS':
                if ($cost <= 100000) {
                    return 1440.00;
                } elseif ($cost <= 500000) {
                    return 2160.00;
                } elseif ($cost <= 1000000) {
                    return 3600.00;
                } elseif ($cost <= 2000000) {
                    return 5200.00;
                } else {
                    // ₱5200 + 1/10 of 1% of cost in excess of ₱2,000,000
                    return 5200.00 + (0.001 * ($cost - 2000000));
                }
                
            case 'Special Uses':
            case 'SPECIAL USES/SPECIAL PROJECTS':
                if ($cost <= 2000000) {
                    return 3600.00;
                } else {
                    // ₱5200 + 1/10 of 1% of cost in excess of ₱2,000,000
                    return 5200.00 + (0.001 * ($cost - 2000000));
                }
                
            case 'Alteration/Expansion':
            case 'ALTERATION/EXPANSION':
                // Based on affected areas/cost only - same as Single Residential
                if ($cost <= 100000) {
                    return 298.00;
                } elseif ($cost <= 200000) {
                    return 576.00;
                } else {
                    return 720.00 + (0.001 * ($cost - 200000));
                }
                
            default:
                return 0.00;
        }
    }
}


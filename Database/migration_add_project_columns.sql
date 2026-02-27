-- Migration: Add missing columns to payment_forms table
-- Date: 2025-11-13
-- Description: Adds project_type, project_cost, multiplier, and calculated_cost columns

ALTER TABLE `payment_forms`
ADD COLUMN `project_type` VARCHAR(10) DEFAULT NULL AFTER `division`,
ADD COLUMN `project_cost` DECIMAL(15,2) DEFAULT 0.00 AFTER `project_type`,
ADD COLUMN `multiplier` DECIMAL(10,2) DEFAULT 0.00 AFTER `project_cost`,
ADD COLUMN `calculated_cost` DECIMAL(15,2) DEFAULT 0.00 AFTER `multiplier`;


<?php
/**
 * Password Setup Script
 * This script generates correct password hashes for default users
 */

// Generate password hashes
$adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
$mpdcPassword = password_hash('admin123', PASSWORD_BCRYPT);

echo "Password Hash Generation Script\n";
echo "================================\n\n";
echo "Admin Password (admin123):\n";
echo $adminPassword . "\n\n";
echo "MPDC Password (admin123):\n";
echo $mpdcPassword . "\n\n";

// SQL statements
echo "SQL INSERT statements:\n";
echo "======================\n\n";
echo "DELETE FROM users WHERE username IN ('admin', 'mpdc');\n\n";
echo "INSERT INTO users (username, password, full_name, role) VALUES\n";
echo "('admin', '" . $adminPassword . "', 'Administrator', 'admin'),\n";
echo "('mpdc', '" . $mpdcPassword . "', 'MPDC Staff', 'mpdc_staff');\n\n";

// Save to file
$sql = "DELETE FROM users WHERE username IN ('admin', 'mpdc');\n\n";
$sql .= "INSERT INTO users (username, password, full_name, role) VALUES\n";
$sql .= "('admin', '" . $adminPassword . "', 'Administrator', 'admin'),\n";
$sql .= "('mpdc', '" . $mpdcPassword . "', 'MPDC Staff', 'mpdc_staff');\n";

file_put_contents('Database/update_passwords.sql', $sql);
echo "SQL saved to: Database/update_passwords.sql\n";
echo "\nRun this SQL in phpMyAdmin or MySQL to update passwords.\n";


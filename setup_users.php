<?php
/**
 * User Setup Script
 * Run this file in your browser to set up default users with correct passwords
 * URL: http://localhost/Payment/setup_users.php
 */

require_once __DIR__ . '/config.php';

// Check if database connection works
try {
    $db = new Database();
    echo "<h2>User Setup Script</h2>";
    echo "<p>Setting up default users...</p>";
    
    // Generate password hashes
    $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $mpdcPassword = password_hash('admin123', PASSWORD_BCRYPT);
    
    // Delete existing default users
    $db->query("DELETE FROM users WHERE username IN ('admin', 'mpdc')");
    $db->execute();
    
    // Insert admin user
    $db->query("INSERT INTO users (username, password, full_name, role) VALUES (:username, :password, :full_name, :role)");
    $db->bind(':username', 'admin');
    $db->bind(':password', $adminPassword);
    $db->bind(':full_name', 'Administrator');
    $db->bind(':role', 'admin');
    if ($db->execute()) {
        echo "<p>✅ Admin user created</p>";
    }
    
    // Insert MPDC staff user
    $db->query("INSERT INTO users (username, password, full_name, role) VALUES (:username, :password, :full_name, :role)");
    $db->bind(':username', 'mpdc');
    $db->bind(':password', $mpdcPassword);
    $db->bind(':full_name', 'MPDC Staff');
    $db->bind(':role', 'mpdc_staff');
    if ($db->execute()) {
        echo "<p>✅ MPDC staff user created</p>";
    }
    
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>✅ Users created successfully!</h3>";
    echo "<p><strong>Admin User:</strong></p>";
    echo "<ul>";
    echo "<li>Username: <strong>admin</strong></li>";
    echo "<li>Password: <strong>admin123</strong></li>";
    echo "</ul>";
    echo "<p><strong>MPDC Staff User:</strong></p>";
    echo "<ul>";
    echo "<li>Username: <strong>mpdc</strong></li>";
    echo "<li>Password: <strong>admin123</strong></li>";
    echo "</ul>";
    echo "<p><a href='" . BASE_URL . "?page=login' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<p><strong>⚠️ Security Note:</strong> Change these default passwords after first login!</p>";
    echo "</div>";
    
    // Optionally delete this file for security
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<p><strong>🔒 Security:</strong> Delete this file (setup_users.php) after setup is complete!</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Make sure:</p>";
    echo "<ul>";
    echo "<li>Database is running (MySQL in XAMPP)</li>";
    echo "<li>Database 'payment_system' exists</li>";
    echo "<li>Database credentials in config.php are correct</li>";
    echo "</ul>";
    echo "</div>";
}


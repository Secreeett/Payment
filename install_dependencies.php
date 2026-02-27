<?php
/**
 * Install Composer Dependencies Helper
 * This script helps install PhpSpreadsheet and other dependencies
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Install Dependencies</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin: 15px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        code { background: #f8f9fa; padding: 2px 5px; border-radius: 3px; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
    </style>
</head>
<body>
<div class='container'>
    <h1>📦 Install Composer Dependencies</h1>";

// Check if Composer is installed
$composerInstalled = false;
$composerPath = '';

// Check common Composer locations
$possiblePaths = [
    'composer',
    'composer.phar',
    'C:\\ProgramData\\ComposerSetup\\bin\\composer.bat'
];

// Try to get username for user-specific path
$username = getenv('USERNAME') ?: getenv('USER');
if ($username) {
    $possiblePaths[] = 'C:\\Users\\' . $username . '\\AppData\\Roaming\\Composer\\vendor\\bin\\composer.bat';
}

foreach ($possiblePaths as $path) {
    $output = [];
    $returnVar = 0;
    exec("$path --version 2>&1", $output, $returnVar);
    if ($returnVar === 0) {
        $composerInstalled = true;
        $composerPath = $path;
        break;
    }
}

if (!$composerInstalled) {
    // Check if PHP exec is available
    if (!function_exists('exec')) {
        echo "<div class='error'>
            <h2>❌ PHP exec() function is disabled</h2>
            <p>PHP's exec() function is disabled. Please enable it in php.ini or install Composer manually.</p>
        </div>";
    } else {
        echo "<div class='warning'>
            <h2>⚠️ Composer Not Found</h2>
            <p>Composer is not installed or not in your PATH. Please install Composer first.</p>
        </div>";
        
        echo "<div class='step'>
            <h3>Step 1: Install Composer</h3>
            <p>Download and install Composer from:</p>
            <p><a href='https://getcomposer.org/download/' target='_blank'>https://getcomposer.org/download/</a></p>
            <p>For Windows:</p>
            <ol>
                <li>Download Composer-Setup.exe from the link above</li>
                <li>Run the installer and follow the instructions</li>
                <li>Make sure to add Composer to your PATH</li>
                <li>Restart your command prompt or terminal</li>
            </ol>
        </div>";
        
        echo "<div class='step'>
            <h3>Step 2: Install Dependencies</h3>
            <p>After installing Composer, run this command in your terminal:</p>
            <pre>cd " . __DIR__ . "
composer install</pre>
            <p>Or use the command below if Composer is installed in a specific location:</p>
            <pre>cd " . __DIR__ . "
php composer.phar install</pre>
        </div>";
        
        echo "<div class='info'>
            <h3>Alternative: Manual Installation</h3>
            <p>If you cannot install Composer, you can manually download PhpSpreadsheet:</p>
            <ol>
                <li>Download PhpSpreadsheet from: <a href='https://github.com/PHPOffice/PhpSpreadsheet/releases' target='_blank'>https://github.com/PHPOffice/PhpSpreadsheet/releases</a></li>
                <li>Extract the files to the <code>vendor/phpoffice/phpspreadsheet</code> directory</li>
                <li>Download mPDF from: <a href='https://github.com/mpdf/mpdf/releases' target='_blank'>https://github.com/mpdf/mpdf/releases</a></li>
                <li>Extract the files to the <code>vendor/mpdf/mpdf</code> directory</li>
                <li>Create <code>vendor/autoload.php</code> manually or use a simple autoloader</li>
            </ol>
        </div>";
    }
} else {
    echo "<div class='success'>
        <h2>✅ Composer Found!</h2>
        <p>Composer is installed at: <code>$composerPath</code></p>
    </div>";
    
    // Try to install dependencies
    echo "<div class='info'>
        <h3>Installing Dependencies...</h3>
        <p>Running: <code>$composerPath install</code></p>
    </div>";
    
    $output = [];
    $returnVar = 0;
    $command = "$composerPath install 2>&1";
    exec($command, $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "<div class='success'>
            <h2>✅ Dependencies Installed Successfully!</h2>
            <p>PhpSpreadsheet and mPDF have been installed.</p>
        </div>";
        
        // Show output
        if (!empty($output)) {
            echo "<div class='info'>
                <h3>Installation Output:</h3>
                <pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>
            </div>";
        }
    } else {
        echo "<div class='error'>
            <h2>❌ Installation Failed</h2>
            <p>There was an error installing dependencies. Please run this command manually:</p>
            <pre>$composerPath install</pre>
        </div>";
        
        // Show output
        if (!empty($output)) {
            echo "<div class='error'>
                <h3>Error Output:</h3>
                <pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>
            </div>";
        }
    }
}

// Check if vendor/autoload.php exists
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "<div class='success'>
        <h2>✅ Vendor Autoloader Found</h2>
        <p>The vendor/autoload.php file exists. Dependencies should be installed.</p>
    </div>";
} else {
    echo "<div class='warning'>
        <h2>⚠️ Vendor Autoloader Not Found</h2>
        <p>The vendor/autoload.php file does not exist. Dependencies need to be installed.</p>
    </div>";
}

echo "</div>
</body>
</html>";
?>


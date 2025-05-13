<?php
// Enable error display for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Information</h1>";

// Check session
session_start();
echo "<h2>Session Information</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check if Database.php exists and can be included
echo "<h2>Database File Check</h2>";
$dbFile = '../Database/Database.php';
if (file_exists($dbFile)) {
    echo "Database.php exists.<br>";
    
    // Try to include it
    try {
        require_once $dbFile;
        echo "Database.php included successfully.<br>";
        
        // Try to create a database instance
        try {
            $db = new Database();
            echo "Database instance created successfully.<br>";
            
            // Try to open a connection
            if ($db->openConnection()) {
                echo "Database connection opened successfully.<br>";
                
                // Try a simple query
                $result = $db->select("SELECT 1 as test", "", []);
                echo "Test query result: ";
                print_r($result);
                
                $db->closeConnection();
                echo "<br>Database connection closed.<br>";
            } else {
                echo "Failed to open database connection.<br>";
            }
        } catch (Exception $e) {
            echo "Error creating Database instance: " . $e->getMessage() . "<br>";
        }
    } catch (Exception $e) {
        echo "Error including Database.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "Database.php does not exist at path: $dbFile<br>";
}

// Check if opportunity ID is provided
echo "<h2>Request Parameters</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

// Check if OpportunityController.php exists
echo "<h2>Controller File Check</h2>";
$controllerFile = '../Controllers/OpportunityController.php';
if (file_exists($controllerFile)) {
    echo "OpportunityController.php exists.<br>";
} else {
    echo "OpportunityController.php does not exist at path: $controllerFile<br>";
}

// Check PHP version and extensions
echo "<h2>PHP Information</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Loaded Extensions: <br>";
$extensions = get_loaded_extensions();
echo "<ul>";
foreach ($extensions as $extension) {
    echo "<li>$extension</li>";
}
echo "</ul>";

// Check server information
echo "<h2>Server Information</h2>";
echo "<pre>";
print_r($_SERVER);
echo "</pre>";
?>
<?php
/**
 * Script to add auth token security to all PHP files that handle authentication
 * This script will scan all PHP files in the project and add the auth token code
 * to files that have session management but don't already have the auth token.
 */

// Define the directories to scan
$directories = [
    'Admin',
    'Host',
    'Traveler',
    'Common',
    'Controllers'
];

// Define the auth token code to add
$authTokenCode = "
// Add a session token for additional security
if (!isset(\$_SESSION['auth_token'])) {
    \$_SESSION['auth_token'] = bin2hex(random_bytes(32));
}
";

// Counter for modified files
$modifiedFiles = 0;

// Function to check if a file needs the auth token
function needsAuthToken($content) {
    // Check if the file has session_start but doesn't have auth_token
    return (
        strpos($content, 'session_start()') !== false && 
        strpos($content, '$_SESSION[\'userID\']') !== false && 
        strpos($content, '$_SESSION[\'auth_token\']') === false
    );
}

// Function to add auth token to a file
function addAuthToken($filePath) {
    global $authTokenCode, $modifiedFiles;
    
    $content = file_get_contents($filePath);
    
    if (needsAuthToken($content)) {
        // Find the position after session checks
        $pattern = '/session_start\(\);.*?(?:if\s*\([^\)]+\)\s*\{[^}]+\}|exit;)/s';
        if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $insertPos = $matches[0][1] + strlen($matches[0][0]);
            
            // Insert the auth token code
            $newContent = substr($content, 0, $insertPos) . $authTokenCode . substr($content, $insertPos);
            
            // Write the modified content back to the file
            file_put_contents($filePath, $newContent);
            
            echo "Added auth token to: $filePath\n";
            $modifiedFiles++;
        } else {
            echo "Could not find insertion point in: $filePath\n";
        }
    }
}

// Scan directories and process files
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        echo "Directory not found: $dir\n";
        continue;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            addAuthToken($file->getPathname());
        }
    }
}

echo "\nCompleted! Modified $modifiedFiles files.\n";

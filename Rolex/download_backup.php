<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'inventory_system'; // ✅ Change this

// Full path to mysqldump
$mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe'; // ✅ Update this path as needed

// Backup file name
$backupFile = 'backup_' . date("Y-m-d_H-i-s") . '.sql';

// Send headers
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"$backupFile\"");

// Escape password (in case it contains special chars)
$escapedPassword = escapeshellarg($password);

// Dump command
$command = "\"$mysqldumpPath\" --user=$username --password=$password --host=$host $database";

// Execute and stream output
passthru($command, $result);

// If it fails
if ($result !== 0) {
    echo "Failed to generate backup. Please check your configuration.";
}
?>

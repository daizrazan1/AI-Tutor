<?php
// Database connection configuration using environment variables or hardcoded for setup
$host = 'localhost'; 
$db   = 'u237055794_team06';
$user = 'u237055794_team06';
$pass = 'eQeZ6c:A6r';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Database connected successfully.\n";
} catch (\PDOException $e) {
     echo "Database connection failed: " . $e->getMessage() . "\n";
}

/**
 * Global function to get PDO instance
 */
function getDbConnection() {
    global $pdo;
    return $pdo;
}

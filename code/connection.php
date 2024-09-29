<?php
$host = 'localhost';
$dbname = 'realtime_chatapp';
$username = 'root';
$password = '';

$dsn = "mysql:host=$host;dbname=$dbname";

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $username, $password);

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
}

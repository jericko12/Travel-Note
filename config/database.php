<?php
$host = 'localhost';
$dbname = 'travel_notes';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $_SESSION['error'] = "Database Connection Error: " . $e->getMessage();
    header("Location: error.php");
    exit();
}
?> 
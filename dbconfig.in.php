<?php

$dbHost = 'localhost';
$dbName = 'ticketSystem';
$dbUser = 'ticketuser';
$dbPass = 'Ticket@334';

$connectionString =
    "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $pdo = new PDO(
        $connectionString,
        $dbUser,
        $dbPass,
        $options
    );
} catch (PDOException $exception) {
    die('Database connection failed: ' . $exception->getMessage());
}

?>
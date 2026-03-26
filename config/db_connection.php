<?php

date_default_timezone_set('Asia/Yangon');

$host = "localhost";
$dbname = "db_magazine-system";
$username = "root";
$password = "";

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+06:30'"
    ];

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
}
catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
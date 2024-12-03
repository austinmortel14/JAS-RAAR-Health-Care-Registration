<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'mortel';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode([
        "message" => "Database connection failed: " . $conn->connect_error,
        "statusCode" => 400
    ]));
}

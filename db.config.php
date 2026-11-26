<?php
$host = getenv('PIZZERIA_DB_HOST') ?: 'localhost';
$dbname = getenv('PIZZERIA_DB_NAME') ?: 'pizzeria';
$username = getenv('PIZZERIA_DB_USER');
$password = getenv('PIZZERIA_DB_PASSWORD');

if ($username === false || $password === false) {
    error_log('Pizzeria database credentials are not configured.');
    http_response_code(500);
    die('Database configuration is missing.');
}

try {
    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new mysqli_sql_exception($conn->connect_error);
    }

    if (!$conn->set_charset('utf8mb4')) {
        throw new mysqli_sql_exception($conn->error);
    }
} catch (mysqli_sql_exception $exception) {
    error_log('Pizzeria database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    die('Database connection failed. Check the server configuration.');
}
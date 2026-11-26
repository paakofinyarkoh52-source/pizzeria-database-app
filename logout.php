<?php
session_start();

require_once __DIR__ . '/db.config.php';

if (!empty($_SESSION['user_id'])) {
    $userId = (int) $_SESSION['user_id'];

    $stmt = $conn->prepare("
        UPDATE users
        SET logged_in = 0
        WHERE user_id = ?
    ");

    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }
}

$_SESSION = [];

session_destroy();

header("Location: LogIn.php");
exit;

<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header("Location: LogIn.php");
    exit;
}

if (empty($_SESSION['order_id'])) {
    header("Location: Create_order.php");
    exit;
}

header("Location: Add_item.php");
exit;

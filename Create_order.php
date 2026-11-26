<?php
global $conn;
session_start();
require_once __DIR__ . '/db.config.php';
require_once __DIR__ . '/navigation.php';
render_nav();
?>

<form method="POST">
    Table number: <label>
        <input type="number" name="table">
    </label><br>
    <button type="submit">Create Order</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id'])) {
        echo "Please log in first.";
        exit;
    }

    $userId = (int) $_SESSION['user_id'];
    if ($userId <= 0) {
        echo "Invalid session user.";
        exit;
    }

    $tableNumber = isset($_POST['table']) ? (int) $_POST['table'] : 0;
    if ($tableNumber <= 0) {
        echo "Please provide a valid table number.";
        exit;
    }

    $userStmt = $conn->prepare(
        /** @lang MySQL */
        "SELECT user_id
         FROM `users`
         WHERE user_id = ?"
    );

    if (! $userStmt) {
        die("Database error: " . $conn->error);
    }

    $userStmt->bind_param("i", $userId);
    $userStmt->execute();

    if (! $userStmt->get_result()->fetch_assoc()) {
        echo "User not found. Please log in again.";
        exit;
    }

    $stmt = $conn->prepare(
        /** @lang MySQL */
        "INSERT INTO orders (date, user_id, status, total, table_number)
         VALUES (NOW(), ?, 'at_table', 0.00, ?)"
    );

    if (! $stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("ii", $userId, $tableNumber);

    if ($stmt->execute()) {
        $_SESSION['order_id'] = $conn->insert_id;
        echo "Order created! Order ID: " . $_SESSION['order_id'];
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

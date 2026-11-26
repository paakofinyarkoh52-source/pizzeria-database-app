<?php
session_start();
require_once __DIR__ . '/db.config.php';
require_once __DIR__ . '/navigation.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Email and password are required.";
    } else {

        $stmt = $conn->prepare("
            SELECT user_id, email, password, name
            FROM users
            WHERE email = ?
        ");

        if (!$stmt) {
            die("Database error: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = "Invalid credentials!";
        } else {

            // Prevent session fixation
            session_regenerate_id(true);

            // Update login info
            $updateStmt = $conn->prepare("
                UPDATE users
                SET last_login = NOW(), logged_in = 1
                WHERE user_id = ?
            ");

            if ($updateStmt) {
                $userId = (int)$user['user_id'];
                $updateStmt->bind_param("i", $userId);
                $updateStmt->execute();
            }

            // Store session data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect after login
            header("Location: Create_order.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<?php render_nav(); ?>

<h2>Login</h2>

<?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST">
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>

</body>
</html>
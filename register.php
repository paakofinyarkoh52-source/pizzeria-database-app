<?php
require_once __DIR__ . '/db.config.php';
require_once __DIR__ . '/navigation.php';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = "All fields are required.";
    } else {

        // 1. Check if email already exists
        $check = $conn->prepare("
            SELECT user_id 
            FROM users
            WHERE email = ?
        ");

        if (!$check) {
            die("Prepare failed: " . $conn->error);
        }

        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already registered.";
        } else {

            // 2. Hash password securely
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 3. Insert new user
            $stmt = $conn->prepare("
                INSERT INTO users (name, email, password, last_login, logged_in)
                VALUES (?, ?, ?, NOW(), 0)
                ");

            if (!$stmt) {
                die("Insert prepare failed: " . $conn->error);
            }

            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            if ($stmt->execute()) {
                header("Location: LogIn.php");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
<?php render_nav(); ?>
<h2>Register</h2>

<?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>

<form method="POST">
    Name: <input type="text" name="name" required><br><br>
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Register</button>
</form>

</body>
</html>
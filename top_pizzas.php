<?php
global $conn;
require_once __DIR__ . '/db.config.php';

/* ---------------- DELETE ---------------- */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    if ($id > 0) {
        $conn->query("DELETE FROM order_items WHERE product_id = $id");

        $deleteSql = "DELETE FROM products WHERE product_id = $id AND type = 'pizza'";

        if ($conn->query($deleteSql)) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?sort=" . urlencode($_GET['sort'] ?? 'total'));
            exit;
        } else {
            $delete_error = $conn->error;
        }
    }
}

require_once __DIR__ . '/navigation.php';
render_nav();

if (isset($delete_error)) {
    echo "<p style='color:red;'>Error deleting pizza: " . $delete_error . "</p>";
}

/* ---------------- SORTING ---------------- */
$allowed_sorts = ['name', 'size', 'total'];
$sort = $_GET['sort'] ?? 'total';

if (!in_array($sort, $allowed_sorts)) {
    $sort = 'total';
}

/* ---------------- QUERY ---------------- */
$sql = "
    SELECT 
        p.product_id,
        p.name,
        p.size,
        COALESCE(SUM(oi.quantity), 0) AS total
    FROM products p
    LEFT JOIN order_items oi ON oi.product_id = p.product_id
    WHERE p.type = 'pizza'
    GROUP BY p.product_id, p.name, p.size
    ORDER BY $sort DESC
";

$result = $conn->query($sql);

if (!$result) {
    die("Database error: " . $conn->error);
}

if ($result->num_rows === 0) {
    echo "<p>No pizza orders found.</p>";
    exit;
}
?>

    <!-- ---------------- SORT LINKS ---------------- -->
    <div style="margin-bottom:15px;">
        <strong>Sort by:</strong>
        <a href="?sort=name">Name</a> |
        <a href="?sort=size">Size</a> |
        <a href="?sort=total">Total Orders</a>
    </div>

    <!-- ---------------- RESULTS ---------------- -->
<?php while ($row = $result->fetch_assoc()): ?>
    <?php
    $id = (int)$row['product_id'];
    $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
    $size = $row['size'] !== null
        ? htmlspecialchars($row['size'], ENT_QUOTES, 'UTF-8')
        : 'unspecified';
    $total = (int)$row['total'];
    ?>
    <div style="margin-bottom:8px;">
        <?php echo "$name ($size) - $total"; ?>
        <a href="?delete=<?php echo $id; ?>&sort=<?php echo urlencode($sort); ?>"
           onclick="return confirm('Are you sure you want to delete this pizza?');"
           style="color:red; margin-left:10px;">Delete</a>
    </div>
<?php endwhile; ?>
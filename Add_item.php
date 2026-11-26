<?php
global $conn;
session_start();
require_once __DIR__ . '/db.config.php';
require_once __DIR__ . '/navigation.php';
render_nav();
?>
<?php
$pizzas = [];
if (!empty($_SESSION['order_id'])) {
    $pizzaStmt = $conn->prepare(
            "SELECT oi.order_item_id, p.product_id
         FROM order_items oi
         JOIN products p ON p.product_id = oi.product_id
         WHERE oi.order_id = ? AND p.type = 'pizza'"
    );

    $pizzaStmt->bind_param("i", $_SESSION['order_id']);
    $pizzaStmt->execute();
    $pizzas = $pizzaStmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<form method="POST">
    Order ID: <label>
        <input type="number" name="order_id" value="<?php echo isset($_SESSION['order_id']) ? (int) $_SESSION['order_id'] : ''; ?>">
    </label><br>
    Product ID: <label>
        <input type="number" name="product_id">
    </label><br>
    Quantity: <label>
        <input type="number" name="qty">
    </label><br>
    Parent Pizza:<label>
    <select name="parent">
        <option value="">-- None (this is a pizza) --</option>
        <?php foreach ($pizzas as $pizza): ?>
            <option value="<?= $pizza['order_item_id'] ?>">
                Pizza Item #<?= $pizza['order_item_id'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>
    </label><br>
    <button type="submit">Add Item</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order_id']) && $_POST['order_id'] !== ''
        ? (int) $_POST['order_id']
        : (isset($_SESSION['order_id']) ? (int) $_SESSION['order_id'] : 0);
    $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $qty = isset($_POST['qty']) ? (int) $_POST['qty'] : 0;
    $parentPizzaItemId = isset($_POST['parent']) && $_POST['parent'] !== '' ? (int) $_POST['parent'] : null;

    if ($orderId <= 0 || $productId <= 0 || $qty <= 0) {
        echo "Please provide valid order, product, and quantity values.";
        exit;
    }

    $productStmt = $conn->prepare(
        /** @lang MySQL */
        "SELECT price, type
         FROM products
         WHERE product_id = ?"
    );
    if (! $productStmt) {
        die("Database error: " . $conn->error);
    }

    $productStmt->bind_param("i", $productId);
    $productStmt->execute();
    $product = $productStmt->get_result()->fetch_assoc();

    if (! $product) {
        echo "Product not found.";
        exit;
    }

    $orderStmt = $conn->prepare(
        /** @lang MySQL */
        "SELECT order_id
         FROM orders
         WHERE order_id = ?"
    );
    if (! $orderStmt) {
        die("Database error: " . $conn->error);
    }

    $orderStmt->bind_param("i", $orderId);
    $orderStmt->execute();

    if (! $orderStmt->get_result()->fetch_assoc()) {
        echo "Order not found.";
        exit;
    }

    if ($parentPizzaItemId !== null) {
        if ($product['type'] !== 'topping') {
            echo "Only toppings can reference a parent pizza item.";
            exit;
        }

        $parentStmt = $conn->prepare(
            /** @lang MySQL */
            "SELECT oi.order_item_id
             FROM order_items oi
             JOIN products p ON p.product_id = oi.product_id
             WHERE oi.order_item_id = ? AND oi.order_id = ? AND p.type = 'pizza'"
        );

        if (! $parentStmt) {
            die("Database error: " . $conn->error);
        }

        $parentStmt->bind_param("ii", $parentPizzaItemId, $orderId);
        $parentStmt->execute();

        if (! $parentStmt->get_result()->fetch_assoc()) {
            echo "Parent pizza item not found for this order.";
            exit;
        }
    } elseif ($product['type'] === 'topping') {
        echo "Toppings must reference a pizza item.";
        exit;
    }

    $itemTotal = (float) $product['price'] * $qty;

    if ($parentPizzaItemId === null) {
        $stmt = $conn->prepare(
            /** @lang MySQL */
            "INSERT INTO order_items (order_id, product_id, quantity, item_total, topping_for_pizza)
             VALUES (?, ?, ?, ?, NULL)"
        );
    } else {
        $stmt = $conn->prepare(
            /** @lang MySQL */
            "INSERT INTO order_items (order_id, product_id, quantity, item_total, topping_for_pizza)
             VALUES (?, ?, ?, ?, ?)"
        );
    }

    if (! $stmt) {
        die("Database error: " . $conn->error);
    }

    if ($parentPizzaItemId === null) {
        $stmt->bind_param("iiid", $orderId, $productId, $qty, $itemTotal);
    } else {
        $stmt->bind_param("iiidi", $orderId, $productId, $qty, $itemTotal, $parentPizzaItemId);
    }

    if (! $stmt->execute()) {
        echo "Error: " . $stmt->error;
        exit;
    }

    $updateTotalStmt = $conn->prepare(
        /** @lang MySQL */
        "UPDATE orders
         SET total = (
             SELECT COALESCE(SUM(item_total), 0)
             FROM order_items
             WHERE order_id = ?
         )
         WHERE order_id = ?"
    );

    if (! $updateTotalStmt) {
        die("Database error: " . $conn->error);
    }

    $updateTotalStmt->bind_param("ii", $orderId, $orderId);
    $updateTotalStmt->execute();
    $_SESSION['order_id'] = $orderId;

    echo "Item added!";
}
?>

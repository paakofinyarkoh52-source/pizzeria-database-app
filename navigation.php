<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('render_nav')) {
    function render_nav(): void
    {
        $loggedIn = !empty($_SESSION['user_id']);
        $hasOrder = !empty($_SESSION['order_id']);
        $currentPage = basename($_SERVER['PHP_SELF']);

        function nav_active(string $page, string $currentPage): string
        {
            return $page === $currentPage ? 'class="active"' : '';
        }
        ?>

        <style>
            nav {
                background: #222;
                padding: 12px;
                margin-bottom: 20px;
                font-family: Arial, sans-serif;
            }

            nav a {
                color: white;
                text-decoration: none;
                margin-right: 15px;
                padding: 8px 12px;
                border-radius: 4px;
            }

            nav a:hover {
                background: #444;
            }

            nav a.active {
                background: #e67e22;
            }

            nav span {
                color: #ccc;
                margin-right: 15px;
            }
        </style>

        <nav>
            <?php if (!$loggedIn): ?>
                <a href="register.php" <?= nav_active('register.php', $currentPage); ?>>Register</a>
                <a href="LogIn.php" <?= nav_active('LogIn.php', $currentPage); ?>>Login</a>
            <?php else: ?>
                <span>
                    Logged in as:
                    <?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user'] ?? 'User'); ?>
                </span>

                <a href="Create_order.php" <?= nav_active('Create_order.php', $currentPage); ?>>
                    Create Order
                </a>

                <?php if ($hasOrder): ?>
                    <a href="Add_item.php" <?= nav_active('Add_item.php', $currentPage); ?>>
                        Add Item
                    </a>
                <?php endif; ?>

                <a href="top_pizzas.php" <?= nav_active('top_pizzas.php', $currentPage); ?>>
                    Top Pizzas
                </a>

                <a href="logout.php">
                    Logout
                </a>
            <?php endif; ?>
        </nav>

        <?php
    }
}

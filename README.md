# Pizzeria Ordering App

A small web application for taking pizza orders at a restaurant, built with PHP and MySQL and run locally on XAMPP. It started as a university assignment; I taught myself SQL while building it.

## Features

- Register and log in (passwords are stored hashed with `password_hash`)
- Create an order for a table
- Add pizzas to an order, and add toppings linked to a specific pizza item
- "Top pizzas" report with sorting and deletion

## Tech stack

- PHP 8.2 (tested on 8.2.12)
- MySQL / MariaDB (tested on MariaDB 10.4.32)
- Apache via XAMPP
- ER diagram made in draw.io: [`pizzeria_er_diagram.drawio.pdf`](pizzeria_er_diagram.drawio.pdf)

## Database

Four tables, defined in [`database.sql`](database.sql):

| Table | Purpose |
| --- | --- |
| `users` | Accounts: email (unique), password hash, name, last login |
| `products` | Menu items; `type` is `pizza` or `topping` |
| `orders` | One row per order: date, status (`ready`, `at_table`, `paid`), total, table number, user |
| `order_items` | Items in an order with quantity and total; `topping_for_pizza` points to the pizza item a topping belongs to |

`database.sql` contains the structure and the menu. It contains no user or order data.

## Setup (Windows, XAMPP)

1. **Copy the project** into `C:\xampp\htdocs\pizzeria`.
2. **Start Apache and MySQL** in the XAMPP Control Panel.
3. **Import the database:** open `http://localhost/phpmyadmin`, go to the **Import** tab, choose `database.sql` and run it. This creates the `pizzeria` database.
4. **Set the database credentials as environment variables.** `db.config.php` reads them instead of storing a password in the code. In the XAMPP Control Panel, click **Config** next to Apache, open `httpd.conf`, and add these lines at the bottom:

   ```
   SetEnv PIZZERIA_DB_USER root
   SetEnv PIZZERIA_DB_PASSWORD ""
   ```

   Use your own MySQL user and password if you changed the XAMPP defaults. Optionally set `PIZZERIA_DB_HOST` (default `localhost`) and `PIZZERIA_DB_NAME` (default `pizzeria`).
5. **Restart Apache** (stop, then start). A browser refresh is not enough.
6. **Open the app:** `http://localhost/pizzeria/`, and register the first account.

If you see "Database configuration is missing", Apache did not pick up the environment variables. Check the lines in `httpd.conf` and restart Apache.

## Project structure

| File | Role |
| --- | --- |
| `index.php` | Home page |
| `register.php`, `LogIn.php`, `logout.php` | Account registration, login and logout |
| `Create_order.php` | Create an order for a table |
| `Add_item.php` | Add pizzas and toppings to an order |
| `top_pizzas.php` | Top pizzas report |
| `navigation.php` | Shared navigation |
| `db.config.php` | Database connection (reads environment variables) |
| `database.sql` | Database schema and menu |

## Notes

- No credentials are stored in the repository.
- This is a learning project meant for local use, not a production system.

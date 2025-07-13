<?php
session_start();
include 'includes/db_connect.php';

$action = $_POST['action'] ?? '';

if ($action === 'add_to_cart') {
    $product_id = (int)$_POST['product_id'];
    $selected_sizes = $_POST['sizes'] ?? [];
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = [];
    }
    
    foreach ($selected_sizes as $size) {
        $quantity = (int)$_POST["quantity_$size"];
        if ($quantity > 0) {
            if (isset($_SESSION['cart'][$product_id][$size])) {
                $_SESSION['cart'][$product_id][$size] += $quantity;
            } else {
                $_SESSION['cart'][$product_id][$size] = $quantity;
            }
        }
    }
    
    $_SESSION['cart'][$product_id] = array_filter($_SESSION['cart'][$product_id], function($quantity) {
        return $quantity > 0;
    });
    
    if (empty($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    
    header('Location: cart.php?success=item_added');
    exit;
}

if ($action === 'remove_from_cart') {
    $product_id = (int)$_POST['product_id'];
    $size = $_POST['size'] ?? null;
    
    if (isset($_SESSION['cart'][$product_id])) {
        if ($size) {
            unset($_SESSION['cart'][$product_id][$size]);
            if (empty($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
            }
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header('Location: cart.php?success=item_removed');
    exit;
}

if ($action === 'checkout') {
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $customer_address = $_POST['customer_address'];
    $payment_method = $_POST['payment_method'] ?? null;  
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

    if (empty($cart)) {
        header('Location: cart.php?error=empty_cart');
        exit;
    }

    $total = 0;
    foreach ($cart as $product_id => $sizes) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            foreach ($sizes as $size => $quantity) {
                $total += $product['price'] * $quantity;
            }
        }
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, total, payment_method) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$customer_name, $customer_phone, $customer_address, $total, $payment_method]);
        $order_id = $pdo->lastInsertId();

        foreach ($cart as $product_id => $sizes) {
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                foreach ($sizes as $size => $quantity) {
                    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, size, quantity, price) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$order_id, $product_id, $size, $quantity, $product['price']]);
                    $stmt = $pdo->prepare("UPDATE product_sizes SET stock = stock - ? WHERE product_id = ? AND size = ?");
                    $stmt->execute([$quantity, $product_id, $size]);
                }
            }
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        header('Location: index.php?success=order_placed');
    } catch (Exception $e) {
        $pdo->rollBack();
        header('Location: checkout.php?error=order_failed');
    }
    exit;
}

if ($action === 'contact') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: contact.php?error=invalid_input');
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO contact_inquiries (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        header('Location: contact.php?success=message_sent');
    } catch (Exception $e) {
        header('Location: contact.php?error=database_error');
    }
    exit;
}

if ($action === 'admin_login') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        header('Location: admin/dashboard.php?success=login_success');
    } else {
        header('Location: admin/index.php?error=invalid_credentials');
    }
    exit;
}

if ($action === 'add_product' || $action === 'update_product') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $image = $_FILES['image']['name'];

    if ($image) {
        move_uploaded_file($_FILES['image']['tmp_name'], 'assets/images/' . $image);
    } else if ($action === 'update_product') {
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $image = $stmt->fetchColumn();
    }

    $pdo->beginTransaction();
    try {
        if ($action === 'add_product') {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $category_id, $image]);
            $product_id = $pdo->lastInsertId();
            $redirect_success = 'product_added';
        } else {
            $product_id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $category_id, $image, $product_id]);

            $stmt = $pdo->prepare("DELETE FROM product_sizes WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $redirect_success = 'product_updated';
        }

        $selected_sizes = $_POST['sizes'] ?? [];
        foreach ($selected_sizes as $size) {
            $stock = (int)$_POST["stock_$size"];
            if ($stock >= 0) {
                $stmt = $pdo->prepare("INSERT INTO product_sizes (product_id, size, stock) VALUES (?, ?, ?)");
                $stmt->execute([$product_id, $size, $stock]);
            }
        }

        $pdo->commit();
        header("Location: admin/products.php?success=$redirect_success");
    } catch (Exception $e) {
        $pdo->rollBack();
        header('Location: admin/products.php?error=database_error');
    }
    exit;
}

if ($action === 'delete_product') {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([(int)$_POST['id']]);
    header('Location: admin/products.php?success=product_deleted');
    exit;
}

if ($action === 'update_order_status') {
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], (int)$_POST['order_id']]);
        header('Location: admin/orders.php?success=order_status_updated');
    } catch (Exception $e) {
        header('Location: admin/orders.php?error=database_error');
    }
    exit;
}

if ($action === 'add_category' || $action === 'update_category') {
    $name = $_POST['name'];
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    if ($action === 'add_category') {
        $stmt = $pdo->prepare("INSERT INTO categories (name, parent_id) VALUES (?, ?)");
        $stmt->execute([$name, $parent_id]);
        $redirect_success = 'category_added';
    } else {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, parent_id = ? WHERE id = ?");
        $stmt->execute([$name, $parent_id, (int)$_POST['id']]);
        $redirect_success = 'category_updated';
    }
    header("Location: admin/categories.php?success=$redirect_success");
    exit;
}

if ($action === 'delete_category') {
    $id = (int)$_POST['id'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        header('Location: admin/categories.php?error=has_products');
        exit;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE parent_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        header('Location: admin/categories.php?error=has_subcategories');
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: admin/categories.php?success=category_deleted');
    exit;
}

if ($action === 'subscribe') {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?)");
    $stmt->execute([$email]);
    header('Location: index.php?success=subscribed');
    exit;
}
?>
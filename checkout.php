<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = 'checkout.php';
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลสินค้าจากตะกร้า
$stmt = $conn->prepare("
    SELECT cart_items.product_id, products.name, products.price, cart_items.quantity 
    FROM cart_items cart_items
    JOIN products products ON cart_items.product_id = products.product_id
    WHERE cart_items.user_id = ?
");
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
}
$result = $stmt->get_result();

$cart_items = [];
$total_amount = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[$row['product_id']] = $row;
    $total_amount += $row['price'] * $row['quantity'];
}
$stmt->close();

if (!empty($cart_items)) {
    $conn->begin_transaction();

    try {
        // สร้างคำสั่งซื้อ
        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_status) VALUES (?, ?, 'pending')");
        if (!$stmt_order) {
            throw new Exception("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt_order->bind_param("id", $user_id, $total_amount);
        if (!$stmt_order->execute()) {
            throw new Exception("Execute failed: (" . $stmt_order->errno . ") " . $stmt_order->error);
        }
        $order_id = $stmt_order->insert_id;
        $stmt_order->close();

        // เพิ่มรายการสินค้าของคำสั่งซื้อ
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        if (!$stmt_item) {
            throw new Exception("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        
        foreach ($cart_items as $product_id => $product) {
            $quantity = $product['quantity'];
            $price = $product['price'];
            $stmt_item->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            if (!$stmt_item->execute()) {
                throw new Exception("Execute failed: (" . $stmt_item->errno . ") " . $stmt_item->error);
            }

            // อัปเดตสต็อกในตาราง products
            $update_stock_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
            $update_stock_stmt->bind_param("ii", $quantity, $product_id);
            if (!$update_stock_stmt->execute()) {
                throw new Exception("Failed to update stock: (" . $update_stock_stmt->errno . ") " . $update_stock_stmt->error);
            }
            $update_stock_stmt->close();
        }
        $stmt_item->close();

        // ลบสินค้าจากตะกร้า
        $stmt_clear_cart = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt_clear_cart->bind_param("i", $user_id);
        $stmt_clear_cart->execute();
        $stmt_clear_cart->close();

        $conn->commit();

        // ลบข้อมูลตะกร้าจาก session
        unset($_SESSION['cart']);

        // เปลี่ยนเส้นทางไปที่หน้า order_confirmation.php
        header('Location: order_confirmation.php?order_id=' . $order_id);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "เกิดข้อผิดพลาดในการสั่งซื้อ: " . $e->getMessage();
    }
} else {
    echo "ตะกร้าสินค้าของคุณว่างเปล่า";
}

$conn->close();
?>

<?php
session_start();
include 'config.php';

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบแล้ว
if (!isset($_SESSION['user_id'])) {
    // หากยังไม่ได้เข้าสู่ระบบ ให้เปลี่ยนเส้นทางไปยังหน้าเข้าสู่ระบบ
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $quantity = max($quantity, 1);
    $user_id = $_SESSION['user_id'];

    // ดึงข้อมูลสินค้าจากฐานข้อมูล
    $stmt = $conn->prepare("SELECT name, price FROM products WHERE product_id = ?");
    if ($stmt === false) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("i", $product_id);
    if (!$stmt->execute()) {
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }
    $result = $stmt->get_result();

    if ($product = $result->fetch_assoc()) {
        // ตรวจสอบว่ามีสินค้านี้อยู่ในตะกร้าของผู้ใช้แล้วหรือไม่
        $stmt_check = $conn->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
        if ($stmt_check === false) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt_check->bind_param("ii", $user_id, $product_id);
        if (!$stmt_check->execute()) {
            die("Execute failed: (" . $stmt_check->errno . ") " . $stmt_check->error);
        }
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // หากมีอยู่แล้ว ให้เพิ่มจำนวนสินค้า
            $stmt_check->bind_result($cart_item_id, $current_quantity);
            $stmt_check->fetch();
            $new_quantity = $current_quantity + 1;

            $stmt_update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
            if ($stmt_update === false) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            $stmt_update->bind_param("ii", $new_quantity, $cart_item_id);
            if (!$stmt_update->execute()) {
                die("Execute failed: (" . $stmt_update->errno . ") " . $stmt_update->error);
            }
            $stmt_update->close();
        } else {
            // หากไม่มี ให้เพิ่มรายการใหม่ลงในตะกร้า
            $stmt_insert = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
            if ($stmt_insert === false) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            $quantity = 1; // หรือรับค่าจากฟอร์มถ้ามี
            $stmt_insert->bind_param("iii", $user_id, $product_id, $quantity);
            if (!$stmt_insert->execute()) {
                die("Execute failed: (" . $stmt_insert->errno . ") " . $stmt_insert->error);
            }
            $stmt_insert->close();
        }

        $stmt_check->close();

        $redirect_url = 'product.php';
        if (isset($_GET['category_id']) && intval($_GET['category_id']) > 0) {
            $redirect_url .= '?category_id=' . intval($_GET['category_id']);
        }

        // เปลี่ยนเส้นทางกลับไปยังหน้า cart.php
        header('Location: ' . $redirect_url);
        exit();
    } else {
        echo "ไม่พบสินค้า";
    }

    $stmt->close();
} else {
    echo "ไม่มีข้อมูลสินค้า";
}

$conn->close();
?>

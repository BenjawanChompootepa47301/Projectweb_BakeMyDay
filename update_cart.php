<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['product_id'], $_POST['action'])) {
    $product_id = intval($_POST['product_id']);
    $action = $_POST['action'];

    // ดึงข้อมูลสินค้าจากตะกร้าในฐานข้อมูล
    $stmt = $conn->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->bind_result($current_quantity);
    $stmt->fetch();
    $stmt->close();

    if ($action == 'increase') {
        // เพิ่มจำนวนสินค้า
        $new_quantity = $current_quantity + 1;
        $stmt_update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt_update->bind_param("iii", $new_quantity, $user_id, $product_id);
        $stmt_update->execute();
        $stmt_update->close();
    } elseif ($action == 'decrease') {
        // ลดจำนวนสินค้า
        $new_quantity = $current_quantity - 1;
        if ($new_quantity > 0) {
            $stmt_update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt_update->bind_param("iii", $new_quantity, $user_id, $product_id);
            $stmt_update->execute();
            $stmt_update->close();
        } else {
            // ถ้าจำนวนเท่ากับหรือต่ำกว่า 0 ลบออกจากตะกร้า
            $stmt_delete = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
            $stmt_delete->bind_param("ii", $user_id, $product_id);
            $stmt_delete->execute();
            $stmt_delete->close();
        }
    } elseif ($action == 'remove') {
        // ลบสินค้าออกจากตะกร้า
        $stmt_delete = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt_delete->bind_param("ii", $user_id, $product_id);
        $stmt_delete->execute();
        $stmt_delete->close();
    }
}

header('Location: cart.php');
exit();
?>

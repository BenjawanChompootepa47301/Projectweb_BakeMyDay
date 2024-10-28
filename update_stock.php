<?php
include 'config.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

$product_id = $_POST['product_id'];
$action = $_POST['action'];

// ดึงข้อมูลสินค้า
$query = "SELECT quantity FROM stock WHERE id = $product_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row) {
    $quantity = $row['quantity'];

    // ทำการเพิ่ม ลด หรือลบ สินค้า
    if ($action == 'increase') {
        $quantity++;
    } elseif ($action == 'decrease' && $quantity > 0) {
        $quantity--;
    } elseif ($action == 'delete') {
        $quantity = 0; // หรือลบ record ออกจากตาราง
        mysqli_query($conn, "DELETE FROM stock WHERE id = $product_id");
        echo 0;
        exit;
    }

    // อัปเดตจำนวนสินค้าในฐานข้อมูล
    $update_query = "UPDATE stock SET quantity = $quantity WHERE id = $product_id";
    mysqli_query($conn, $update_query);

    // ส่งค่าจำนวนสินค้าใหม่กลับไปที่หน้าเว็บ
    echo $quantity;
} else {
    echo "Error: Product not found.";
}
?>

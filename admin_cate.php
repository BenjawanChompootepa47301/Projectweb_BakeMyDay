<?php
include 'config.php'; // ไฟล์สำหรับเชื่อมต่อฐานข้อมูล

// ฟังก์ชันสำหรับดึงสินค้าตามหมวดหมู่
function getProductsByCategory($category_id) {
    global $conn; // ใช้การเชื่อมต่อฐานข้อมูลที่กำหนดไว้ใน config.php
    $stmt = $conn->prepare("SELECT product_id, name, description, price, image FROM products WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    return $stmt->get_result(); // คืนค่าผลลัพธ์
}

// ฟังก์ชันสำหรับดึงชื่อหมวดหมู่
function getCategoryName($category_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT category_name FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['category_name'] ?? null; // คืนค่าชื่อหมวดหมู่
}
?>

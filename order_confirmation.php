<?php
session_start();
include 'config.php';

if (isset($_GET['order_id']) && intval($_GET['order_id']) > 0) {
    $order_id = intval($_GET['order_id']);
} else {
    echo "ไม่พบหมายเลขคำสั่งซื้อ";
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt_order = $conn->prepare("SELECT order_id, total_amount, order_status, created_at FROM orders WHERE order_id = ? AND user_id = ?");
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

if ($result_order->num_rows == 0) {
    echo "ไม่พบคำสั่งซื้อหรือคุณไม่มีสิทธิ์เข้าถึงคำสั่งซื้อนี้";
    exit();
}

$order = $result_order->fetch_assoc();
$stmt_order->close();

$stmt_items = $conn->prepare("SELECT order_item_id.product_id, order_item_id.quantity, order_item_id.price, products.name, products.image FROM order_items order_item_id JOIN products products ON order_item_id.product_id = products.product_id WHERE order_item_id.order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$order_items = array();
while ($item = $result_items->fetch_assoc()) {
    $order_items[] = $item;
}

$stmt_items->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ยืนยันคำสั่งซื้อ</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <h1 class="text-center">ยืนยันคำสั่งซื้อ</h1>
        <div class="order-details">
            <h2>หมายเลขคำสั่งซื้อ: #<?php echo $order['order_id']; ?></h2>
            <p>วันที่สั่งซื้อ: <?php echo $order['created_at']; ?></p>
            <p>สถานะคำสั่งซื้อ: <?php echo htmlspecialchars($order['order_status']); ?></p>
            <h3>ยอดรวม: $<?php echo number_format($order['total_amount'], 2); ?></h3>
        </div>
        <hr>
        <h3>รายการสินค้าที่สั่งซื้อ</h3>
        <div class="order-items">
            <?php foreach ($order_items as $item): ?>
                <div class="order-item d-flex flex-row justify-content-between align-items-center p-2 bg-white mt-4 px-3 rounded">
                    <div class="mr-1">
                        <img class="rounded" src="uploaded_img/<?php echo htmlspecialchars($item['image']); ?>" width="200" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    </div>
                    <div class="d-flex flex-column align-items-center product-details">
                        <span class="font-weight-bold"><?php echo htmlspecialchars($item['name']); ?></span>
                    </div>
                    <div class="d-flex flex-row align-items-center qty">
                        <span class="mx-2">จำนวน: <?php echo $item['quantity']; ?></span>
                    </div>
                    <div>
                        <h5 class="text-grey price">ราคา: $<?php echo number_format($item['price'], 2); ?></h5>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="product.php" class="btn btn-primary">กลับไปยังหน้าสินค้า</a>
        </div>
    </div>

    

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลสินค้าจากตะกร้า
$stmt = $conn->prepare("
    SELECT cart_items.cart_item_id, products.product_id, products.name, products.price, products.image, cart_items.quantity
    FROM cart_items cart_items
    JOIN products products ON cart_items.product_id = products.product_id
    WHERE cart_items.user_id = ?
");
if ($stmt === false) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
}
$result = $stmt->get_result();

$cart_items = [];
$total = 0; // กำหนดค่าเริ่มต้นของตัวแปร $total
$total_quantity = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
    $total_quantity += $row['quantity']; // บวกจำนวนสินค้าทั้งหมด
}

session_start();
$cart_quantity = isset($_SESSION['cart_quantity']) ? $_SESSION['cart_quantity'] : 0;

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eCommerce Website</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background: #eee;
        }
        
        /* Additional custom styles */
        .product-details {
            margin-right: 70px;
            
        }
        
        .text-grey {
            color: #a39f9f;
        }

        .qty i {
            font-size: 11px;
            cursor: pointer; /* Change cursor for better UX */
        }
    </style>
</head>
<body>
<div class="container mt-3">
    <div class="d-flex justify-content-end">
        <a href="cart.php" class="btn btn-primary">
            <i class="fa fa-shopping-cart"></i> ตะกร้าสินค้า 
            <span class="badge badge-light"><?php echo $total_quantity; ?></span>
        </a>
    </div>
</div>

<div class="container mt-5 mb-5">
    <h1 class="text-center">ตะกร้าสินค้า</h1>
    <?php if (!empty($cart_items)): ?>
        <div class="d-flex justify-content-center row">
            <div class="col-md-8">
                <?php foreach ($cart_items as $product): ?>
                    <div class="d-flex flex-row justify-content-between align-items-center p-2 bg-white mt-4 px-3 rounded">
                        <div class="mr-1">
                            <!-- ดึงรูปภาพสินค้า -->
                            <img class="rounded" src="uploaded_img/<?php echo htmlspecialchars($product['image']); ?>" width="70" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="d-flex flex-column align-items-center product-details">
                            <span class="font-weight-bold"><?php echo htmlspecialchars($product['name']); ?></span>
                        </div>
                        <div class="d-flex flex-row align-items-center qty">
                            <form method="post" action="update_cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                <button type="submit" name="action" value="decrease" class="btn btn-sm btn-light">-</button>
                                <span class="mx-2"><?php echo $product['quantity']; ?></span>
                                <button type="submit" name="action" value="increase" class="btn btn-sm btn-light">+</button>
                            </form>
                        </div>
                        <div>
                            <h5 class="text-grey price">$<?php echo number_format($product['price'] * $product['quantity'], 2); ?></h5>
                        </div>
                        <div class="d-flex align-items-center">
                            <form method="post" action="update_cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                <button type="submit" name="action" value="remove" class="btn btn-sm btn-danger">ลบ</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="d-flex flex-row align-items-center mt-3 p-2 bg-white rounded">
                    <h4 class="mr-auto">ยอดรวม: $<?php echo number_format($total, 2); ?></h4>
                    <form action="checkout.php" method="post">
                        <button class="btn btn-success btn-lg" type="submit">ชำระเงิน</button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center">ตะกร้าสินค้าของคุณว่างเปล่า</p>
    <?php endif; ?>
</div>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Bootstrap Bundle (includes Popper.js) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>

</body>
</html>

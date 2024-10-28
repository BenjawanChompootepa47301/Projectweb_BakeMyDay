<?php
session_start();
include 'config.php'; // ไฟล์สำหรับเชื่อมต่อฐานข้อมูล

// รับค่า category_id และ search จาก URL
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// ถ้าหากมีการค้นหา
if (!empty($search_query)) {
    // ดึงสินค้าตามคำค้นหา
    $stmt = $conn->prepare("SELECT product_id, name, description, price, image FROM products WHERE name LIKE ?");
    $search_param = "%{$search_query}%";
    $stmt->bind_param("s", $search_param);
} elseif ($category_id > 0) {
    // ดึงสินค้าตามหมวดหมู่
    $stmt = $conn->prepare("SELECT product_id, name, description, price, image FROM products WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
} else {
    // ดึงสินค้าทั้งหมด
    $stmt = $conn->prepare("SELECT product_id, name, description, price, image FROM products");
}

$stmt->execute();
$result = $stmt->get_result();

// ดึงชื่อหมวดหมู่ (ถ้ามี)
$category_name = '';
if ($category_id > 0) {
    $stmt_cat = $conn->prepare("SELECT category_name FROM categories WHERE category_id = ?");
    $stmt_cat->bind_param("i", $category_id);
    $stmt_cat->execute();
    $result_cat = $stmt_cat->get_result();
    if ($row_cat = $result_cat->fetch_assoc()) {
        $category_name = $row_cat['category_name'];
    }
    $stmt_cat->close();
}
$image_path = 'uploaded_img/' . $product['image'];
if (file_exists($image_path)) {
    echo '<img class="img-fluid" src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($product['name']) . '">';
} else {
    echo '<p>รูปภาพไม่พร้อมใช้งาน</p>';
}
// Fetch products from the database
$category_id = isset($_GET["category_id"]) ? $_GET["category_id"] : ''; // Assign $category_id from $_GET, default to empty string

// Define SQL query based on $category_id
if ($category_id == "") {
    $sql = "SELECT product_id, name, description, price, image FROM products";
} elseif ($category_id == 1) {
    // Query for Brownie products
    $sql = "SELECT product_id, name, description, price, image FROM products WHERE category_id = 1"; // ปรับ categoryID ตามจริง
} elseif ($category_id == 2) {
    // Query for Cake products
    $sql = "SELECT product_id, name, description, price, image FROM products WHERE category_id = 2"; // ปรับ categoryID ตามจริง
} elseif ($category_id == 3) {
    // Query for Cookie products
    $sql = "SELECT product_id, name, description, price, image FROM products WHERE category_id = 3"; // ปรับ categoryID ตามจริง
} elseif ($category_id == 4) {
    // Query for Macaron products
    $sql = "SELECT product_id, name, description, price, image FROM products WHERE category_id = 4"; // ปรับ categoryID ตามจริง
} 

// ตรวจสอบว่ามีการส่งคำค้นมาหรือไม่
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// เขียน SQL Query ตามคำค้นหา
if (!empty($searchQuery)) {
    // ใช้คำค้นหาในการค้นหาข้อมูลที่ตรงกับชื่อสินค้าหรือคำอธิบาย
    $sql = "SELECT product_id, name, price, image FROM products WHERE name LIKE '%$searchQuery%'";
}

$result = $conn->query($sql);

// ปิด statement
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Bake My Day</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 


    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->


    
    <!-- Navbar Start -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <a href="index.html" class="navbar-brand ms-4 ms-lg-0">
            <h1 class="text-primary m-0">Bake My Day</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mx-auto p-4 p-lg-0">
                <li class="nav-item">
                    <a href="product.php" class="nav-link <?php if (!$category_id) echo 'active'; ?>">Products</a>
                </li>
                <li class="nav-item">
                    <a href="product.php?category_id=1" class="nav-link <?php if ($category_id == 1) echo 'active'; ?>">Cookie</a>
                </li>
                <li class="nav-item">
                    <a href="product.php?category_id=2" class="nav-link <?php if ($category_id == 2) echo 'active'; ?>">Cake</a>
                </li>
                <li class="nav-item">
                    <a href="product.php?category_id=3" class="nav-link <?php if ($category_id == 3) echo 'active'; ?>">Brownie</a>
                </li>
                <li class="nav-item">
                    <a href="product.php?category_id=4" class="nav-link <?php if ($category_id == 4) echo 'active'; ?>">Macaron</a>
                </li>

                

                <li class="nav-item d-flex align-items">
                <!-- Sub Navigation Bar Start -->
                    <div class="search-bar-container">
                        <form class="d-flex" method="GET" action="product.php">
                            <input class="form-control me-2" type="search" name="search" placeholder="Product Search" aria-label="Search">
                            <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search text-primary"></i></button>
                        </form>
                    </div>
               
                <!-- Sub Navigation Bar End -->
                </li>
                <li class="nav-item d-flex align-items">
                    <a href="cart.php" class="position-relative me-4 my-auto">
                        <i class="fa fa-shopping-bag fa-2x"></i>
                        <?php
                        $cart_count = 0;
                        if (isset($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                $cart_count += $item['quantity'];
                            }
                        }
                        if ($cart_count > 0) {
                            echo '<span class="badge bg-primary">'.$cart_count.'</span>';
                        }
                        ?>
                    </a>
                </li>

            </ul>


            <ul class="navbar-nav ms-auto p-4 p-lg-0">
            <li class="nav-item">
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="text-white me-3">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="btn btn-sm btn-outline-light">Log out</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-outline-light me-2">
                        <i class="fas fa-sign-in-alt"></i> Log in
                    </a>
                    <a href="register.php" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-user-plus"></i> Create Account
                    </a>
                <?php endif; ?>
            </div>
            </li>
                
            </ul>
        </div>
    </div>
</nav>
<!-- Navbar End -->





    <!-- Product Start -->
    <div class="container-xxl bg-light my-6 py-6 pt-0">
        <div class="container">
            <!-- ส่วนหัวของหน้าเพจ -->
            <div class="text-center mx-auto mb-5">
                <p class="text-primary text-uppercase mb-2"></p>
            </div>
            
              <!-- แสดงสินค้าตามหมวดหมู่หรือทั้งหมด -->
            <div class="row g-4">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($product = $result->fetch_assoc()): ?>
                        
                        <div class="col-lg-4 col-md-6">
                            <div class="product-item d-flex flex-column bg-white rounded overflow-hidden h-100">
                                <div class="text-center p-4">
                                    <div class="d-inline-block border border-primary rounded-pill px-3 mb-3">
                                        $<?php echo number_format($product['price'], 2); ?>
                                    </div>
                                    <h3 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <span><?php echo htmlspecialchars($product['description']); ?></span>
                                </div>
                                <div class="position-relative mt-auto">
                                    <img class="img-fluid" src="uploaded_img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="product-overlay">
                                        <form method="post" action="add_to_cart.php">
                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                            <button type="submit" class="btn btn-lg-square btn-outline-light rounded-circle">
                                                <i class="fas fa-plus text-primary"></i>
                                            </button>
                                        </form>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?> 
                    <p>ไม่พบสินค้าในหมวดหมู่นี้</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Product End -->
    <?php
    $stmt->close();
    $conn->close();
    ?>
     



    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    
    <script>
    // ฟังเหตุการณ์การเลื่อนของหน้า
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) { // เลื่อนลงเกิน 50px
            navbar.classList.add('scrolled'); // เพิ่มคลาสเมื่อเลื่อนลง
        } else {
            navbar.classList.remove('scrolled'); // ลบคลาสเมื่อกลับไปด้านบน
        }
    });
    </script>

    <script>
        let basket = [];
        let totalPrice = 0;
        
        function addToBasket(productName, productPrice) {
            basket.push({ name: productName, price: productPrice });
            updateBasket();
        }
        
        function updateBasket() {
            const basketItems = document.getElementById('basket-items');
            basketItems.innerHTML = ''; // Clear current items
            totalPrice = 0; // Reset total price
        
            basket.forEach((item, index) => {
                totalPrice += item.price;
                basketItems.innerHTML += `<div class="basket-item">
                    <span>${item.name}</span>
                    <span>$${item.price.toFixed(2)}</span>
                    <button onclick="removeFromBasket(${index})">Remove</button>
                </div>`;
            });
        
            document.getElementById('total-price').textContent = totalPrice.toFixed(2);
        }
        
        function removeFromBasket(index) {
            basket.splice(index, 1); // Remove item from basket
            updateBasket(); // Update the basket display
        }
        </script>
        
</body>

</html>
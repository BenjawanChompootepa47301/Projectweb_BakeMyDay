<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
@include 'config.php';

if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_home.php');
    exit();
}

// การเพิ่มสินค้าใหม่
if (isset($_POST['add_product'])) {
    
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_description = $_POST['product_description'];
    $product_stock = $_POST['product_stock'];
    $product_category_id = $_POST['product_category_id'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'uploaded_img/' . $product_image;
    
    if (empty($product_name) || empty($product_price) || empty($product_description) || empty($product_stock) || empty($product_category_id) ||empty($product_image)) {
        $message[] = 'Please fill out all fields';
    } else {
        $insert = "INSERT INTO products(name, description, price, stock, category_id, image) VALUES('$product_name', '$product_description', '$product_price', '$product_stock', '$product_category_id','$product_image')";
        $upload = mysqli_query($conn, $insert);
        if ($upload) {
            $message[] = 'New product added successfully';
        } else {
            $message[] = 'Could not add the product';
        }
    }
}

// การดึงข้อมูลสินค้าปัจจุบันเพื่อแสดงฟอร์มแก้ไข
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_query = "SELECT * FROM products WHERE product_id = $edit_id";
    $edit_result = mysqli_query($conn, $edit_query);
    $edit_row = mysqli_fetch_assoc($edit_result);

    if (mysqli_query($conn, $edit_query)) {
        $message[] = 'Product deleted successfully';
        header('Location: admin_stock.php');
        exit();
    } else {
        $message[] = 'Failed to delete product';
    }
}

// การอัปเดตสินค้า
if (isset($_POST['update_product'])) {
    $update_id = $_POST['update_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_description = $_POST['product_description'];
    $product_stock = $_POST['product_stock'];
    $product_category_id = $_POST['product_category_id'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'uploaded_img/'.$product_image;


   // ตรวจสอบว่าได้อัปโหลดรูปภาพใหม่หรือไม่
   if (!empty($_FILES['product_image']['name'])) {
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'uploaded_img/' . $product_image;
    
    // ลบรูปภาพเดิม
    $select_image = mysqli_query($conn, "SELECT image FROM products WHERE product_id = $update_id");
    if ($image_row = mysqli_fetch_assoc($select_image)) {
        $old_image_path = 'uploaded_img/' . $image_row['image'];
        if (file_exists($old_image_path)) {
            unlink($old_image_path);
        }
    }

    // อัปเดตรูปภาพใหม่
    move_uploaded_file($product_image_tmp_name, $product_image_folder);
    $update_query = "UPDATE products SET name='$product_name', description='$product_description', price='$product_price', stock='$product_stock', category_id='$product_category_id', image='$product_image',  WHERE product_id='$update_id'";
} else {
    // อัปเดตข้อมูลส่วนอื่น ๆ โดยไม่อัปเดตรูปภาพ
    $update_query = "UPDATE products SET name='$product_name', description='$product_description', price='$product_price', stock='$product_stock', category_id='$product_category_id',  WHERE product_id='$update_id'";
}

if (mysqli_query($conn, $update_query)) {
    $message[] = 'Product updated successfully';
    header('Location: admin_stock.php');
    exit();
} else {
    $message[] = 'Failed to update product';
}
}
$select = mysqli_query($conn, "SELECT * FROM products");



// ลบสินค้า
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];


    $delete_query = "DELETE FROM products WHERE product_id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        $message[] = 'Product deleted successfully';
        header('Location: admin_stock.php');
        exit();
    } else {
        $message[] = 'Failed to delete product';
    }
    
}
$select = mysqli_query($conn, "SELECT * FROM products");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css2/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #fef3e8;
        }

        .container {
            background: #fffff;
            padding: 30px;
            width: 100%;
            max-width: 900px;
            border-radius: 12px; /* กรอบมน */
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); /* เงา */
            text-align: center;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        .box {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px; /* กรอบมน */
        }

        .btn {
            width: 100%;
            padding: 10px;
            background-color: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 15px; /* กรอบมน */
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* เงา */
        }

        .btn:hover {
            background-color: #000;
        }

        p {
            margin-top: 10px;
            font-size: 18px;
            color: #000;
        }

        p a {
            color: #e74c3c;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        th {
            margin-top: 10px;
            font-size: 14px;
            color: #000;
        }

        td {
            margin-top: 10px;
            font-size: 12px;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Stock</h2>

        <!-- ฟอร์มแก้ไข -->
        <?php if (isset($edit_row)) { ?>
            <form action="admin_stock.php" method="post">
            <input type="hidden" name="update_id" value="<?php echo $edit_row['product_id']; ?>">
            <input type="text" class="box" name="product_name" value="<?php echo $edit_row['name']; ?>">
            <input type="text" class="box" name="product_price" value="<?php echo $edit_row['price']; ?>">
            <input type="text" class="box" name="product_description" value="<?php echo $edit_row['description']; ?>">
            <input type="text" class="box" name="product_stock" value="<?php echo $edit_row['stock']; ?>">
            <input type="text" class="box" name="product_category_id" value="<?php echo $edit_row['category_id']; ?>">
            <input type="file" class="box" name="product_image" value="<?php echo $edit_row['image'];?>">
            <input type="submit" class="btn" name="update_product" value="Save Changes">
                
               
            </form>
        <?php } ?>

        <!-- ตารางแสดงสินค้า -->
        <table class="product-display-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Category ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($select)) { ?>
                <tr>
                    <td><img src="uploaded_img/<?php echo $row['image']; ?>" width="150" alt=""></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['price']; ?>/-</td>
                    <td><?php echo $row['stock']; ?></td>
                    <td><?php echo $row['category_id']; ?></td>
                    <td>
                        <a href="admin_update.php?edit=<?php echo $row['product_id']; ?>" class="btn"> <i class="fas fa-edit"></i> Edit </a>
                        <a href="admin_stock.php?delete=<?php echo $row['product_id']; ?>" class="btn"> <i class="fas fa-trash"></i> Delete </a>
                        
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <a href="admin_home.php" class="btn" style="max-width: 100px;">go back</a>
    </div>
</body>
</html>

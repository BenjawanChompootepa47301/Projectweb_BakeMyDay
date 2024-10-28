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

    if (empty($product_name) || empty($product_price) || empty($product_image) || empty($product_description) || empty($product_stock) || empty($product_category_id) || empty($product_image)) {
        $message[] = 'Please fill out all fields';
    } else {
        $insert = "INSERT INTO products(name, description, price, stock, image, category_id) VALUES('$product_name', '$product_description', '$product_price', '$product_stock', '$product_image', '$product_category_id')";
        $upload = mysqli_query($conn, $insert);
        if ($upload) {
            move_uploaded_file($product_image_tmp_name, $product_image_folder);
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
}




// การอัปเดตสินค้า
if (isset($_POST['update_product'])) {
    $update_id = $_POST['update_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_description = $_POST['product_description'];
    $product_stock = $_POST['product_stock'];
    $product_category_id = $_POST['product_category_id'];


    // อัปเดตรูปภาพถ้ามีการอัปโหลดใหม่
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

      // ย้ายรูปภาพใหม่และอัปเดตในฐานข้อมูล
      move_uploaded_file($product_image_tmp_name, $product_image_folder);
      $update_query = "UPDATE products SET name='$product_name', description='$product_description', price='$product_price', stock='$product_stock', category_id='$product_category_id', image='$product_image' WHERE product_id='$update_id'";
  } else {
      // ถ้าไม่มีรูปภาพใหม่ อัปเดตเฉพาะข้อมูลอื่น ๆ
      $update_query = "UPDATE products SET name='$product_name', description='$product_description', price='$product_price', stock='$product_stock', category_id='$product_category_id' WHERE product_id='$update_id'";
  }

    $update_query = "UPDATE products SET name='$product_name', description='$product_description', price='$product_price', stock='$product_stock', category_id='$product_category_id, image='$product_image_folder WHERE product_id='$update_id'";
    if (mysqli_query($conn, $update_query)) {
        $message[] = 'Product updated successfully';
        header('Location: admin_stock1.php');
        exit();
    } else {
        $message[] = 'Failed to update product';
    }
}



// ลบสินค้า
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_query = "DELETE FROM products WHERE product_id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        $message[] = 'Product deleted successfully';
        header('Location: admin_stock1.php');
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
</head>
<body>
    <div class="container">
        <h2>Stock</h2>

        <!-- ฟอร์มแก้ไข -->
        <?php if (isset($edit_row)) { ?>
            <form action="" method="post">
                <input type="file" name="product_image" value="<?php echo $edit_row['image'];?>">
                <input type="hidden" name="update_id" value="<?php echo $edit_row['product_id']; ?>">
                <input type="text" name="product_name" value="<?php echo $edit_row['name']; ?>">
                <input type="text" name="product_price" value="<?php echo $edit_row['price']; ?>">
                <input type="text" name="product_description" value="<?php echo $edit_row['description']; ?>">
                <input type="text" name="product_stock" value="<?php echo $edit_row['stock']; ?>">
                <input type="text" name="product_category_id" value="<?php echo $edit_row['category_id']; ?>">
                <input type="submit" name="update_product" value="Save Changes">
                
               
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
                    <td>$<?php echo $row['price']; ?>/-</td>
                    <td><?php echo $row['stock']; ?></td>
                    <td><?php echo $row['category_id']; ?></td>
                    <td>
                        <a href="admin_stock1.php?edit=<?php echo $row['product_id']; ?>" class="btn"> <i class="fas fa-edit"></i> Edit </a>
                        <a href="admin_stock1.php?delete=<?php echo $row['product_id']; ?>" class="btn"> <i class="fas fa-trash"></i> Delete </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

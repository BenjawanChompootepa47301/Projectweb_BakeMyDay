<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
@include 'config.php';
if(!isset($_SESSION['admin_username'])){
   header('Location: admin_home.php');
   exit();
}

if(isset($_POST['add_product'])){
  

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_description = $_POST['product_description'];
   $product_stock = $_POST['product_stock'];
   $product_category_id = $_POST['product_category_id'];
   $product_image = $_FILES['product_image']['name'];
   $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
   $product_image_folder = 'uploaded_img/'.$product_image;
   

   if(empty($product_name) || empty($product_price) || empty($product_image)|| empty($product_description) || empty($product_stock) || empty($product_category_id)){
      $message[] = 'please fill out all';
   }else{
      $insert = "INSERT INTO products(name,description, price,stock, image, category_id) VALUES('$product_name', '$product_description','$product_price', $product_stock,'$product_image',$product_category_id)";
      $upload = mysqli_query($conn,$insert);
      if($upload){
         move_uploaded_file($product_image_tmp_name, $product_image_folder);
         $message[] = 'new product added successfully';
      }else{
         $message[] = 'could not add the product';
      }
   }

};


?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Page</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <!-- Custom CSS file link -->
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
            max-width: 600px;
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
            width: 50%;
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
            font-size: 14px;
            color: #000;
        }

        p a {
            color: #e74c3c;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php
if (isset($message)) {
   foreach ($message as $message) {
      echo '<centerspan class="message">'.$message.'</span>';
   }
}
?>
   
<div class="container">
    <h1 class="page-title">Bake My Day Admin Panel</h1>

    <div class="admin-product-form-container">
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
         
            <input type="text" placeholder="Product Name" name="product_name" class="box" required>
            <input type="text" placeholder="Product Description" name="product_description" class="box" required>
            <input type="number" placeholder="Product Price" name="product_price" class="box" required>
            <input type="number" placeholder="Stock Quantity" name="product_stock" class="box" required>
            <input type="number" placeholder="ID ชนิดสินค้า 1.Cookies 2.Cakes 3.Brownies 4.Macaron" name="product_category_id" class="box" min="1" max="4" required>
            <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box" required>
            <input type="submit" class="btn" name="add_product" value="Add Product">
            <a href="admin_stock.php" class="btn">go back</a>
        </form>
    </div>

   
</div>

</body>
</html>

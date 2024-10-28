<?php
session_start();
@include 'config.php';

// Initialize message array
$message = array();

if(isset($_POST['register'])){
    $admin_username = mysqli_real_escape_string($conn, $_POST['admin_username']);
    $admin_password = mysqli_real_escape_string($conn, $_POST['admin_password']);
    $admin_confirm_password = mysqli_real_escape_string($conn, $_POST['admin_confirm_password']);

    // Check if all fields are filled
    if(empty($admin_username) || empty($admin_password) || empty($admin_confirm_password)){
        $message[] = 'Please fill out all fields';
    } elseif($admin_password !== $admin_confirm_password){
        $message[] = 'Passwords do not match';
    } else {
        // Check if username already exists
        $query = "SELECT * FROM admins WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $admin_username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0){
            $message[] = 'Username already exists';
        } else {
            // Hash the password
            $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

            // Insert new admin into database
            $insert_query = "INSERT INTO admins (username, password) VALUES (?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "ss", $admin_username, $hashed_password);

            if(mysqli_stmt_execute($insert_stmt)){
                $message[] = 'Admin registered successfully';
                // Redirect to login page
                header('Location: admin_login.php');
                exit();
            } else {
                $message[] = 'Registration failed';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
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
            max-width: 400px;
            border-radius: 12px; /* กรอบมน */
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); /* เงา */
            text-align: center;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        form .box {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px; /* กรอบมน */
        }

        form .btn {
            width: 100%;
            padding: 10px;
            background-color: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 8px; /* กรอบมน */
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* เงา */
        }

        form .btn:hover {
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
    if(isset($message)){
        foreach($message as $msg){
            echo '<span class="message">'.$msg.'</span>';
        }
    }
    ?>

    <div class="container">
        <h2>Create Account</h2>
        <form action="" method="POST">
            <input type="text" name="admin_username" placeholder="Name" required class="box">
            <input type="password" name="admin_password" placeholder="Password" required class="box">
            <input type="password" name="admin_confirm_password" placeholder="Confirm Password" required class="box">
            <button type="submit" name="register" value="Register" class="btn">Submit</button>
            <p>มีบัญชีแล้ว? <a href="admin_login.php">Login</a></p> <!-- ลิงก์ไปหน้าลงทะเบียน -->
        </form>
    </div>
</body>
</html>

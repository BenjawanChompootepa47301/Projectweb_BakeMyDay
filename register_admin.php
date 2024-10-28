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
                header('Location: login_admin.php');
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
    <!-- Head content -->
    <title>Admin Registration</title>
    <!-- Include your CSS files -->
</head>
<body>
    <?php
    if(isset($message)){
        foreach($message as $msg){
            echo '<span class="message">'.$msg.'</span>';
        }
    }
    ?>

    <form action="" method="post">
        <h2>Register Admin</h2>
        <input type="text" name="admin_username" placeholder="Username" required>
        <input type="password" name="admin_password" placeholder="Password" required>
        <input type="password" name="admin_confirm_password" placeholder="Confirm Password" required>
        <input type="submit" name="register" value="Register">
    </form>
</body>
</html>

<?php 
    session_start();
    include('config.php');
    
    $errors = array();

    if (isset($_POST['reg_user'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password_1 = mysqli_real_escape_string($conn, $_POST['password_1']);
        $password_2 = mysqli_real_escape_string($conn, $_POST['password_2']);
        $phoneNumber = mysqli_real_escape_string($conn , $_POST['phone']);

       
        
        if (empty($email)) {
            array_push($errors, "* Email is required");
            $_SESSION['error'] = "* Email is required";
        }
        if (empty($password_1)) {
            array_push($errors, "* Password is required");
            $_SESSION['error'] = "* Password is required";
        }
        if ($password_1 != $password_2) {
            array_push($errors, "!! The two passwords do not match");
            $_SESSION['error'] = "!! The two passwords do not match";
        }

        $user_check_query = "SELECT * FROM customers WHERE username = '$username' OR email = '$email' LIMIT 1";
        $query = mysqli_query($conn, $user_check_query);
        $result = mysqli_fetch_assoc($query);

        if ($result) { // if user exists
            if ($result['email'] === $email and $result['username'] === $username) {
                array_push($errors, "Email and Username already exists!");
                $_SESSION['error'] = "Email and Username already exists!";
            }
            if ($result['username'] === $username) {
                array_push($errors, "Username already exists!");
                $_SESSION['error'] = "Username already exists!";
            }
            if ($result['email'] === $email) {
                array_push($errors, "Email already exists!");
                $_SESSION['error'] = "Email already exists!";
            }
            
        }

        if (count($errors) == 0) {
            $password = md5($password_1);

            $sql = "INSERT INTO customers(username, email, password , phone , state) VALUES ('$username','$email', '$password' , '$phoneNumber' , 'USER')";
            mysqli_query($conn, $sql);

            $_SESSION['username'] = $username;
            $_SESSION['success'] = "You are now logged in";
            header('location: login.php');
        } else {
            header("location: registerr.php");
        }
    }

?>

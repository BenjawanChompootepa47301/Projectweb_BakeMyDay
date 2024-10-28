<?php
session_start();
include 'config.php';

$message = array();
$username_error = ""; // Initialize a variable to hold the username error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $message[] = "กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        if ($stmt === false) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $username_error = "ชื่อผู้ใช้นี้ถูกใช้แล้ว"; // Set the error message for username
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt_insert = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            if ($stmt_insert === false) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            $stmt_insert->bind_param("ss", $username, $password_hash);
            if ($stmt_insert->execute()) {
                $_SESSION['user_id'] = $stmt_insert->insert_id;
                $_SESSION['username'] = $username;

                if (isset($_SESSION['redirect_url'])) {
                    $redirect_url = $_SESSION['redirect_url'];
                    unset($_SESSION['redirect_url']);
                    header("Location: $redirect_url");
                } else {
                    header('Location: product.php');
                }
                exit();
            } else {
                $message[] = "เกิดข้อผิดพลาดในการสมัครสมาชิก: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>

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
            background: #fff;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            border-radius: 12px; /* กรอบมน */
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); /* เงา */
            text-align: center;
        }

        h1 {
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

        .notification {
            color: #e74c3c; /* เปลี่ยนสีข้อความให้เป็นสีเดียวกับลิงค์ */
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #FF9999; /* ขอบสีเดียวกัน */
            border-radius: 8px; /* กรอบมน */
            background-color: #ffe6e6; /* สีพื้นหลังอ่อนสำหรับแจ้งเตือน */
            font-size: 14px; /* ขนาดฟอนต์เดียวกับ "มีบัญชีอยู่แล้ว?" */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Account</h1>
        <?php if (!empty($username_error)): ?>
            <div class="notification"><?php echo htmlspecialchars($username_error); ?></div>
        <?php endif; ?>
        <?php if (!empty($message)): ?>
            <div class="notification">
                <?php foreach ($message as $msg): ?>
                    <p><?php echo htmlspecialchars($msg); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="register.php">
            <input type="text" name="username" placeholder="Name" required class="box">
            <input type="password" name="password" placeholder="Password" required class="box">
            <button type="submit" class="btn">Create Account</button>
            <p>มีบัญชีอยู่แล้ว? <a href="login.php">Log in</a></p>
        </form>
    </div>
</body>
</html>

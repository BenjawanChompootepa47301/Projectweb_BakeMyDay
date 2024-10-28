<?php 
    session_start();
    include('config.php'); 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - เบเกอรี่</title>
    <style>
        /* Reset CSS เบื้องต้น */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* จัดการพื้นหลังและฟอนต์ */
body {
    font-family: Arial, sans-serif;
    background-color: #f7e7d3;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* จัดการคอนเทนเนอร์ของฟอร์ม */
.register-container {
    background-color: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 90%;
    max-width: 400px;
}

/* สไตล์หัวข้อ */
.register-container h2 {
    color: #d2691e;
    margin-bottom: 1.5rem;
    font-weight: bold;
}

/* จัดการฟิลด์ input */
label {
    display: block;
    font-size: 1rem;
    color: #6a4e3f;
    margin-bottom: 0.5rem;
}

input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 0.8rem;
    margin-bottom: 1.2rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    background-color: #fdf7f2;
}

/* จัดการกลุ่มปุ่ม */
.button-group {
    display: flex;
    justify-content: space-between;
    margin-top: 1.5rem;
}

/* สไตล์ปุ่มลงทะเบียน */
.btn-register {
    padding: 0.8rem 1.5rem;
    background-color: #d2691e;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-register:hover {
    background-color: #a7531b;
}

/* สไตล์ปุ่มเข้าสู่ระบบ */
.btn-login {
    padding: 0.8rem 1.5rem;
    background-color: #8b4513;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-login:hover {
    background-color: #69310e;
}
.notification {
            display: none;
            margin-top: 10px;
            color: #32CD32; /* สีเขียวแจ้งเตือน */
        }
    </style>
</head>
<div class="register-container">
        <h2>ลงทะเบียนสำหรับร้านเบเกอรี่ของเรา</h2>
        <form action="register_db.php" method="post">
            <label for="username">ชื่อผู้ใช้:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">อีเมล:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">รหัสผ่าน:</label>
            <input type="password" id="password" name="password_1" required>

            <label for="confirm_password">ยืนยันรหัสผ่าน:</label>
            <input type="password" id="confirm_password" name="password_2" required>

            <div class="button-group">
                <button type="submit" class="btn-register" name="reg_user" >ลงทะเบียน</button>
                <button type="button" class="btn-login" onclick="window.location.href='login.php'">เข้าสู่ระบบ</button>
            </div>
        </form>
    </div>

    <script>
        function register() {
            // สมมติว่าลงทะเบียนสำเร็จ
            document.getElementById("notification").style.display = "block";
            setTimeout(() => {
                redirectToLogin();
            }, 2000); // รอ 2 วินาทีแล้วเปลี่ยนไปหน้าล็อกอิน
        }

        function redirectToLogin() {
            window.location.href = "login.php"; // เปลี่ยน URL ไปหน้าล็อกอิน
        }
    </script>
</body>
</html>

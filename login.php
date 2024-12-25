<?php
// เริ่มต้นเซสชัน
session_start();

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // เชื่อมต่อฐานข้อมูล
    $conn = new mysqli('localhost', 'root', '', 'hotal_booking');

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die('การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $conn->connect_error);
    }

    // เตรียมคำสั่ง SQL เพื่อป้องกัน SQL Injection
    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // ตรวจสอบว่าพบผู้ใช้หรือไม่
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $user['password'] ?? '')) {
            $_SESSION['user_id'] = $user['customer_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];

            header('Location: room-list.php');
            exit;
        } else {
            $error = 'รหัสผ่านไม่ถูกต้อง';
        }
    } else {
        $error = 'ไม่พบอีเมลในระบบ';
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
        }

        .login-form h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .login-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .login-form button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .login-form button:hover {
            background: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .login-form {
                padding: 15px;
            }

            .login-form h2 {
                font-size: 18px;
            }

            .login-form button {
                font-size: 14px;
            }
        }

        .regis {
            text-align: center;
        }

        .container-regis {
            display: flex;
            justify-content: center;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <form class="login-form" method="POST">
        <h2>เข้าสู่ระบบ</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <input type="email" name="email" placeholder="อีเมล" required>
        <input type="password" name="password" placeholder="รหัสผ่าน" required>
        <button type="submit">เข้าสู่ระบบ</button>
        <div class="container-regis">
            <a class="regis" href=" ./register.php">ลงชื่อเข้าใช้</a>
        </div>
    </form>
</body>

</html>
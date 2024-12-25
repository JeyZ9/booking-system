<?php
// เริ่มต้นเซสชัน
session_start();

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $error = '';

    // ตรวจสอบรหัสผ่าน
    if ($password !== $confirm_password) {
        $error = 'รหัสผ่านไม่ตรงกัน';
    } else {
        // แฮชรหัสผ่าน
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // เชื่อมต่อฐานข้อมูล
        $conn = new mysqli('localhost', 'root', '', 'hotal_booking');

        // ตรวจสอบการเชื่อมต่อ
        if ($conn->connect_error) {
            die('การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $conn->connect_error);
        }

        // เตรียมคำสั่ง SQL เพื่อป้องกัน SQL Injection
        $stmt = $conn->prepare("INSERT INTO customers (customer_id, first_name, last_name, email, phone_number, password, created_at) VALUES (1, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone_number, $hashed_password);

        // บันทึกข้อมูล
        if ($stmt->execute()) {
            // เปลี่ยนเส้นทางไปยังหน้าเข้าสู่ระบบ
            header('Location: login.php');
            exit;
        } else {
            $error = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
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

        .register-form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
        }

        .register-form h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .register-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .register-form button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .register-form button:hover {
            background: #218838;
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

            .register-form {
                padding: 15px;
            }

            .register-form h2 {
                font-size: 18px;
            }

            .register-form button {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <form class="register-form" method="POST">
        <h2>สมัครสมาชิก</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <input type="text" name="first_name" placeholder="ชื่อ" required>
        <input type="text" name="last_name" placeholder="นามสกุล" required>
        <input type="email" name="email" placeholder="อีเมล" required>
        <input type="text" name="phone_number" placeholder="เบอร์โทรศัพท์" required>
        <input type="password" name="password" placeholder="รหัสผ่าน" required>
        <input type="password" name="confirm_password" placeholder="ยืนยันรหัสผ่าน" required>
        <button type="submit">สมัครสมาชิก</button>
    </form>
</body>

</html>
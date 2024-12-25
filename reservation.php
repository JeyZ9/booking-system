<?php
session_start();

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'hotal_booking');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die('การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_SESSION['user_id']; // สมมุติว่าเก็บ `user_id` ไว้ใน session
    $room_id = $_POST['room_id'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $status = 'Confirmed'; // สถานะการจอง

    // สร้างคำสั่ง SQL เพื่อบันทึกการจอง
    $stmt = $conn->prepare("INSERT INTO reservations (customer_id, room_id, check_in_date, check_out_date, reservation_date, status) VALUES (?, ?, ?, ?, NOW(), ?)");
    $stmt->bind_param("iisss", $customer_id, $room_id, $check_in_date, $check_out_date, $status);

    if ($stmt->execute()) {
        echo "การจองห้องพักสำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }

    $stmt->close();
}

// ดึงข้อมูลห้องพักที่เลือก
$room_id = $_GET['room_id'];
$result = $conn->query("SELECT * FROM rooms WHERE room_id = $room_id");
$room = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองห้องพัก</title>
</head>

<body>
    <div>
        <h2>จองห้องพัก: <?= htmlspecialchars($room['room_number']) ?> - <?= htmlspecialchars($room['room_type']) ?></h2>
        <form method="POST" action="">
            <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">

            <label for="check_in_date">วันที่เช็คอิน:</label>
            <input type="date" name="check_in_date" required><br>

            <label for="check_out_date">วันที่เช็คเอาต์:</label>
            <input type="date" name="check_out_date" required><br>

            <button type="submit">จองห้อง</button>
        </form>
    </div>
</body>

</html>
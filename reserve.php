<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'hotal_booking');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die('การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $conn->connect_error);
}

// ตรวจสอบว่ามีข้อมูลจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'] ?? '';
    $check_in_date = $_POST['check_in_date'] ?? '';
    $check_out_date = $_POST['check_out_date'] ?? '';
    $reservation_date = date('Y-m-d H:i:s');
    $status = 'pending';

    if (empty($room_id) || empty($check_in_date) || empty($check_out_date)) {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("
        SELECT * FROM reservations 
        WHERE room_id = ? 
          AND (
            (check_in_date <= ? AND check_out_date >= ?) 
            OR (check_in_date >= ? AND check_out_date <= ?)
          )
    ");
    $stmt->bind_param("issss", $room_id, $check_in_date, $check_in_date, $check_out_date, $check_out_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('ห้องพักนี้ถูกจองในช่วงเวลาที่เลือก กรุณาเลือกวันที่อื่น'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO reservations (customer_id, room_id, check_in_date, check_out_date, reservation_date, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $customer_id = 1;
    $stmt->bind_param("iissss", $customer_id, $room_id, $check_in_date, $check_out_date, $reservation_date, $status);

    if ($stmt->execute()) {
        echo "<script>alert('การจองสำเร็จ!'); window.location.href='rooms.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
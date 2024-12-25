<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'hotal_booking');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die('การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $conn->connect_error);
}

// ดึงข้อมูลห้องพักทั้งหมด
$result = $conn->query("SELECT * FROM rooms");

// ตรวจสอบห้องพักที่มีในระบบ
if ($result->num_rows > 0) {
    while ($room = $result->fetch_assoc()) {
        echo "<div class='room-card'>";
        echo "<h3>" . htmlspecialchars($room['room_type']) . "</h3>";
        echo "<p>ห้องหมายเลข: " . htmlspecialchars($room['room_number']) . "</p>";
        echo "<p>ราคา: " . number_format($room['price'], 2) . " บาท</p>";
        echo "<p>ความจุ: " . htmlspecialchars($room['capacity']) . " คน</p>";
        echo "<button class='btn-book' data-room-id='" . $room['room_id'] . "' data-room-type='" . htmlspecialchars($room['room_type']) . "'>จองห้อง</button>";
        echo "</div>";
    }
} else {
    echo "<p>ไม่พบห้องพัก</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แสดงห้องพัก</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F5EFE7;
            /* สีครีมอ่อน */
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }

        .room-card {
            background-color: #fff;
            padding: 20px;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 250px;
            text-align: center;
        }

        .btn-book {
            background-color: #3E5879;
            /* สีฟ้ากลาง */
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-book:hover {
            background-color: #213555;
            /* สีฟ้ามืด */
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 30px;
            border-radius: 10px;
            width: 50%;
        }

        .modal-header,
        .modal-body,
        .modal-footer {
            margin-bottom: 20px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
        }

        .modal-header h2 {
            margin: 0;
            color: #213555;
            /* สีฟ้ามืด */
        }

        .close-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .close-btn:hover {
            background-color: #cc0000;
        }

        input[type="date"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #D8C4B6;
            /* สีเบจอ่อน */
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #3E5879;
            /* สีฟ้ากลาง */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #213555;
            /* สีฟ้ามืด */
        }
    </style>

</head>

<body>

    <!-- รายการห้องพัก -->
    <div class="room-list">
        <!-- ห้องพักจะถูกแสดงที่นี่โดย PHP -->
    </div>

    <!-- Modal Popup สำหรับการจองห้อง -->
    <div id="reservationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>จองห้องพัก</h2>
                <button class="close-btn" onclick="closeModal()">X</button>
            </div>
            <div class="modal-body">
                <form id="reservationForm" method="POST" action="reserve.php">
                    <input type="hidden" name="room_id" id="room_id" value="">
                    <div>
                        <label for="check_in_date">วันที่เช็คอิน:</label>
                        <input type="date" name="check_in_date" required>
                    </div>
                    <div>
                        <label for="check_out_date">วันที่เช็คเอาต์:</label>
                        <input type="date" name="check_out_date" required>
                    </div>
                    <button type="submit">ยืนยันการจอง</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // เปิด Modal Popup
        document.querySelectorAll('.btn-book').forEach(button => {
            button.addEventListener('click', function () {
                const roomId = this.getAttribute('data-room-id');
                const roomType = this.getAttribute('data-room-type');

                // เติมข้อมูลห้องพักที่เลือกลงในฟอร์ม
                document.getElementById('room_id').value = roomId;

                // แสดง Modal
                document.getElementById('reservationModal').style.display = 'block';
            });
        });

        // ปิด Modal Popup
        function closeModal() {
            document.getElementById('reservationModal').style.display = 'none';
        }

        // ปิด Modal เมื่อคลิกนอก Modal
        window.onclick = function (event) {
            const modal = document.getElementById('reservationModal');
            if (event.target === modal) {
                closeModal();
            }
        };
    </script>
</body>

</html>
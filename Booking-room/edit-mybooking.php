<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
include 'check_user.php';
include 'db/db_connection.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// ตรวจสอบว่าได้รับ ID ของการจอง
if (!isset($_GET['id'])) {
    echo "ไม่พบการจองนี้";
    exit();
}

// เช็คว่า session มีข้อความแจ้งเตือนหรือไม่
if (isset($_SESSION['error_message'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: '{$_SESSION['error_message']}',
                confirmButtonText: 'ตกลง'
            });
          </script>";
    unset($_SESSION['error_message']); // ลบข้อความหลังแสดงแล้ว
}

if (isset($_SESSION['success_message'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: '{$_SESSION['success_message']}',
                confirmButtonText: 'ตกลง'
            });
          </script>";
    unset($_SESSION['success_message']); // ลบข้อความหลังแสดงแล้ว
}

$id = $_GET['id'];

// ดึงข้อมูลการจองจากฐานข้อมูล
$id = $_GET['id'];
$sql = "SELECT * FROM bookings WHERE id = $id AND user_id = {$_SESSION['user_id']}";
$result = $conn->query($sql);
$booking = $result->fetch_assoc();


if (!$booking) {
    echo "ไม่พบการจองที่ต้องการแก้ไข";
    exit();
}

// ดึงข้อมูลห้อง
$room_res = $conn->query("SELECT * FROM rooms WHERE is_visible = 1");
$rooms = $room_res->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขการจอง</title>
    <link rel="stylesheet" href="css/theme.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        body {

            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 40%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 130px;
        }
/* เพิ่มการจัดรูปแบบสำหรับช่องเลือกวันและเวลา */
.form-group {
    display: flex;
    flex-wrap: wrap; /* ให้ช่องสามารถยืดหยุ่นเมื่อจอเล็กลง */
    gap: 10px;
    margin-bottom: 20px;
}

.form-group div {
    width: 100%; /* ให้แต่ละช่องเต็มความกว้าง */
}

@media (min-width: 768px) {
    .form-group div {
        width: 48%; /* แบ่งช่องให้มีขนาด 48% สำหรับหน้าจอที่กว้างขึ้น */
    }
}

input[type="date"],
input[type="time"] {
    width: 100%; /* ทำให้ช่องเลือกวันที่และเวลาเต็มความกว้าง */
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type="date"]:focus,
input[type="time"]:focus {
    outline: none;
    border-color: #6a71d7; /* เพิ่มสี border เมื่อช่องถูกเลือก */
}

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: grid;
            gap: 20px;
        }

        label {
            font-size: 16px;
            color: #555;
        }

        input[type="text"],
        input[type="tel"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        .cb input[type="checkbox"] {
            margin-right: 10px;
        }

        .form-group {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .form-group input,
        .form-group select {
            width: 48%;
        }

        .btn-save {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-save:hover {
            background-color: #45a049;
        }

        .btn-clear {
            background-color: #f44336;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-clear:hover {
            background-color: #e53935;
        }
        /* ปุ่มกลับ */
.btn-back {
    align-items: center;
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 16px;
    margin-bottom: 20px;
    transition: background-color 0.3s;
}

.btn-back:hover {
    background-color: #0056b3;
}

.arrow {
    font-size: 15px;
    margin-right: 8px;
}

    </style>
</head>
<body>
<!-- หน้า HTML สำหรับการแก้ไขการจอง -->
<div class="container">
<a href="mybk.php" class="btn-back">
    <span class="arrow">&#8592;</span>
</a>

    <h2>แก้ไขการจอง</h2>
    <form action="update_booking.php" method="POST">
        <input type="hidden" name="id" value="<?= $booking['id'] ?>">

        <label>ชื่อห้อง:</label>
        <select name="room_id" required>
            <?php foreach ($rooms as $room): ?>
                <option value="<?= $room['id'] ?>" <?= $room['id'] == $booking['room_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($room['room_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>จำนวนผู้เข้าร่วม:</label>
        <input type="number" name="participants" value="<?= htmlspecialchars($booking['participants']) ?>" required>

        <label>ชื่อผู้จอง:</label>
        <input type="text" name="booker_name" value="<?= htmlspecialchars($booking['booker_name']) ?>" required>

        <label>โทรศัพท์:</label>
        <input type="tel" name="phone" value="<?= htmlspecialchars($booking['phone']) ?>" required>

        <div class="form-group">
    <div>
        <label>วันที่เริ่มต้น:</label>
        <input type="date" name="start_date" value="<?= $booking['start_date'] ?>" required>
    </div>
    <div>
        <label>เวลาเริ่มต้น:</label>
        <input type="time" name="start_time" value="<?= $booking['start_time'] ?>" required>
    </div>
</div>

<div class="form-group">
    <div>
        <label>วันที่สิ้นสุด:</label>
        <input type="date" name="end_date" value="<?= $booking['end_date'] ?>" required>
    </div>
    <div>
        <label>เวลาสิ้นสุด:</label>
        <input type="time" name="end_time" value="<?= $booking['end_time'] ?>" required>
    </div>
</div>


        <label>ใช้สำหรับ:</label>
        <select name="purpose">
            <option value="ประชุม" <?= $booking['purpose'] == 'ประชุม' ? 'selected' : '' ?>>ประชุม</option>
            <option value="สัมมนา" <?= $booking['purpose'] == 'สัมมนา' ? 'selected' : '' ?>>สัมมนา</option>
            <option value="อบรม" <?= $booking['purpose'] == 'อบรม' ? 'selected' : '' ?>>อบรม</option>
        </select>

        <label>อุปกรณ์:</label>
        <div class="cb">
            โปรเจคเตอร์ <input type="checkbox" name="equipment[]" value="โปรเจคเตอร์" <?= in_array('โปรเจคเตอร์', explode(',', $booking['equipment'])) ? 'checked' : '' ?>>
            ไมค์ <input type="checkbox" name="equipment[]" value="ไมค์" <?= in_array('ไมค์', explode(',', $booking['equipment'])) ? 'checked' : '' ?>>
        </div>

        <label>อื่นๆ:</label>
        <textarea name="other_details"><?= htmlspecialchars($booking['other_details']) ?></textarea>

        <button type="submit" class="btn-save">บันทึกการแก้ไข</button>
        <button type="reset" class="btn-clear">ล้างฟอร์ม</button>
    </form>
    
</div>

</body>
</html>
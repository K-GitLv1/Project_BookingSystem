<?php
include 'db/db_connection.php';

session_start();
date_default_timezone_set('Asia/Bangkok');

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อนทำการจอง'); window.location.href='login.php';</script>";
    exit();
}

// รับค่าจากฟอร์ม
$user_id = $_SESSION['user_id'];
$room_id = $_POST['room_id'];
$participants = $_POST['participants'];
$booker_name = $_POST['booker_name'];
$phone = $_POST['phone'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$purpose = $_POST['purpose'];
$equipment = isset($_POST['equipment']) ? implode(", ", $_POST['equipment']) : '';
$other_details = $_POST['other_details'];

// ตรวจสอบวันที่เริ่มต้นและสิ้นสุดว่าเป็นวันที่ในอนาคต
$current_date = date('Y-m-d'); // วันที่ปัจจุบัน
if ($start_date < $current_date) {
    echo "<script>alert('ไม่สามารถจองห้องย้อนหลังได้ กรุณาเลือกวันที่ใหม่อีกครั้ง'); window.location.href = 'bk.php';</script>";
    exit();
}

if ($end_date < $current_date) {
    echo "<script>alert('ไม่สามารถเลือกวันที่สิ้นสุดที่เป็นวันที่ผ่านมาแล้วได้ กรุณาเลือกวันที่ใหม่อีกครั้ง'); window.location.href = 'bk.php';</script>";
    exit();
}

// ตรวจสอบว่า end_date ไม่ควรน้อยกว่า start_date
if ($start_date > $end_date) {
    echo "<script>alert('วันที่สิ้นสุดต้องมากกว่าหรือเท่ากับวันที่เริ่มต้น'); window.location.href = 'bk.php';</script>";
    exit();
}

// ตรวจสอบให้เวลาการจองไม่น้อยกว่า 30 นาที
$start_datetime = DateTime::createFromFormat('Y-m-d H:i', $start_date . ' ' . $start_time);
$end_datetime = DateTime::createFromFormat('Y-m-d H:i', $end_date . ' ' . $end_time);

// คำนวณความต่างของเวลาระหว่าง start_datetime และ end_datetime
$interval = $start_datetime->diff($end_datetime);

// ตรวจสอบว่าเวลาต่างไม่น้อยกว่า 30 นาที
if (($interval->days == 0 && $interval->h == 0 && $interval->i < 30) || ($interval->days < 0)) {
    echo "<script>alert('เวลาการจองต้องไม่น้อยกว่า 30 นาที'); window.location.href = 'bk.php';</script>";
    exit();
}

// ตรวจสอบการจองซ้อน
$check_sql = "SELECT * FROM bookings WHERE room_id = ? 
              AND ((start_date <= ? AND end_date >= ?) 
              AND (start_time < ? AND end_time > ?))";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("issss", $room_id, $end_date, $start_date, $end_time, $start_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>
            alert('ห้องนี้ถูกจองในช่วงเวลาที่เลือกแล้ว กรุณาเลือกเวลาอื่น');
            window.location.href = 'bk.php'; // กลับไปหน้าจองห้อง
          </script>";
    exit();
}

// ตรวจสอบจำนวนผู้เข้าร่วมไม่ให้เกินความจุห้อง
$sql_capacity = "SELECT capacity FROM rooms WHERE id = ?";
$stmt_capacity = $conn->prepare($sql_capacity);
$stmt_capacity->bind_param("i", $room_id);
$stmt_capacity->execute();
$result_capacity = $stmt_capacity->get_result();

if ($result_capacity->num_rows > 0) {
    $row = $result_capacity->fetch_assoc();
    $capacity = $row['capacity'];
    
    if ($participants > $capacity) {
        echo "<script>
                alert('จำนวนผู้เข้าร่วมเกินความจุห้อง');
                window.location.href = 'bk.php'; // กลับไปหน้าจองห้อง
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('ไม่พบข้อมูลห้องที่เลือก');
            window.location.href = 'bk.php'; // กลับไปหน้าจองห้อง
          </script>";
    exit;
}

// บันทึกข้อมูลการจอง
$sql = "INSERT INTO bookings (user_id, room_id, participants, booker_name, phone, start_date, end_date, start_time, end_time, purpose, equipment, other_details) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiisssssssss", $user_id, $room_id, $participants, $booker_name, $phone, $start_date, $end_date, $start_time, $end_time, $purpose, $equipment, $other_details);

if ($stmt->execute()) {
    echo "<script>alert('จองห้องสำเร็จ!'); window.location.href='bk.php';</script>";
} else {
    echo "<script>alert('เกิดข้อผิดพลาด: " . $stmt->error . "'); window.location.href='bk.php';</script>";
}

// ปิดการเชื่อมต่อ
$stmt->close();
$conn->close();
?>

<?php
include 'check_user.php';
include 'db/db_connection.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// กำหนด timezone ให้ตรง
date_default_timezone_set('Asia/Bangkok');

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $id = $_POST['id'];
    $room_id = $_POST['room_id'];
    $participants = $_POST['participants'];
    $booker_name = $_POST['booker_name'];
    $phone = $_POST['phone'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $purpose = $_POST['purpose'];
    $equipment = isset($_POST['equipment']) ? implode(',', $_POST['equipment']) : '';
    $other_details = $_POST['other_details'];

    // ตรวจสอบการจองที่เกี่ยวข้องกับ user_id และ id ที่ถูกต้อง
    $sql = "SELECT * FROM bookings WHERE id = $id AND user_id = {$_SESSION['user_id']}";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // ตรวจสอบไม่ให้จองย้อนหลัง
        $current_timestamp = time();  // เวลาปัจจุบันเป็น timestamp
        $start_timestamp = strtotime($start_date . ' ' . $start_time);  // เวลาที่ผู้ใช้เลือกเป็น timestamp

        if ($start_timestamp < $current_timestamp) {
            // ส่งค่าผ่าน URL ไปที่หน้า edit-mybooking.php
            $_SESSION['error_message'] = 'ไม่สามารถจองย้อนหลังได้';
            header("Location: edit-mybooking.php?id=$id");
            exit();
        }

        // ตรวจสอบว่า วันที่เริ่มต้นต้องไม่มากกว่าหรือเท่ากับวันที่สิ้นสุด
        if ($start_date > $end_date) {
            $_SESSION['error_message'] = 'วันที่เริ่มต้นไม่สามารถมากกว่าหรือเท่ากับวันที่สิ้นสุด';
            header("Location: edit-mybooking.php?id=$id");
            exit();
        }

        // แปลงเวลาจากรูปแบบ HH:MM เป็น timestamp
        $start_timestamp = strtotime($start_date . ' ' . $start_time);
        $end_timestamp = strtotime($end_date . ' ' . $end_time);

        // ตรวจสอบว่าเวลาห่างกันไม่น้อยกว่า 30 นาที
        if (($end_timestamp - $start_timestamp) < 30 * 60) {
            $_SESSION['error_message'] = 'เวลาจองต้องห่างกันไม่น้อยกว่า 30 นาที';
            header("Location: edit-mybooking.php?id=$id");
            exit();
        }

// ตรวจสอบการจองห้องในช่วงเวลาที่เลือก
$check_sql = "SELECT * FROM bookings WHERE room_id = ? 
              AND ((start_date <= ? AND end_date >= ?) 
              AND (start_time < ? AND end_time > ?)) 
              AND id != ?";
$stmt_check = $conn->prepare($check_sql);
$stmt_check->bind_param("issssi", $room_id, $end_date, $start_date, $end_time, $start_time, $id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $_SESSION['error_message'] = 'ไม่สามารถจองห้องนี้ในช่วงเวลานี้ได้ เนื่องจากมีการจองแล้ว';
    header("Location: edit-mybooking.php?id=$id");
    exit();
}




        // อัพเดตข้อมูลการจอง
        $update_sql = "UPDATE bookings SET 
        room_id = ?, 
        participants = ?, 
        booker_name = ?, 
        phone = ?, 
        start_date = ?, 
        end_date = ?, 
        start_time = ?, 
        end_time = ?, 
        purpose = ?, 
        equipment = ?, 
        other_details = ? 
        WHERE id = ? AND user_id = {$_SESSION['user_id']}";

        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("iisssssssssi", 
            $room_id, 
            $participants, 
            $booker_name, 
            $phone, 
            $start_date, 
            $end_date, 
            $start_time, 
            $end_time, 
            $purpose, 
            $equipment, 
            $other_details, 
            $id
        );

        // ถ้าการอัพเดตสำเร็จ
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'บันทึกสำเร็จ!';
            header("Location: mybk.php?id=$id");
            exit();
        } else {
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาด!';
            header("Location: mybk.php?id=$id");
            exit();
        }
    } else {
        echo "ไม่พบการจองที่คุณต้องการแก้ไข";
    }
} else {
    echo "ข้อมูลไม่ถูกต้อง";
}
?>

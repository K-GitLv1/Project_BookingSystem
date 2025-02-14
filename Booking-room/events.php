<?php
include 'db/db_connection.php'; // เชื่อมต่อกับฐานข้อมูล

$sql = "SELECT * FROM bookings"; // ดึงข้อมูลทั้งหมด
$result = $conn->query($sql);

$events = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // แปลงเวลาให้อยู่ในรูปแบบ HH:mm
        $start_time = date("H:i", strtotime($row['start_time']));
        $end_time = date("H:i", strtotime($row['end_time']));

        // แสดงหัวข้อเป็น "ประชุม" แทนการแสดงช่วงเวลา
        $title = "ประชุม";  // ตั้งหัวข้อให้เป็น "ประชุม" แทนที่จะแสดงเวลา

        // กำหนดสีตามค่า is_confirmed
        $status_class = '';
        if ($row['is_confirmed'] == 1) {
            $status_class = 'confirmed';  // อนุมัติ (สีเขียว)
        } elseif ($row['is_confirmed'] == 2) {
            $status_class = 'unconfirmed';  // ไม่อนุมัติ (สีแดง)
        } else {
            $status_class = 'pending';  // รออนุมัติ (สีเหลือง)
        }

        // เพิ่มรายละเอียดการจองใน `extendedProps` เพื่อใช้ใน `eventClick`
        $events[] = array(
            'title' => $title,  // แสดงหัวข้อเป็น "ประชุม"
            'start' => $row['start_date'] . 'T' . $row['start_time'],
            'end' => $row['end_date'] . 'T' . $row['end_time'],
            'bookerName' => $row['booker_name'], // ชื่อผู้จอง
            'phone' => $row['phone'], // เบอร์โทรศัพท์
            'additionalDetails' => $row['other_details'], // รายละเอียดเพิ่มเติม
            'className' => $status_class
        );
    }
}

// ส่งข้อมูลในรูปแบบ JSON
echo json_encode($events);

$conn->close();
?>

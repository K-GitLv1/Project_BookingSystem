<?php
include 'db/db_connection.php';

// รับค่า ID จาก URL
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ตรวจสอบว่ามี ID หรือไม่
if ($booking_id) {
    // ดึงข้อมูลการจองจากฐานข้อมูล
    $sql = "SELECT b.id, b.participants, b.booker_name, b.phone, 
                   b.start_date, b.end_date, b.start_time, b.end_time, 
                   b.purpose, b.equipment, b.other_details, b.is_confirmed, 
                   r.room_name 
            FROM bookings b 
            JOIN rooms r ON b.room_id = r.id 
            WHERE b.id = $booking_id";
    
    $result = $conn->query($sql);
    $booking = $result->fetch_assoc();

    if ($booking) {
        // แสดงรายละเอียดการจองใน HTML
        echo "<p><strong>ห้อง:</strong> " . htmlspecialchars($booking['room_name']) . "</p>";
        echo "<p><strong>ผู้จอง:</strong> " . htmlspecialchars($booking['booker_name']) . "</p>";
        echo "<p><strong>โทรศัพท์:</strong> " . htmlspecialchars($booking['phone']) . "</p>";
        echo "<p><strong>วันที่เริ่มต้น:</strong> " . htmlspecialchars($booking['start_date']) . "</p>";
        echo "<p><strong>วันที่สิ้นสุด:</strong> " . htmlspecialchars($booking['end_date']) . "</p>";
        echo "<p><strong>เวลาเริ่มต้น:</strong> " . htmlspecialchars($booking['start_time']) . "</p>";
        echo "<p><strong>เวลาสิ้นสุด:</strong> " . htmlspecialchars($booking['end_time']) . "</p>";
        echo "<p><strong>ใช้สำหรับ:</strong> " . htmlspecialchars($booking['purpose']) . "</p>";
        echo "<p><strong>อุปกรณ์:</strong> " . htmlspecialchars($booking['equipment']) . "</p>";
        echo "<p><strong>รายละเอียดอื่นๆ:</strong> " . htmlspecialchars($booking['other_details']) . "</p>";
    } else {
        echo "<p>ไม่พบข้อมูลการจอง</p>";
    }
} else {
    echo "<p>ไม่พบข้อมูลการจอง</p>";
}
?>

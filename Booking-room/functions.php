<?php
function getTopResources($period) {
    global $conn;
    // คำสั่ง SQL สำหรับดึงข้อมูล Top 5 รีซอร์ส
    $sql = "SELECT room_name, COUNT(*) as bookings FROM bookings WHERE booking_date BETWEEN '$start_date' AND '$end_date' GROUP BY room_name ORDER BY bookings DESC LIMIT 5";
    $result = $conn->query($sql);
    return $result;
}
?>

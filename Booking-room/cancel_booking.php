<?php

include 'db/db_connection.php';  // เชื่อมต่อฐานข้อมูล
include 'check_user.php';
// ตรวจสอบว่า ID การจองถูกส่งมาหรือไม่
if (isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);  // รับค่า ID ของการจองที่ต้องการยกเลิก

    // ตรวจสอบว่าผู้ใช้มีสิทธิ์ในการยกเลิกการจองนี้หรือไม่
    $sql = "SELECT user_id FROM bookings WHERE id = $booking_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();

        // หากผู้ใช้เป็นเจ้าของการจองนี้, ลบการจอง
        if ($booking['user_id'] == $_SESSION['user_id']) {
            // ลบการจองจากฐานข้อมูล
            $delete_sql = "DELETE FROM bookings WHERE id = $booking_id";
            if ($conn->query($delete_sql)) {
                // การลบสำเร็จ
                echo "การจองถูกยกเลิกเรียบร้อยแล้ว";
            } else {
                // หากเกิดข้อผิดพลาด
                echo "เกิดข้อผิดพลาดในการยกเลิกการจอง";
            }
        } else {
            echo "คุณไม่มีสิทธิ์ในการยกเลิกการจองนี้";
        }
    } else {
        echo "ไม่พบข้อมูลการจอง";
    }
} else {
    echo "ไม่มีข้อมูลการจองที่ต้องการยกเลิก";
}

$conn->close();  // ปิดการเชื่อมต่อฐานข้อมูล
?>

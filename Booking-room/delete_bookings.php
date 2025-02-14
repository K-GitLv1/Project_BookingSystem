<?php
include 'check_admin.php';
include 'db/db_connection.php';

// ตั้งค่าให้ response เป็น JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_ids'])) {
    $booking_ids = json_decode($_POST['booking_ids'], true);

    if (!empty($booking_ids) && is_array($booking_ids)) {
        // แปลงค่าให้เป็นตัวเลขเพื่อป้องกัน SQL Injection
        $ids = implode(',', array_map('intval', $booking_ids));

        $sql = "DELETE FROM bookings WHERE id IN ($ids)";
        if ($conn->query($sql)) {
            echo json_encode(["status" => "success", "message" => "ลบการจองที่เลือกสำเร็จ"]);
        } else {
            echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการลบข้อมูล"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "ไม่พบรายการที่ต้องการลบ"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "คำขอไม่ถูกต้อง"]);
}

$conn->close();
?>

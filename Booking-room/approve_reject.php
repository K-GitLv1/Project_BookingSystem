<?php
include 'db/db_connection.php';

// การอนุมัติการจอง
if (isset($_POST['approve_id'])) {
    $approve_id = intval($_POST['approve_id']);
    $stmt = $conn->prepare("UPDATE bookings SET is_confirmed = 1 WHERE id = ?");
    $stmt->bind_param("i", $approve_id); // "i" หมายถึง integer
    if ($stmt->execute()) {
        echo "อนุมัติการจองแล้ว";
    } else {
        echo "เกิดข้อผิดพลาดในการอนุมัติ";
    }
    $stmt->close();
}

// การปฏิเสธการจอง
if (isset($_POST['reject_id'])) {
    $reject_id = intval($_POST['reject_id']);
    $stmt = $conn->prepare("UPDATE bookings SET is_confirmed = 2 WHERE id = ?");
    $stmt->bind_param("i", $reject_id); // "i" หมายถึง integer
    if ($stmt->execute()) {
        echo "ปฏิเสธการจองแล้ว";
    } else {
        echo "เกิดข้อผิดพลาดในการปฏิเสธ";
    }
    $stmt->close();
}
?>

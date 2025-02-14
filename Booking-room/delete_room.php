<?php
// เชื่อมต่อฐานข้อมูล
include 'db/db_connection.php';
include 'check_admin.php';
// ตรวจสอบว่าได้ส่งค่า 'id' มาหรือไม่
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $roomId = $_GET['id'];

    // คำสั่ง SQL สำหรับลบห้องประชุม
    $sql = "DELETE FROM rooms WHERE id = ?";
    
    // เตรียมคำสั่ง SQL
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $roomId); // กำหนดให้ id เป็นชนิดข้อมูล Integer
    
    // ตรวจสอบผลลัพธ์การลบ
    if ($stmt->execute()) {
        // ถ้าลบสำเร็จ ให้กลับไปยังหน้าห้องประชุม
        header("Location: manage_rooms.php?status=success");
        exit();
    } else {
        // ถ้ามีข้อผิดพลาดในการลบ ให้แสดงข้อความ
        echo "เกิดข้อผิดพลาดในการลบห้อง: " . $conn->error;
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $stmt->close();
} else {
    echo "ไม่พบข้อมูลห้องที่ต้องการลบ";
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

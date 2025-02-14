<?php
include 'db/db_connection.php';

// รับข้อมูล JSON
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['ids']) && is_array($data['ids'])) {
    $ids = implode(',', array_map('intval', $data['ids'])); // แปลงเป็นเลขและรวมเป็น String
    $sql = "DELETE FROM rooms WHERE id IN ($ids)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ลบข้อมูลไม่สำเร็จ: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ไม่มีข้อมูลที่ถูกส่งมา']);
}
$conn->close();
?>

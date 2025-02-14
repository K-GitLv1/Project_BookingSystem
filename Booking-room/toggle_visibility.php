<?php
include 'db/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $room_id = intval($_POST['id']);

    // ดึงสถานะปัจจุบัน
    $sql = "SELECT is_visible FROM rooms WHERE id = $room_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_visibility = $row['is_visible'];

        // สลับสถานะ
        $new_visibility = $current_visibility ? 0 : 1;

        // อัปเดตสถานะในฐานข้อมูล
        $update_sql = "UPDATE rooms SET is_visible = $new_visibility WHERE id = $room_id";
        if ($conn->query($update_sql) === TRUE) {
            echo json_encode(['status' => 'success', 'new_visibility' => $new_visibility]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Room not found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}

$conn->close();
?>

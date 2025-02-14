<?php
include 'db/db_connection.php';
include 'check_admin.php';
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // ลบผู้ใช้จากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('ลบผู้ใช้สำเร็จ'); window.location='manage_users.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

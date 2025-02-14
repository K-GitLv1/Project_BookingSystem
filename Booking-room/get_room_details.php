<?php
include('db/db_connection.php'); // รวมการเชื่อมต่อกับฐานข้อมูล

if (isset($_GET['id'])) {
    $roomId = $_GET['id'];

    // ดึงข้อมูลห้องจากฐานข้อมูล
    $sql = "SELECT * FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $room = $result->fetch_assoc();
        echo "<h3>" . $room['room_name'] . "</h3>";
        echo "<p><strong>รายละเอียด:</strong> " . $room['description'] . "</p>";
        echo "<p><strong>สถานที่:</strong> " . $room['location'] . "</p>";
        echo "<p><strong>ความจุ:</strong> " . $room['capacity'] . " คน</p>";
        // echo "<p><strong>หมายเลขห้อง:</strong> " . $room['room_number'] . "</p>";
        if ($room['image']) {
            echo "<img src='" . $room['image'] . "' alt='Room Image' class='img-fluid'>";
        }
    } else {
        echo "ไม่พบข้อมูลห้องนี้.";
    }
}
?>

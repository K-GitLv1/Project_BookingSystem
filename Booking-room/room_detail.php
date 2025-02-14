<?php
// เชื่อมต่อฐานข้อมูล
include 'db/db_connection.php';
include 'check_login.php';
// ตรวจสอบการรับค่าจาก URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $roomId = $_GET['id'];
} else {
    echo "ข้อมูลห้องไม่ถูกต้องหรือไม่มีการส่งค่า id";
    exit;
}

// ดึงข้อมูลห้องจากฐานข้อมูล
$sql = "SELECT * FROM rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $roomId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $room = $result->fetch_assoc();
} else {
    echo "ไม่พบข้อมูลห้อง";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดห้อง</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme.css">
</head>
<body>
<style>
    body {
        margin-top: 50px; /* เพิ่มช่องว่างด้านบนของ body */
    }

    .container {
        padding-top: 50px; /* เพิ่มช่องว่างภายใน container */
        padding-bottom: 50px;
    }


    .card-img {
        max-width: 100%; /* ให้รูปไม่เกินความกว้างของ container */
        max-height: 600px; /* กำหนดความสูงสูงสุดเป็น 400px */
        object-fit: cover; /* ทำให้รูปไม่บิดเบือน */
    }
</style>

<div class="container mt-5">
    <div class="card" style="width: 100%;">
        <div class="card-header text-center">
            <h3>รายละเอียดห้องประชุม</h3>
        </div>
        <div class="card-body">
            <h5 class="card-title">ชื่อห้อง: <?php echo htmlspecialchars($room['room_name']); ?></h5>
            <p class="card-text"><strong>รายละเอียด:</strong> <?php echo htmlspecialchars($room['description']); ?></p>
            <p class="card-text"><strong>ความจุ:</strong> <?php echo htmlspecialchars($room['capacity']); ?> คน</p>
            <p class="card-text"><strong>สถานที่:</strong> <?php echo htmlspecialchars($room['location']); ?></p>
            <p><strong>ภาพสถานที่:</strong></p>
            
            <?php if ($room['image']): ?>
                <img src="<?php echo htmlspecialchars($room['image']); ?>" alt="Room Image" class="img-fluid rounded mx-auto d-block card-img">
            <?php else: ?>
                <p>ไม่มีภาพห้อง</p>
            <?php endif; ?>

        </div>
        <!-- เพิ่มปุ่มย้อนกลับ -->
        <div class="card-footer text-center">
            <button class="btn btn-secondary" onclick="window.history.back()">ย้อนกลับ</button>
        </div>
    </div>
</div>

</body>
</html>

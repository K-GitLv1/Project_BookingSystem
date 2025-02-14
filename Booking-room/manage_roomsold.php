<?php
// เชื่อมต่อฐานข้อมูล
include 'db/db_connection.php';

// ดึงข้อมูลห้องประชุมทั้งหมด
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการห้องประชุม</title>
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/mgr.css">


    <script src="js/cb.js"></script>

</head>
<body>
<?php 
    include 'check_login.php';
?>

<div class="rooms-container">
    <table>
        <thead>
    <tr>
        
        <th>ชื่อห้อง</th>
        <th>รูปภาพ</th>
        <th>อาคาร / สถานที่</th>
        <th>เลขที่ห้อง</th>
        <th>จำนวนที่นั่ง</th>
        <th>สถานะการจอง</th>
        <th>การจัดการ</th>
        <th><input type="checkbox" id="select-all"></th> <!-- Checkbox สำหรับเลือกทั้งหมด -->
    </tr>
</thead>
<tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                <td>
                    <?php if (!empty($row['image']) && file_exists('uploads/' . $row['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="รูปห้อง" width="50">
                    <?php else: ?>
                        ไม่มีรูปภาพ
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                <td>
                    <span class="eye-icon" data-id="<?php echo $row['id']; ?>" data-visible="<?php echo $row['is_visible']; ?>">
                        <?php if ($row['is_visible']): ?>
                            <img src="icons/eye-open.png" alt="เปิดการจอง" width="20">
                        <?php else: ?>
                            <img src="icons/eye-closed.png" alt="ปิดการจอง" width="20">
                        <?php endif; ?>
                    </span>
                </td>
                <td>
                    <a href="edit_room.php?id=<?php echo $row['id']; ?>" class="btn-edit">แก้ไข</a>
                    <a href="delete_room.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('ยืนยันการลบห้องนี้?')">ลบ</a>
                </td>
                <td>
                    <input type="checkbox" class="delete-checkbox" value="<?php echo $row['id']; ?>">
                </td> <!-- ย้ายมาอยู่สุดท้าย -->
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="8" class="no-rooms">ไม่มีห้องประชุม</td>
        </tr>
    <?php endif; ?>
</tbody>


    </table>
</div>
<!-- Floating Button -->
<a href="add_room.php" class="float-btn">
    <img src="icons/plus-icon.png" alt="เพิ่มห้องประชุม" width="30" height="30">
</a>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const eyeIcons = document.querySelectorAll('.eye-icon');

    eyeIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const roomId = this.getAttribute('data-id');
            const currentVisible = this.getAttribute('data-visible');

            // ส่งข้อมูลด้วย AJAX
            fetch('toggle_visibility.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${roomId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // อัปเดตสถานะใน DOM
                    const newVisibility = data.new_visibility;
                    const img = this.querySelector('img');
                    if (newVisibility == 1) {
                        img.src = 'icons/eye-open.png';
                        img.alt = 'เปิดการจอง';
                    } else {
                        img.src = 'icons/eye-closed.png';
                        img.alt = 'ปิดการจอง';
                    }
                    this.setAttribute('data-visible', newVisibility);
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
<form id="bulk-delete-form" method="POST" action="bulk_delete.php">
    <button type="submit" class="btn-delete1">ลบที่เลือก</button>
</form>


    <!-- Footer -->
    <footer>
        <p>ProjectVC.2 2024 | E-Booking System</p>
    </footer>

</body>
</html>

<?php $conn->close(); ?>
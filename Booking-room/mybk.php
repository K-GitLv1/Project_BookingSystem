<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
    include 'check_user.php';
    include 'db/db_connection.php';
    
    // หากมีการตั้งค่าข้อความแจ้งเตือนใน session ให้แสดง
if (isset($_SESSION['error_message']) || isset($_SESSION['success_message'])) {
    $message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : $_SESSION['success_message'];
    $type = isset($_SESSION['error_message']) ? 'error' : 'success';
    echo "<script>
            Swal.fire({
                icon: '$type',
                title: '$message',
                showConfirmButton: false,
                timer: 3000
            });
          </script>";

    // ลบข้อความหลังจากแสดงแล้ว
    unset($_SESSION['error_message']);
    unset($_SESSION['success_message']);
}
    

    // ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    // จำนวนรายการที่แสดง
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    $room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
    $status = isset($_GET['status']) ? intval($_GET['status']) : -1;

    // การคำนวณหน้าปัจจุบัน
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * $limit;

    // ดึงข้อมูลรายการจองจากฐานข้อมูล
    $sql = "SELECT b.id, b.participants, b.booker_name, b.phone, 
                   b.start_date, b.end_date, b.start_time, b.end_time, 
                   b.purpose, b.equipment, b.other_details, b.is_confirmed, 
                   r.room_name 
            FROM bookings b 
            JOIN rooms r ON b.room_id = r.id 
            WHERE b.user_id = {$_SESSION['user_id']}";  // เพิ่มเงื่อนไขสำหรับแสดงเฉพาะของผู้ใช้ที่ล็อกอิน

    if ($start_date && $end_date) {
        $sql .= " AND b.start_date BETWEEN '$start_date' AND '$end_date'";
    }
    if ($room_id) {
        $sql .= " AND b.room_id = $room_id";
    }
    if ($status != -1) {
        $sql .= " AND b.is_confirmed = $status";
    }

    // เพิ่ม LIMIT และ OFFSET
    $sql .= " ORDER BY b.start_date DESC LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
    $bookings = $result->fetch_all(MYSQLI_ASSOC);

    // หาจำนวนทั้งหมดของรายการ
    $count_sql = "SELECT COUNT(*) AS total FROM bookings b WHERE b.user_id = {$_SESSION['user_id']}";
    if ($start_date && $end_date) {
        $count_sql .= " AND b.start_date BETWEEN '$start_date' AND '$end_date'";
    }
    if ($room_id) {
        $count_sql .= " AND b.room_id = $room_id";
    }
    if ($status != -1) {
        $count_sql .= " AND b.is_confirmed = $status";
    }
    $count_result = $conn->query($count_sql);
    $total_rows = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_rows / $limit);
    

    
?>



<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการจองของฉัน</title>
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/approvebk.css">
    <script src="js/bk2.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    

</head>
<body>
<!-- หน้า HTML -->
<div class="container">
    <h2>รายการจองของฉัน</h2>
    <div class="filter-options">
        <form method="GET">
            <label>แสดง: 
                <select name="limit">
                    <?php foreach ([100, 50, 40, 30, 20, 10] as $num): ?>
                        <option value="<?= $num ?>" <?= $num == $limit ? 'selected' : '' ?>><?= $num ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>วันที่: <input type="date" name="start_date" value="<?= $start_date ?>"> - <input type="date" name="end_date" value="<?= $end_date ?>"></label>
            <label>ห้อง:
                <select name="room_id">
                    <option value="0">ทั้งหมด</option>
                    <?php $room_res = $conn->query("SELECT * FROM rooms WHERE is_visible = 1");
                    while ($room = $room_res->fetch_assoc()): ?>
                        <option value="<?= $room['id'] ?>" <?= $room['id'] == $room_id ? 'selected' : '' ?>><?= $room['room_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </label>
            <label>สถานะ:
                <select name="status">
                    <option value="-1">ทั้งหมด</option>
                    <option value="0" <?= $status == 0 ? 'selected' : '' ?>>รอตรวจสอบ</option>
                    <option value="1" <?= $status == 1 ? 'selected' : '' ?>>อนุมัติ</option>
                    <option value="2" <?= $status == 2 ? 'selected' : '' ?>>ไม่อนุมัติ</option>
                    <!-- <option value="3" <?= $status == 3 ? 'selected' : '' ?>>ยกเลิกแล้ว</option> -->
                </select>
            </label>
            <button type="submit">ค้นหา</button>
        </form>
    </div>

    <!-- ตารางแสดงการจอง -->
    <table class="room-table">
    <thead>
        <tr>
            <th>ห้อง</th>
            <th>ผู้จอง</th>
            <th>ติดต่อ</th>
            <th>วันที่</th>
            <th>เวลา</th>
            <th>ใช้สำหรับ</th>
            <th>สถานะ</th>
            <th>จัดการ</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($bookings as $booking): ?>
        <tr>
            <td><?= htmlspecialchars($booking['room_name']) ?></td>
            <td><?= htmlspecialchars($booking['booker_name']) ?></td>
            <td><?= htmlspecialchars($booking['phone']) ?></td>
            <td><?= htmlspecialchars($booking['start_date']) ?> - <?= htmlspecialchars($booking['end_date']) ?></td>
            <td><?= htmlspecialchars($booking['start_time']) ?> - <?= htmlspecialchars($booking['end_time']) ?></td>
            <td><?= htmlspecialchars($booking['purpose']) ?></td>
            <td>
                <?php 
                    $statusText = ['รอตรวจสอบ', 'อนุมัติ', 'ไม่อนุมัติ', 'ยกเลิกแล้ว'];
                    echo $statusText[$booking['is_confirmed']] ?? 'ไม่ทราบสถานะ'; 
                ?>
            </td>
            <td>
                <button onclick="openModal(<?= $booking['id'] ?>)">รายละเอียด</button>
                <button onclick="editBooking(<?= $booking['id'] ?>)">แก้ไข</button>  <!-- ปุ่มแก้ไข -->
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    </table>

    <!-- ปุ่มเปลี่ยนหน้า -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>">ก่อนหน้า</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&limit=<?= $limit ?>" <?= $page == $i ? 'style="background-color: #0056b3;"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>">ถัดไป</a>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>รายละเอียดการจอง</h2>
        <div id="bookingDetails"></div>
        <input type="hidden" id="approve_id">
        <input type="hidden" id="reject_id">
        <!-- <button onclick="editBooking()">แก้ไข</button> -->
        <button onclick="cancelBooking()">ยกเลิก</button>
    </div>
</div>

<script>
// ฟังก์ชันเปิด modal และดึงรายละเอียดการจอง
function openModal(id) {
    fetch(`get_mybk_details.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('bookingDetails').innerHTML = `
                <p>ห้อง: ${data.room_name}</p>
                <p>ผู้จอง: ${data.booker_name}</p>
                <p>โทรศัพท์: ${data.phone}</p>
                <p>เริ่มใช้: ${data.start_date} - ${data.start_time}</p>
                <p>ใช้เสร็จ: ${data.end_date} - ${data.end_time}</p>
                <p>ใช้สำหรับ: ${data.purpose}</p>
                <p>รายละเอียด: ${data.other_details}</p>
            `;
            document.getElementById('approve_id').value = data.id;
            document.getElementById('reject_id').value = data.id;
            document.getElementById('modal').style.display = 'block';
        });
}

// ฟังก์ชันปิด modal
function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

// ฟังก์ชันแก้ไขการจอง
function editBooking(bookingId) {
    // เปลี่ยนการ redirect ไปที่หน้าที่ต้องการ
    window.location.href = `edit-mybooking.php?id=${bookingId}`;
}



// ฟังก์ชันยกเลิกการจอง
function cancelBooking() {
    const bookingId = document.getElementById('reject_id').value;

    // ยืนยันการยกเลิกก่อนที่จะลบ
    if (confirm("คุณต้องการยกเลิกการจองนี้จริงหรือไม่?")) {
        fetch(`cancel_booking.php?id=${bookingId}`)
            .then(() => {
                alert("การจองถูกยกเลิกเรียบร้อยแล้ว");
                location.reload();  // รีเฟรชหน้าหลังจากยกเลิก
            })
            .catch(() => {
                alert("เกิดข้อผิดพลาดในการยกเลิกการจอง");
            });
    } else {
        alert("คุณยกเลิกการยกเลิกการจอง");
    }
}
</script>

</body>
</html>

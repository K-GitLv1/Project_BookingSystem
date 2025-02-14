<?php 
    include 'check_login.php';
    // include 'footer.php';
    include 'db/db_connection.php'; // เชื่อมต่อฐานข้อมูล
    date_default_timezone_set('Asia/Bangkok');
    // ดึงข้อมูลห้องประชุมจากฐานข้อมูล
    $sql = "SELECT * FROM rooms WHERE is_visible = 1"; // คำสั่ง SQL ดึงห้องประชุมที่สามารถแสดงได้
    $result = $conn->query($sql); // รันคำสั่ง SQL
    $phone = "";
    if ($result->num_rows > 0) {
        // มีข้อมูลห้องประชุม
        $rooms = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $rooms = [];
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองห้อง</title>
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/bk1.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <script src="js/bk.js"></script>
</head>
<body>
<style>
        /* Calendar container */
.room-table {
    color:  rgb(255, 255, 255);
}
body {
    padding-bottom: 100px; /* เพิ่มช่องว่างที่ท้ายหน้า */
}
/* เพิ่มการตัดบรรทัดใน modal */
#roomDetails p {
    word-wrap: break-word; /* ตัดบรรทัดเมื่อข้อความยาวเกินพื้นที่ */
    white-space: normal;   /* ยอมให้ข้อความข้ามบรรทัดได้ */
}
.container {
    width: 1200px;
    margin: 0 auto;
    background-color: #6f75e6;
    color: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-top: 200px;
}


/* ปรับขนาดของรูปภาพ */
.room-image {
    max-width: 100%;  /* ป้องกันภาพใหญ่เกินไป */
    max-height: 200px; /* จำกัดความสูง */
    object-fit: cover; /* ป้องกันภาพบิดเบี้ยว */
    border-radius: 5px; /* มุมมนเพื่อความสวยงาม */
    display: block; /* ป้องกันช่องว่างเกิน */
    margin: 10px 0;
}
.room-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    table-layout: fixed; /* เพิ่มเพื่อให้ตารางมีความกว้างคงที่ */
}
.room-table th:first-child, .room-table td:first-child {
    width: 80%; /* ปรับความกว้างของคอลัมน์ซ้ายเป็น 70% */
}

.room-table th:last-child, .room-table td:last-child {
    width: 15%; /* ปรับความกว้างของคอลัมน์ขวา */
    text-align: center; /* จัดข้อความในหัวคอลัมน์ให้ตรงกลาง */
    vertical-align: middle; /* จัดเนื้อหาให้อยู่ตรงกลางในแนวตั้ง */
}

.room-table td:last-child {
    text-align: right; /* ปรับให้ปุ่มอยู่ชิดขวา */
}


.room-table th, .room-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
    vertical-align: top; /* ปรับให้อัลไลน์เนื้อหาด้านบน */
    word-wrap: break-word; /* ตัดข้อความเมื่อเกินพื้นที่คอลัมน์ */
}

.room-table th {
    background-color: #6a71d7; /* เพิ่มสีพื้นหลังเพื่อให้ดูโดดเด่น */
    color: #fff;
    text-align: center; /* จัดให้ข้อความในหัวคอลัมน์อยู่ตรงกลาง */
}
.room-table td {
    text-align: left; /* ให้ข้อความในเซลล์คอลัมน์ "รายละเอียดห้อง" เรียงจากซ้าย */
}


.room-info {
    display: flex;
    flex-direction: column;
    max-width: 800px; /* จำกัดความกว้างคอลัมน์ */
    max-height: 5.6em; /* จำกัดความสูงให้ไม่เกิน 6 บรรทัด (1.4em ต่อบรรทัด) */
    overflow: hidden; /* ซ่อนข้อความที่เกิน */
    text-overflow: ellipsis; /* เพิ่ม ... สำหรับข้อความที่ถูกตัด */
    white-space: normal; /* อนุญาตให้ข้อความตัดบรรทัด */
    line-height: 1.4em; /* ระยะห่างระหว่างบรรทัด */
    
}


.room-name {
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 5px;
}

.btn {
    padding: 8px 12px;
    margin-right: 5px;
    border: none;
    border-radius: 5px;
    color: #fff;
    cursor: pointer;
    display: inline-block;
}

.btn-book {
    background-color: #007bff;
}

.btn-detail {
    background-color:rgb(255, 166, 0);
}


.btn:hover {
    opacity: 0.9; /* เพิ่มเอฟเฟกต์ hover */
}

.modal {
    display: none; /* Hide modal by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    overflow: auto;
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
    width: 50%; /* ปรับความกว้าง */
    max-width: 600px; /* กำหนดขนาดสูงสุด */
 /* ป้องกันไม่ให้ modal ยาวเกินหน้าจอ */
    overflow-y: auto; /* เพิ่มแถบเลื่อนถ้าข้อความเยอะ */
}


.close {
    float: right;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: red;
}

#roomDetails {
    font-size: 16px;
    line-height: 1.5;
    max-height: none;
}

    </style>
<div class="container">
    <h2>ห้อง / รายการ</h2>
    
    <table class="room-table">
        <thead>
            <tr>
                <th>รายละเอียดห้อง</th>
                <th>การดำเนินการ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $room): ?>
            <tr>
                <td>
                    <div class="room-info">
                        <span class="room-name"><?php echo htmlspecialchars($room['room_name']); ?></span>
                        <p><?php echo htmlspecialchars($room['description']); ?></p>
                    </div>
                </td>
                <td>
                    <!-- ปุ่มจองห้องที่เช็คว่าเข้าสู่ระบบหรือไม่ -->
                    <button class="btn btn-book" onclick="checkLoginAndOpenBookingModal('<?php echo $room['id']; ?>')">จองห้อง</button>
                    <button class="btn btn-detail" onclick="window.location.href='room_detail.php?id=<?php echo $room['id']; ?>'">รายละเอียด</button>


                </td>
<!-- Modal สำหรับจองห้อง -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeBookingModal()">&times;</span>
        
        <h2>เพิ่ม การจอง</h2>
        <button type="reset" class="btn btn-clear">ล้างฟอร์ม</button>
        <form action="book_room.php" method="POST">
            <label>ชื่อห้อง:</label>
            <select name="room_id" id="room_id" required>
            <?php foreach ($rooms as $room): ?>
                <option value="<?php echo $room['id']; ?>" data-capacity="<?php echo $room['capacity']; ?>"><?php echo htmlspecialchars($room['room_name']); ?></option>
            <?php endforeach; ?>
        </select>
            
            <label>จำนวนผู้เข้าร่วม:</label>
            <input type="number" name="participants" id="participants" required>
            <label>ชื่อผู้จอง:</label>
            <input type="text" name="booker_name" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" required>

            <label>โทรศัพท์:</label>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($_SESSION['phone']); ?>"  required>

            <!-- วันที่เริ่มต้นและเวลาเริ่มต้นในบรรทัดเดียวกัน -->
<div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
    <label>วันที่เริ่มต้น:</label>
    <label>เวลาเริ่มต้น:</label>
</div>
<div style="display: flex; justify-content: space-between; gap: 10px;">
    <input type="date" name="start_date" required>
    <input type="time" name="start_time" required>
</div>

<!-- วันที่สิ้นสุดและเวลาสิ้นสุดในบรรทัดเดียวกัน -->
<div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: 10px;">
    <label>วันที่สิ้นสุด:</label>
    <label>เวลาสิ้นสุด:</label>
</div>
<div style="display: flex; justify-content: space-between; gap: 10px;">
    <input type="date" name="end_date" required>
    <input type="time" name="end_time" required>
</div>


            <label>ใช้สำหรับ:</label>
            <select name="purpose">
                <option value="ประชุม">ประชุม</option>
                <option value="สัมมนา">สัมมนา</option>
                <option value="อบรม">อบรม</option>
            </select>
            <label>อุปกรณ์:</label>
            <div></div>
            <div class="cb">
                
            โปรเจคเตอร์ <input type="checkbox" name="equipment[]" value="โปรเจคเตอร์"> 
            &nbsp;ไมค์<input type="checkbox" name="equipment[]" value="ไมค์"> 
            </div>
            <div >
                
            
            </div>
            <label>อื่นๆ:</label>
            <textarea name="other_details"></textarea>
            <button type="submit" class="btn btn-save">บันทึก</button>
        </form>
    </div>
</div>


            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="roomModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>รายละเอียดห้องประชุม</h2>
        <div id="roomDetails">
            <!-- Room details will be injected here -->
        </div>
    </div>
</div>

<script>
 document.querySelector('form').addEventListener('submit', function(e) {
    var participants = document.getElementById('participants').value;
    var selectedRoom = document.getElementById('room_id').selectedOptions[0];
    var capacity = selectedRoom.getAttribute('data-capacity');

    if (participants > capacity) {
        alert('จำนวนผู้เข้าร่วมเกินความจุของห้อง');
        e.preventDefault(); // หยุดการส่งฟอร์ม
    }
});
   
document.querySelector('.btn-clear').addEventListener('click', function() {
    // ล้างข้อมูลในฟอร์ม
    document.querySelector('form').reset();

});
document.querySelector('#room_id').addEventListener('change', function() {
    var selectedRoom = this.options[this.selectedIndex];
    var capacity = selectedRoom.getAttribute('data-capacity');
    var participantsInput = document.getElementById('participants');
    
    // ตั้งค่าจำนวนผู้เข้าร่วมไม่เกิน capacity ของห้อง
    participantsInput.setAttribute('max', capacity);
});


function openBookingModal(roomId) {
    // เปิด modal สำหรับจองห้อง
    document.getElementById("room_id").value = roomId;
    document.getElementById("bookingModal").style.display = "block";
}

function closeBookingModal() {
    // ปิด modal เมื่อกดปุ่ม x
    document.getElementById("bookingModal").style.display = "none";
}

window.onclick = function(event) {
    let modal = document.getElementById("bookingModal");
    if (event.target === modal) {
        modal.style.display = "none"; // ปิด modal เมื่อคลิกนอก modal
    }
}
// ปรับให้ใส่การแสดงค่าใน modal ได้โดยไม่โหลดหน้าใหม่
function checkLoginAndOpenBookingModal(roomId) {
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
        openBookingModal(roomId); // เปิด modal การจอง
    <?php else: ?>
        alert("กรุณาล็อกอินก่อนจึงจะสามารถจองห้องได้");
        window.location.href = "login.php"; // ไปที่หน้าล็อกอิน
    <?php endif; ?>
}
document.addEventListener("DOMContentLoaded", function () {
    // ฟังก์ชันตั้งค่าวันที่และเวลาเป็นค่าปัจจุบัน
    function setDefaultDateTime() {
        let now = new Date();

        // now.setHours(now.getHours() + 7);

        
        // ตั้งค่าฟอร์แมตให้เป็น YYYY-MM-DD สำหรับ input type="date"
        let dateStr = now.toISOString().split("T")[0];

        // ตั้งค่าฟอร์แมตให้เป็น HH:MM สำหรับ input type="time"
        let timeStr = now.toTimeString().split(" ")[0].slice(0, 5);

        // กำหนดค่าลงใน input
        document.querySelector('input[name="start_date"]').value = dateStr;
        document.querySelector('input[name="end_date"]').value = dateStr;
        document.querySelector('input[name="start_time"]').value = timeStr;
        document.querySelector('input[name="end_time"]').value = timeStr;
    }

    // เรียกใช้งานฟังก์ชันเมื่อเปิด Modal
    document.querySelector(".btn-book").addEventListener("click", setDefaultDateTime);
});

function checkLoginAndOpenBookingModal(roomId) {
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
        // หากผู้ใช้ล็อกอินแล้ว ให้เปิด Modal การจองห้อง
        openBookingModal(roomId);
    <?php else: ?>
        // หากยังไม่ได้ล็อกอิน แจ้งเตือนและนำไปที่หน้า login
        alert("กรุณาล็อกอินก่อนจึงจะสามารถจองห้องได้");
        window.location.href = "login.php"; // เปลี่ยนเป็นหน้า login ของคุณ
    <?php endif; ?>
}
function closeModal() {
    document.getElementById("roomModal").style.display = "none";
}
document.getElementById("detailButton").addEventListener("click", function() {
    var details = document.getElementById("descriptionText");
    if (details.style.display === "none") {
        details.style.display = "block";
    } else {
        details.style.display = "none";
    }
});

// ปิด modal เมื่อกดนอกกรอบ
window.onclick = function(event) {
    let modal = document.getElementById("roomModal");
    if (event.target === modal) {
        closeModal();
    }
};

</script>
</body>
</html>


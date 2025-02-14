<td>
                    <div class="room-info">
                        <span class="room-name"><?php echo htmlspecialchars($room['room_name']); ?></span>
                        <p><?php echo htmlspecialchars($room['description']); ?></p>
                    </div>
                </td>
                <td>
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
document.querySelector('.btn-clear').addEventListener('click', function() {
    // ล้างข้อมูลในฟอร์ม
    document.querySelector('form').reset();

});


function openBookingModal(roomId) {
    document.getElementById("room_id").value = roomId;
    document.getElementById("bookingModal").style.display = "block";
}
function closeBookingModal() {
    document.getElementById("bookingModal").style.display = "none";
}
window.onclick = function(event) {
    let modal = document.getElementById("bookingModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
document.addEventListener("DOMContentLoaded", function () {
    // ฟังก์ชันตั้งค่าวันที่และเวลาเป็นค่าปัจจุบัน
    function setDefaultDateTime() {
        let now = new Date();
        
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
function openModal(name, description, capacity, location,  image) {
    let modal = document.getElementById("roomModal");
    let roomDetails = document.getElementById("roomDetails");

    roomDetails.innerHTML = `
        <p><strong>ชื่อห้อง:</strong> ${name}</p>
        <p><strong>รายละเอียด:</strong> ${description}</p>
        <p><strong>ความจุ:</strong> ${capacity} คน</p>
        <p><strong>สถานที่:</strong> ${location}</p>
        
        <img src="${image}" alt="Room Image" style="max-width: 100%; border-radius: 5px;">
    `;

    modal.style.display = "block";
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
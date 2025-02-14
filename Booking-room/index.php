<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="css/theme.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.4/index.global.js"></script>
    <!-- Bootstrap CSS (เพิ่มใน <head>) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS (เพิ่มก่อนปิด </body>) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
      body {
        padding-bottom: 100px;
        font-family: 'Noto Sans Thai', sans-serif; /* ใช้ฟอนต์ Noto Sans Thai */
      }
      /* Calendar container */
      #calendar {
        margin-top: 180px;
        width: 120%;
        max-width: 1200px;
        height: 1000px;
        margin-left: auto;
        margin-right: auto;
      }
        /* ลบเส้นขีดใต้ในชื่อวัน (เช่น จ., อ., อา.) */
      .fc-day-header {
        text-decoration: none; /* เอาเส้นขีดใต้ */
        color: black; /* ฟอนต์สีดำ */
      }

      /* ลบเส้นขีดใต้ในตัวเลขวัน */
      .fc-day-number {
        text-decoration: none; /* เอาเส้นขีดใต้ */
        color: black; /* ฟอนต์สีดำ */
      }
      /* สีของกิจกรรมในปฏิทิน */
      .fc-event.confirmed {
        background-color: rgb(0, 212, 18); /* สีเขียว */
        border-color: rgb(0, 180, 15);
        color: black; /* ฟอนต์สีดำ */
        text-decoration: none; /* เอาเส้นขีดใต้ */
      }

      .fc-event.unconfirmed {
        background-color: rgb(255, 66, 66); /* สีแดง */
        border-color: rgb(255, 0, 0);
        color: black; /* ฟอนต์สีดำ */
        text-decoration: none; /* เอาเส้นขีดใต้ */
      }

      .fc-event.pending {
        background-color: rgb(255, 181, 21); /* สีเหลือง */
        border-color: rgb(255, 187, 0);
        color: black; /* ฟอนต์สีดำ */
        text-decoration: none; /* เอาเส้นขีดใต้ */
      }

      /* ปรับฟอนต์ใน Modal */
      .modal-title {
        font-weight: bold;
        font-size: 1.25rem;
        color: black; /* ฟอนต์สีดำ */
      }

      /* เอาเส้นขีดใต้ของข้อความออก */
      p, .modal-body p {
        text-decoration: none; /* ลบเส้นขีดใต้ */
        
      }

      /* ปรับการแสดงผลให้ดูดีขึ้นใน Modal */
      .modal-content {
        border-radius: 8px;
      }
      .modal-body p {
        font-size: 1rem;
        line-height: 1.5;
        margin-bottom: 10px;
      }

      .modal-footer {
        display: flex;
        justify-content: flex-end;
      }
    </style>
</head>
<body>
<?php 
    include 'check_login.php'; 
?>

<!-- Calendar Section -->
<div id="calendar"></div>
<!-- Modal สำหรับแสดงรายละเอียดการจอง -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel">รายละเอียดการจองห้องประชุม</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>หัวข้อ:</strong> <span id="modalTitle"></span></p>
        <p><strong>เริ่มเวลา:</strong> <span id="modalStart"></span></p>
        <p><strong>สิ้นสุดเวลา:</strong> <span id="modalEnd"></span></p>
        <p><strong>ผู้จอง:</strong> <span id="modalBookerName"></span></p>
        <p><strong>โทรศัพท์:</strong> <span id="modalPhone"></span></p>
        <p><strong>รายละเอียดเพิ่มเติม:</strong> <span id="modalAdditionalDetails"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    locale: 'th',
    selectable: true,
    businessHours: true,
    dayMaxEvents: true, // allow "more" link when too many events

    events: function(info, successCallback, failureCallback) {
      fetch('events.php')
        .then(response => response.json())
        .then(data => {
          // แปลงสถานะเป็น class และเพิ่มลงใน event
          data.forEach(event => {
            if (event.status === 'confirmed') {
              event.className = 'fc-event confirmed'; // สีเขียว
            } else if (event.status === 'unconfirmed') {
              event.className = 'fc-event unconfirmed'; // สีแดง
            } else if (event.status === 'pending') {
              event.className = 'fc-event pending'; // สีเหลือง
            }
          });
          successCallback(data);
        })
        .catch(error => failureCallback(error)); // ถ้ามีข้อผิดพลาด
    },

    // กดที่อีเวนต์แล้วแสดงรายละเอียดใน Modal
    eventClick: function(info) {
      var title = info.event.title; // หัวข้อ "ประชุม"
      var start = info.event.start.toLocaleString();
      var end = info.event.end.toLocaleString();
      var bookerName = info.event.extendedProps.bookerName; // ชื่อผู้จอง
      var phone = info.event.extendedProps.phone; // เบอร์โทรศัพท์
      var additionalDetails = info.event.extendedProps.additionalDetails; // รายละเอียดเพิ่มเติม

      // กำหนดค่าใน Modal
      document.getElementById('modalTitle').textContent = title;
      document.getElementById('modalStart').textContent = start;
      document.getElementById('modalEnd').textContent = end;
      document.getElementById('modalBookerName').textContent = bookerName;
      document.getElementById('modalPhone').textContent = phone;
      document.getElementById('modalAdditionalDetails').textContent = additionalDetails;

      // แสดง Modal
      var myModal = new bootstrap.Modal(document.getElementById('eventModal'));
      myModal.show();
    }
  });

  calendar.render();
});
</script>

</body>
</html>

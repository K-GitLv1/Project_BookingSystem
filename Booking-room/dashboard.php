<?php
// เชื่อมต่อกับฐานข้อมูล
include 'db/db_connection.php'; 
include 'check_admin.php';

// จำนวนการจองตามวัน (วันนี้)
$query_today = "SELECT COUNT(*) AS bookings_today FROM bookings WHERE DATE(start_date) = CURDATE()";
$result_today = mysqli_query($conn, $query_today);
$bookings_today = ($result_today) ? mysqli_fetch_assoc($result_today)['bookings_today'] : 0;

// จำนวนการจองตามสัปดาห์ (สัปดาห์นี้)
$query_week = "SELECT COUNT(*) AS bookings_week FROM bookings WHERE WEEK(start_date, 1) = WEEK(CURDATE(), 1)";
$result_week = mysqli_query($conn, $query_week);
$bookings_week = ($result_week) ? mysqli_fetch_assoc($result_week)['bookings_week'] : 0;

// จำนวนการจองตามเดือน (เดือนนี้)
$query_month = "SELECT COUNT(*) AS bookings_month FROM bookings WHERE MONTH(start_date) = MONTH(CURDATE())";
$result_month = mysqli_query($conn, $query_month);
$bookings_month = ($result_month) ? mysqli_fetch_assoc($result_month)['bookings_month'] : 0;

// กำหนดค่าเริ่มต้นสำหรับการเลือกช่วงเวลา
$filter = $_POST['filter'] ?? 'week';
$room_filter = $_POST['room_filter'] ?? 'all';

// ฟังก์ชันดึงข้อมูลการจองตามช่วงเวลา
function getBookingsData($conn, $filter, $room_filter) {
    $conditions = [];
    if ($room_filter !== 'all') {
        $conditions[] = "room_id = " . intval($room_filter);
    }
    switch ($filter) {
        case 'day':
            $conditions[] = "DATE(start_date) = CURDATE()";
            break;
        case 'week':
            $conditions[] = "WEEK(start_date, 1) = WEEK(CURDATE(), 1)";
            break;
        case 'month':
            $conditions[] = "MONTH(start_date) = MONTH(CURDATE())";
            break;
        case 'year':
            $conditions[] = "YEAR(start_date) = YEAR(CURDATE())";
            break;
    }

    // แก้ไขให้แน่ใจว่าคอลัมน์ booking_date ถูกตั้งชื่ออย่างถูกต้อง
    $sql = "SELECT DATE(start_date) AS booking_date, COUNT(*) AS bookings_count FROM bookings";
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    $sql .= " GROUP BY booking_date ORDER BY booking_date ASC";

    return mysqli_query($conn, $sql);
}



// ดึงข้อมูลตามช่วงเวลาและห้องที่เลือก
$result = getBookingsData($conn, $filter, $room_filter);
$dates_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    if (isset($row['booking_date'])) {  // ป้องกัน Undefined array key
        $dates_data[] = [
            'booking_date' => $row['booking_date'],
            'bookings_per_day' => $row['bookings_count']
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ด - ระบบจองห้องประชุม</title>
    <link rel="stylesheet" href="css/theme.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { margin: 120px 0 100px; }
        .container { width: 80%; margin: auto; }
        .card,.card2 { background: #fff; padding: 20px; border-radius: 8px; margin: 15px 0; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .row { display: flex; justify-content: space-between; }
        .row .card,.card2 { width: 30%; text-align: center; }
        .today { background: #FFEB3B; border-left: 5px solid #FF9800; }
        .week { background: #FF9800; border-left: 5px solid #FF5722; }
        .month { background: #F44336; border-left: 5px solid #D32F2F; }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        .card,.card2 {
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 98%;
            
            
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card h3,.card2 {
            margin-top: 0;
        }
        .card-header ,.card2-header{
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        canvas {
            max-width: 100%;
        }
        .row {
            display: flex;
            justify-content: space-between;
        }
        .row .card {
            width: 30%;
        }
        .filter-container {
    display: flex;
    align-items: center;
    gap: 15px;
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin: 20px 0;
}

.filter-container label {
    font-weight: bold;
    color: #333;
}

.filter-container select {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-container select:hover {
    border-color: #007BFF;
}

.filter-container select:focus {
    outline: none;
    border-color: #0056b3;
    box-shadow: 0 0 5px rgba(0, 91, 187, 0.5);
}
.filter-container button {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        background-color: #28a745;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .filter-container button:hover {
        background-color: #218838;
    }
    .filter-container input[type="month"] {
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
        width: 200px;
        transition: all 0.3s ease;
    }

    .filter-container input[type="month"]:hover {
        border-color: #007BFF;
    }

    .filter-container input[type="month"]:focus {
        outline: none;
        border-color: #0056b3;
        box-shadow: 0 0 5px rgba(0, 91, 187, 0.5);
    }
    </style>
</head>
<body>
    <div class="container">
        <h1>แดชบอร์ด</h1>
        <div class="row">
            <div class="card today">
                <h3>การจองวันนี้: <?php echo $bookings_today; ?> ครั้ง</h3>
            </div>
            <div class="card week">
                <h3>การจองสัปดาห์นี้: <?php echo $bookings_week; ?> ครั้ง</h3>
            </div>
            <div class="card month">
                <h3>การจองเดือนนี้: <?php echo $bookings_month; ?> ครั้ง</h3>
            </div>
        </div>
        <form method="POST">
        <div>   
        <div class="filter-container">
    <label for="filter">เลือกช่วงเวลา:</label>
    <select name="filter" id="filter" onchange="this.form.submit();">
        <option value="day" <?php echo ($filter == 'day' ? 'selected' : ''); ?>>รายวัน</option>
        <option value="week" <?php echo ($filter == 'week' ? 'selected' : ''); ?>>รายสัปดาห์</option>
        <option value="month" <?php echo ($filter == 'month' ? 'selected' : ''); ?>>รายเดือน</option>
        <option value="year" <?php echo ($filter == 'year' ? 'selected' : ''); ?>>รายปี</option>
    </select>

    <label for="room_filter">เลือกห้อง:</label>
    <select name="room_filter" id="room_filter" onchange="this.form.submit();">
        <option value="all" <?php echo ($room_filter == 'all' ? 'selected' : ''); ?>>ทั้งหมด</option>
        <?php
        $room_query = "SELECT id, room_name FROM rooms";
        $room_result = mysqli_query($conn, $room_query);
        while ($room = mysqli_fetch_assoc($room_result)) {
            echo '<option value="' . $room['id'] . '" ' . ($room_filter == $room['id'] ? 'selected' : '') . '>' . $room['room_name'] . '</option>';
        }
        ?>
    </select>
    </div>
        </form>
        
        
        <div class="card2">
            <h3>กราฟการจองห้องประชุม</h3>
            <canvas id="bookingsChart"></canvas>
        </div>
    </div>
    <script>
    const datesData = <?php echo json_encode($dates_data); ?>;
    const filterType = "<?php echo $filter; ?>";

    let labels = [];
    let data = [];

    if (filterType === "day") {
        labels = Array.from({ length: 24 }, (_, i) => `${i}:00`); // สร้างช่วงเวลา 00:00 - 23:00
        data = labels.map(hour => {
            let found = datesData.find(d => parseInt(d.booking_date) === parseInt(hour));
            return found ? found.bookings_per_day : 0;
        });
    } else if (filterType === "week" || filterType === "month") {
        labels = datesData.map(d => d.booking_date);
        data = datesData.map(d => d.bookings_per_day);
    } else if (filterType === "year") {
        labels = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
        data = labels.map((month, index) => {
            let found = datesData.find(d => parseInt(d.booking_date) === index + 1);
            return found ? found.bookings_per_day : 0;
        });
    }

    new Chart(document.getElementById('bookingsChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'จำนวนการจองห้องประชุม',
                data: data,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: { display: true, text: filterType === "day" ? "เวลา (ชั่วโมง)" : "วันที่" }
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: "จำนวนการจอง" }
                }
            }
        }
    });
</script>
<div class="filter-container">
<form method="POST" action="pdf.php" target="_blank">
    <label for="selected_month">เลือกเดือนสำหรับสรุปยอด:</label>
    <input type="month" name="selected_month" id="selected_month" required>
    <button type="submit">สร้างรายงาน PDF</button>
</form>
</div>
</body>
</html>

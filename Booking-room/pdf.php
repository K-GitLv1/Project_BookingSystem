<?php
require_once __DIR__ . '/vendor/autoload.php';
include 'db/db_connection.php';

// รับค่าเดือนที่เลือกจากฟอร์ม
$selected_month = isset($_POST['selected_month']) ? $_POST['selected_month'] : date('Y-m');

// ตั้งค่าฟอนต์สำหรับ mPDF
$mpdf = new \Mpdf\Mpdf([
    'default_font' => 'sarabun',  // ตั้งค่าฟอนต์ที่รองรับภาษาไทย
    'format' => 'A4',
    'fontdata' => [
        'sarabun' => [
            'R' => 'THSarabunNew.ttf',
            'B' => 'THSarabunNew-Bold.ttf',
            'I' => 'THSarabunNew-Italic.ttf',
            'BI' => 'THSarabunNew-BoldItalic.ttf',
        ]
    ]
]);

$mpdf->SetFont('sarabun', '', 12);

// แปลงเดือนเป็นภาษาไทย
$thai_months = [
    "01" => "มกราคม", "02" => "กุมภาพันธ์", "03" => "มีนาคม",
    "04" => "เมษายน", "05" => "พฤษภาคม", "06" => "มิถุนายน",
    "07" => "กรกฎาคม", "08" => "สิงหาคม", "09" => "กันยายน",
    "10" => "ตุลาคม", "11" => "พฤศจิกายน", "12" => "ธันวาคม"
];

list($year, $month) = explode('-', $selected_month);
$thai_year = $year + 543;
$thai_month = $thai_months[$month];

// ดึงข้อมูลการจองทั้งหมดในเดือนที่เลือก
$query_total = "SELECT COUNT(*) AS total_bookings FROM bookings WHERE MONTH(start_date) = $month AND YEAR(start_date) = $year";
$result_total = mysqli_query($conn, $query_total);
$total_bookings = mysqli_fetch_assoc($result_total)['total_bookings'];

// ดึงข้อมูลห้องที่มีการจองมากที่สุด
$query_most_booked = "SELECT rooms.room_name, COUNT(*) AS bookings_count FROM bookings JOIN rooms ON bookings.room_id = rooms.id WHERE MONTH(start_date) = $month AND YEAR(start_date) = $year GROUP BY rooms.room_name ORDER BY bookings_count DESC LIMIT 1";
$result_most_booked = mysqli_query($conn, $query_most_booked);
$most_booked = mysqli_fetch_assoc($result_most_booked);

// ดึงข้อมูลการจองตามห้อง
$query_by_room = "SELECT rooms.room_name, COUNT(*) AS bookings_count FROM bookings JOIN rooms ON bookings.room_id = rooms.id WHERE MONTH(start_date) = $month AND YEAR(start_date) = $year GROUP BY rooms.room_name";
$result_by_room = mysqli_query($conn, $query_by_room);

// คำนวณการจองเฉลี่ยต่อวัน
$query_avg_day = "SELECT AVG(bookings_count) AS avg_per_day FROM (
    SELECT COUNT(*) AS bookings_count
    FROM bookings
    WHERE MONTH(start_date) = $month AND YEAR(start_date) = $year
    GROUP BY DATE(start_date)
  ) AS daily_bookings";
$result_avg_day = mysqli_query($conn, $query_avg_day);
$avg_per_day = mysqli_fetch_assoc($result_avg_day)['avg_per_day'];

// เริ่มต้น HTML สำหรับ PDF
$html = '
<h2 style="text-align:center;">สรุปยอดการจองห้องประชุม ประจำเดือน ' . $thai_month . ' ' . $thai_year . '</h2>
<p>เดือนนี้มีการจองทั้งหมด: <strong>' . $total_bookings . '</strong> ครั้ง</p>
<p>เฉลี่ยการจองต่อวัน: <strong>' . round($avg_per_day, 2) . '</strong> ครั้ง</p> <!-- เพิ่มข้อมูลเฉลี่ยการจองต่อวัน -->

<h3>ห้องที่มีการจองมากที่สุด:</h3>
<p>' . ($most_booked ? "ห้อง: <strong>{$most_booked['room_name']}</strong> - จำนวนการจอง: <strong>{$most_booked['bookings_count']}</strong> ครั้ง" : "ไม่มีข้อมูล") . '</p>

<h3>รายละเอียดการจองห้อง:</h3>
<ul>';

while ($row = mysqli_fetch_assoc($result_by_room)) {
    $html .= "<li>ห้อง: <strong>{$row['room_name']}</strong> - จำนวนการจอง: <strong>{$row['bookings_count']}</strong> ครั้ง</li>";
}

$html .= '</ul>';

// ส่งข้อมูลไปยัง mPDF
$mpdf->WriteHTML($html);
$mpdf->Output('booking_summary_' . $selected_month . '.pdf', 'I');
?>

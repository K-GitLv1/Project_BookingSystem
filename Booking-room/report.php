<?php
include 'db/db_connection.php';
require_once __DIR__ . '/vendor/autoload.php';

// ฟังก์ชันดึงข้อมูลตามช่วงเวลาที่เลือก
function getBookingData($period) {
    global $conn;
    // ตัวอย่างการดึงข้อมูลตามช่วงเวลา
    switch ($period) {
        case 'this-week':
            $start_date = date('Y-m-d', strtotime('monday this week'));
            $end_date = date('Y-m-d', strtotime('sunday this week'));
            break;
        case 'this-month':
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
            break;
        default:
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
    }

    $sql = "SELECT COUNT(*) as total_bookings FROM bookings WHERE start_date BETWEEN '$start_date' AND '$end_date'";
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();
    return $data['total_bookings'];
}

$report_period = $_GET['report-period'] ?? 'this-month';
$total_bookings = getBookingData($report_period);

// สร้าง PDF
$mpdf = new \Mpdf\Mpdf([
    'default_font' => 'THSarabun',  // ฟอนต์ที่ใช้
    'fontdata' => [
        'THSarabun' => [
            'R' => 'ttfonts/THSarabun.ttf',  // ฟอนต์ปกติ
            'B' => 'ttfonts/THSarabun-Bold.ttf',  // ฟอนต์หนา
        ]
    ]
]);

// เพิ่มข้อมูลรายงานลงใน PDF
$html = "
<h2 style='text-align: center;'>รายงานการจอง</h2>
<p>จำนวนการจองทั้งหมด: $total_bookings</p>
<p>เลือกช่วงเวลา: " . ucfirst(str_replace('-', ' ', $report_period)) . "</p>
<h3>รายละเอียดการจอง</h3>
<table border='1' style='width: 100%;'>
    <thead>
        <tr>
            <th>ห้อง</th>
            <th>วันที่จอง</th>
            <th>เวลาเริ่ม</th>
            <th>เวลาสิ้นสุด</th>
            <th>ผู้จอง</th>
            <th>จำนวนผู้เข้าร่วม</th>
        </tr>
    </thead>
    <tbody>
        <!-- ดึงข้อมูลการจองจากฐานข้อมูล -->
        <tr>
            <td>Room A</td>
            <td>2025-02-06</td>
            <td>09:00</td>
            <td>11:00</td>
            <td>John Doe</td>
            <td>10</td>
        </tr>
        <tr>
            <td>Room B</td>
            <td>2025-02-07</td>
            <td>14:00</td>
            <td>16:00</td>
            <td>Jane Smith</td>
            <td>15</td>
        </tr>
    </tbody>
</table>
";

// เขียน HTML ลงใน PDF
$mpdf->WriteHTML($html);

// ส่ง PDF ไปยังเบราว์เซอร์
$mpdf->Output('booking_report_' . $report_period . '.pdf', 'I');
?>

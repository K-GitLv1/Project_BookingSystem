<?php
$servername = "localhost"; // เซิร์ฟเวอร์ฐานข้อมูล
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล (ปกติใน XAMPP คือ root)
$password = ""; // รหัสผ่าน (ปกติไม่มีรหัสผ่านใน XAMPP)
$dbname = "bkx2"; // ชื่อฐานข้อมูลที่คุณสร้าง

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// เช็คการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}
?>

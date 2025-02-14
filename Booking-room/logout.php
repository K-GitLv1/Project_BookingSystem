

//session_start();
//session_unset();
//session_destroy(); // ล้างข้อมูล session ทั้งหมด

// ตรวจสอบว่ามีการส่งค่า redirect มาหรือไม่
//$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

// เปลี่ยนเส้นทางไปยังหน้าที่ผู้ใช้มาก่อนหน้า
//header("Location: $redirect");
//exit();


<?php
session_start();
session_unset();
session_destroy(); // ล้างข้อมูล session ทั้งหมด

// เปลี่ยนเส้นทางไปยังหน้า index.php เสมอเมื่อออกจากระบบ
header("Location: index.php");
exit();

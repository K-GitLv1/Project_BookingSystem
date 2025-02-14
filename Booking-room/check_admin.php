<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // หากไม่ได้ล็อกอิน
    header('Location: index.php'); // เปลี่ยนไปที่หน้า index หรือหน้า login
    exit();
}

if ($_SESSION['role_id'] != 0) {
    // หาก role ไม่ใช่ 0 (admin)
    header('Location: index.php'); // เปลี่ยนไปที่หน้า index
    exit();
}
// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือยัง
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    include 'ha.php';  // ถ้าเข้าสู่ระบบแล้วให้เชื่อมไป ha.php
} else {
    include 'hb.php';  // ถ้ายังไม่เข้าสู่ระบบให้เชื่อมไป hb.php
}
?>

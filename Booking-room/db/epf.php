<?php
$dsn = 'mysql:host=127.0.0.1;dbname=bkx2;charset=utf8mb4';
$username = 'root'; // เปลี่ยนเป็นชื่อผู้ใช้ของคุณ
$password = ''; // เปลี่ยนเป็นรหัสผ่านของคุณ

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
}
// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือยัง
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    include 'ha.php';  // ถ้าเข้าสู่ระบบแล้วให้เชื่อมไป ha.php
} else {
    include 'hb.php';  // ถ้ายังไม่เข้าสู่ระบบให้เชื่อมไป hb.php
}
?>

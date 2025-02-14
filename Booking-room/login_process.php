<?php
session_start(); // เริ่ม session

// เชื่อมต่อฐานข้อมูล
include 'db/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ตรวจสอบข้อมูลในฐานข้อมูล
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['password'];

        if (password_verify($password, $hashedPassword)) {
            // ถ้ารหัสผ่านถูกต้อง
            $_SESSION['username'] = $username;
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $row['id']; // เซ็ต user_id ลงใน session
            $_SESSION['role_id'] = $row['role_id']; // เซ็ต role_id ลงใน session
            $_SESSION['profile'] = $row['profile']; // เซ็ตโปรไฟล์จากฐานข้อมูล
            $_SESSION['name'] = $row['name'];
            $_SESSION['phone'] = $row['phone'];
            // แสดง alert และเปลี่ยนเส้นทางหลังจาก 1 วินาที
            echo "<script>
                    
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }); // หน่วงเวลา 1 วินาที ก่อนเปลี่ยนเส้นทาง
                  </script>";
            exit();
        } else {
            // รหัสไม่ถูกต้อง
            echo "<script>
                    alert('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
                    window.location.href = 'login.php';
                  </script>";
            exit();
        }
    } else {
        // ไม่พบชื่อผู้ใช้
        echo "<script>
                alert('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
                window.location.href = 'login.php';
              </script>";
        exit();
    }
}
?>

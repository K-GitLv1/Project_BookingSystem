<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/login2.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    
    <script src="js/login.js"></script>
</head>
<body>
<?php 
    include 'check_login.php';
    include 'footer.php';
?>


    <!-- ส่วน login-container -->
    <div class="login">
        <div class="login-header">
            <h1 align ='center'>ลงชื่อเข้าใช้</h1>
        </div>
        <form action="login_process.php" method="post">
            <div class="input-group">
                <label for="username">ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="input-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
        <div class="footer">
            
        </div>
    </div>

</body>
</html>

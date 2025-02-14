<!-- Header Section -->
<div class="header">
    <img src="pic/logo.png" alt="Logo" style="width: 60px; height: 60px; margin-right: 10px;">
    <div>
        <p class="main-title">Booking</p>
        <p class="sub-title">ระบบจองห้องประชุม</p>
    </div>
</div>

<!-- Hamburger menu icon -->
<div class="menu-icon" onclick="openSidebar()">
    <div></div>
    <div></div>
    <div></div>
</div>

<!-- Sidebar menu -->
<div id="sidebar" class="sidebar" align="center">
    <span class="close-btn" onclick="closeSidebar()">&times;</span>

    <a href="index.php">หน้าหลัก</a>
    <a href="bk.php">จองห้อง</a>

    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 0): ?>
        <a href="Approvebk.php">จัดการรายการจอง</a>    
        <a href="dashboard.php">สถิติ</a> 
    <?php endif; ?>
    
    <a href="mybk.php">รายการจองของฉัน</a>
    <a href="about.php">เกี่ยวกับ</a>
    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 0): ?>
    <div class="dropdown">
    
            <a href="#" class="dropdown-toggle">ตั้งค่า 
                <span class="arrow">&#9660;</span> <!-- ลูกศรชี้ลง -->
            </a>
            <div class="dropdown-content">
                <a href="manage_rooms.php">จองห้อง</a>
                <a href="manage_users.php">จัดการผู้ใช้</a>
            </div>
        </div>
        <?php endif; ?>
    <br><br><br><br><br><br><br><br><br><br>

    <?php if (isset($_SESSION['username'])): ?>
    <div class="user-profile" style="margin-bottom: 20px;">
        <!-- แสดงรูปโปรไฟล์ -->
        <a href="edit_user.php?id=<?php echo $_SESSION['user_id']; ?>"> <!-- ลิงก์ไปที่หน้าแก้ไขโปรไฟล์ของตัวเอง -->
            <img src="<?php echo !empty($_SESSION['profile']) ? $_SESSION['profile'] : 'pic/defpro.jpg'; ?>" 
                 alt="User Profile" 
                 style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
        </a>
        <p><?php echo $_SESSION['name']; ?></p>
        <!-- ปุ่มสำหรับไปแก้ไขโปรไฟล์ -->
        
    </div>
    <?php endif; ?>

    <br>
    <a href="logout.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">ออกจากระบบ</a> <!-- ลิงก์ออกจากระบบพร้อม redirect -->
</div>
<script src="js/hgtab.js"></script>

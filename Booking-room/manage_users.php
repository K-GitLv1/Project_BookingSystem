<?php
include 'check_admin.php';
include 'db/db_connection.php';

// ตรวจสอบว่าเป็นผู้ใช้ประเภทไหนก่อน
if (isset($_SESSION['role_id']) && $_SESSION['role_id'] != 0) {
    // ถ้าเป็นผู้ใช้ที่ไม่ใช่แอดมิน (role_id != 0) ให้รีไดเร็กต์ไปยังหน้า index
    header("Location: index.php");
    exit();
}

// ดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
$sql = "SELECT id, name, username, profile, phone, role_id FROM users";
$result = $conn->query($sql);

// เพิ่มผู้ใช้ใหม่
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_user"])) {
    // ตรวจสอบค่าที่ส่งมาจากฟอร์ม
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $role_id = isset($_POST["role_id"]) ? $_POST["role_id"] : 1;

    // ตรวจสอบว่า username ไม่ว่าง
    if (empty($username)) {
        echo "<script>alert('กรุณากรอกชื่อผู้ใช้');</script>";
    } else {
        // ตรวจสอบชื่อผู้ใช้ว่าไม่มีในฐานข้อมูล
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            echo "<script>alert('ชื่อผู้ใช้ซ้ำ กรุณาใช้ชื่อผู้ใช้ใหม่');</script>";
            $stmt->close();
        } else {
            // ถ้าชื่อผู้ใช้ไม่ซ้ำ ให้ทำการเพิ่มผู้ใช้ใหม่
            $password = password_hash($password, PASSWORD_DEFAULT);

            // อัปโหลดรูปโปรไฟล์
            $profile = NULL;
            if (!empty($_FILES["profile"]["name"]) && $_FILES["profile"]["error"] == 0) {
                $target_dir = "uploads/";
                $file_name = basename($_FILES["profile"]["name"]);
                $profile = $target_dir . $file_name;

                $imageFileType = strtolower(pathinfo($profile, PATHINFO_EXTENSION));
                $allowedTypes = ["jpg", "jpeg", "png"];

                if (in_array($imageFileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES["profile"]["tmp_name"], $profile)) {
                        // อัปโหลดสำเร็จ
                    } else {
                        $profile = NULL;
                    }
                } else {
                    echo "<script>alert('ไฟล์ต้องเป็น JPG, JPEG หรือ PNG เท่านั้น');</script>";
                    $profile = NULL;
                }
            }

            // เพิ่มข้อมูลผู้ใช้ใหม่
            $stmt = $conn->prepare("INSERT INTO users (name, username, password, profile, role_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $name, $username, $password, $profile, $role_id);

            if ($stmt->execute()) {
                echo "<script>alert('เพิ่มผู้ใช้สำเร็จ'); window.location='manage_users.php';</script>";
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
// ปิด connection หลังจากใช้งานเสร็จ
$conn->close();
?>




<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้</title>
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/mu.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
</head>
<body>
    <style>
        
    </style>
    <div class="container">
        <h2>จัดการผู้ใช้</h2>

        


<!-- Modal Form -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span> <!-- ปุ่มปิด -->
        <h3>เพิ่มผู้ใช้ใหม่</h3>
        <form method="POST" enctype="multipart/form-data" onsubmit="return validatePasswords()">
    <input type="text" name="name" placeholder="ชื่อ" required>
    <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
    <input type="password" name="password" id="password" placeholder="รหัสผ่าน" required>
    <input type="password" name="confirm_password" id="confirm_password" placeholder="ยืนยันรหัสผ่าน" required>
    <input type="tel" name="phone" placeholder="หมายเลขโทรศัพท์" required>
    
    <!-- <label for="profile-input">อัปโหลดรูปโปรไฟล์</label> -->
    <input type="file" name="profile" id="profile-input" accept="image/*">
    <!-- <img id="profile-preview" src="#" alt="Profile Preview"> -->
    
    <select name="role_id">
        <option value="1">ผู้ใช้</option>
        <option value="0">แอดมิน</option>
    </select>
    <button type="submit" name="add_user">เพิ่มผู้ใช้</button>
</form>


    </div>
</div>


<table>
    <tr>
        
        <th>ชื่อ</th>
        <th>ชื่อผู้ใช้</th>
        <th>รูปโปรไฟล์</th>
        <th>โทรศัพท์</th>
        <th>สิทธิ์</th>
        <th>การจัดการ</th> <!-- เพิ่มคอลัมน์ "การจัดการ" -->
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        
        <td><?php echo $row["name"]; ?></td>
        <td><?php echo $row["username"]; ?></td>
        <td>
            <?php if ($row["profile"]) { ?>
                <img src="<?php echo $row["profile"]; ?>" width="50">
            <?php } else { echo "ไม่มีภาพ"; } ?>
        </td>
        <td><?php echo $row["phone"]; ?></td>
        <td><?php echo $row["role_id"] == 0 ? "แอดมิน" : "ผู้ใช้"; ?></td>
        <td>
            <!-- ปุ่มแก้ไข -->
            <button class="btn btn-edit" onclick="window.location='edit_user.php?id=<?php echo $row["id"]; ?>'">แก้ไข</button>
            <!-- ปุ่มลบ -->
            <button class="btn btn-delete" onclick="if(confirm('คุณแน่ใจที่จะลบผู้ใช้นี้?')) window.location='delete_user.php?id=<?php echo $row["id"]; ?>'">ลบ</button>
        </td>
    </tr>
    <?php } ?>
</table>

    </div>
<!-- ปุ่มกดเปิด Modal -->
<button class="btn btn-success" onclick="openModal()">เพิ่มผู้ใช้</button>
    <script>
function openModal() {
        document.getElementById("userModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("userModal").style.display = "none";
    }
    
    // กดปุ่ม ESC เพื่อปิด Modal
    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape") {
            closeModal();
        }
    });

    // กดนอก Modal เพื่อปิด
    window.onclick = function(event) {
        let modal = document.getElementById("userModal");
        if (event.target === modal) {
            closeModal();
        }
    };
    function validatePasswords() {
    // รับค่ารหัสผ่านและรหัสผ่านยืนยัน
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;

    // ตรวจสอบว่าทั้งสองรหัสผ่านตรงกันหรือไม่
    if (password !== confirmPassword) {
        alert("รหัสผ่านไม่ตรงกัน กรุณากรอกใหม่");
        return false; // หยุดการส่งฟอร์ม
    }
    return true; // ส่งฟอร์มได้
}
document.getElementById("profile-input").addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById("profile-preview");
            preview.src = e.target.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(file);
    }
});
    </script>
</body>
</html>

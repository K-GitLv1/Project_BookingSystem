<?php
include 'check_user.php';
include 'db/db_connection.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าได้รับ ID ของผู้ใช้จาก URL หรือไม่
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // ดึงข้อมูลของผู้ใช้จากฐานข้อมูล
    $stmt = $conn->prepare("SELECT id, name, username, phone, password, profile FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "ไม่พบข้อมูลผู้ใช้";
        exit;
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $phone = $_POST["phone"]; // รับค่าโทรศัพท์
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    $profile = $user["profile"]; // ใช้ค่าโปรไฟล์เดิมก่อน

    // ถ้าช่องโทรศัพท์ว่าง ให้ตั้งค่าเป็น NULL
    $phone = !empty($phone) ? $phone : NULL;

    // ตรวจสอบว่ามีการเปลี่ยนรหัสผ่านหรือไม่
    if (!empty($new_password) && !empty($confirm_password)) {
        if ($new_password == $confirm_password) {
            $password = password_hash($new_password, PASSWORD_DEFAULT);
        } else {
            echo "รหัสผ่านใหม่ไม่ตรงกัน";
            exit;
        }
    } else {
        // ถ้าไม่มีการเปลี่ยนรหัสผ่าน ให้ใช้รหัสผ่านเดิม
        $password = $user["password"];
    }

    // อัปโหลดรูปโปรไฟล์หากมีการอัพโหลดใหม่
    if (isset($_FILES["profile"]) && $_FILES["profile"]["error"] == 0) {
        if ($_FILES["profile"]["size"] > 2097152) { // จำกัดขนาดที่ 2MB
            echo "<script>alert('ขนาดไฟล์ใหญ่เกินไป (ไม่เกิน 2MB)');</script>";
            exit;
        }
        $target_dir = "uploads/";
        $profile = $target_dir . basename($_FILES["profile"]["name"]);
        move_uploaded_file($_FILES["profile"]["tmp_name"], $profile);
    }

    // อัปเดตข้อมูลผู้ใช้ (ไม่อัปเดตชื่อผู้ใช้)
    $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, password = ?, profile = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $phone, $password, $profile, $user_id);
    
    if ($stmt->execute()) {
        header("Location: manage_users.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลผู้ใช้</title>
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/ep.css">
</head>
<body>
    <div class="container">
        <h2>แก้ไขข้อมูลผู้ใช้</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="profile-container">
                <div class="form-group">
                    <input type="file" id="profile" name="profile" accept="image/*" style="display: none;">
                    <div class="empty-profile" onclick="document.getElementById('profile').click();">
                        <?php if ($user['profile']) { ?>
                            <img src="<?php echo $user['profile']; ?>" alt="Profile" id="profilePreview">
                        <?php } else { ?>
                            <i class="fas fa-plus"></i>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- ฟอร์มข้อมูลแบบ 2 คอลัมน์ -->
            <div class="form-row">
                <div class="form-group">
                    <label for="name">ชื่อ</label>
                    <input type="text"placeholder="ชื่อ" name="name" value="<?php echo $user['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">โทรศัพท์</label>
                    <input type="tel"placeholder="หมายเลขโทรศัพท์" name="phone" value="<?php echo $user['phone']; ?>" >
                </div>
            </div>

            <div class="form-group">
    <label for="old_password">รหัสผ่านเดิม</label>
    <input placeholder="รหัสผ่านเดิม"type="password" name="old_password" required>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="new_password">รหัสผ่านใหม่</label>
        <input type="password" placeholder="รหัสผ่านใหม่" name="new_password">
    </div>
    <div class="form-group">
        <label for="confirm_password">ยืนยันรหัสผ่านใหม่</label>
        <input type="password" placeholder="ยืนยันรหัสผ่านใหม่" name="confirm_password">
    </div>
</div>


            <!-- ปุ่มกด -->
            <div class="form-group">
                <button type="submit">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('profile').addEventListener('change', function(event) {
            var file = event.target.files[0]; 
            var reader = new FileReader(); 

            reader.onload = function(e) {
                var img = document.getElementById('profilePreview'); 
                if (!img) {
                    var newImg = document.createElement('img');
                    newImg.id = 'profilePreview';
                    newImg.src = e.target.result;
                    newImg.style.width = '150px';
                    newImg.style.height = '150px';
                    newImg.style.borderRadius = '50%';
                    newImg.style.objectFit = 'cover';
                    document.querySelector('.empty-profile').innerHTML = '';
                    document.querySelector('.empty-profile').appendChild(newImg);
                } else {
                    img.src = e.target.result;
                }
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const oldPasswordInput = document.querySelector("input[name='old_password']");
            const newPasswordInput = document.querySelector("input[name='new_password']");
            const confirmPasswordInput = document.querySelector("input[name='confirm_password']");
            const submitButton = document.querySelector("button[type='submit']");

            function validateForm() {
                if (newPasswordInput.value.trim() !== "" || confirmPasswordInput.value.trim() !== "") {
                    // ถ้าใส่รหัสผ่านใหม่ หรือ ยืนยันรหัสผ่าน ต้องบังคับใส่รหัสผ่านเดิม
                    oldPasswordInput.required = true;
                    confirmPasswordInput.required = true;
                } else {
                    // ถ้าไม่ได้เปลี่ยนรหัสผ่าน ไม่ต้องบังคับ
                    oldPasswordInput.required = false;
                    confirmPasswordInput.required = false;
                }
            }

            newPasswordInput.addEventListener("input", validateForm);
            confirmPasswordInput.addEventListener("input", validateForm);

            validateForm(); // เรียกใช้ตอนโหลดหน้าเว็บเพื่อเซ็ตค่าเริ่มต้น
        });
        document.querySelectorAll("input").forEach(input => {
    input.addEventListener("focus", function () {
        this.style.transform = "scale(1.05)";
    });

    input.addEventListener("blur", function () {
        this.style.transform = "scale(1)";
    });
});

    </script>

</body>
</html>

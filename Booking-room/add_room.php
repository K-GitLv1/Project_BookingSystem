<?php  
// เชื่อมต่อฐานข้อมูล
include 'db/db_connection.php';
include 'check_admin.php';

// ถ้าฟอร์มถูกส่ง
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = isset($_POST['room_name']) ? $_POST['room_name'] : null;

    // ตรวจสอบว่ากรอกชื่อห้องแล้วหรือยัง
    if (empty($room_name)) {
        echo "กรุณากรอกชื่อห้อง.";
        exit;
    }

    $description = isset($_POST['description']) ? $_POST['description'] : null;
    $location = isset($_POST['location']) ? $_POST['location'] : null;
    // $room_number = isset($_POST['room_number']) ? $_POST['room_number'] : null;
    $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : null;

    // อัปโหลดไฟล์
    $target_dir = "uploads/";
    $image_name = !empty($_FILES["image"]["name"]) ? basename($_FILES["image"]["name"]) : null;
    $target_file = $image_name ? $target_dir . time() . "_" . $image_name : null;

    if ($image_name) {
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // ตรวจสอบไฟล์ว่าเป็นรูปภาพหรือไม่
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false && in_array($imageFileType, ['jpg', 'jpeg', 'png', 'webp'])) {
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์.";
                $target_file = null;
            }
        } else {
            echo "ไฟล์ที่อัปโหลดไม่ถูกต้อง.";
            $target_file = null;
        }
    }

    // เพิ่มข้อมูลในฐานข้อมูล
    $sql = "INSERT INTO rooms (room_name, description, location, /*room_number,*/ capacity, image, is_visible, created_at) 
            VALUES (?, ?, ?, /*?,*/ ?, ?, 1, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssds",
        $room_name,
        $description,
        $location,
        // $room_number,
        $capacity,
        $target_file
    );

    if ($stmt->execute()) {
        // รีไดเรกไปหน้า manage_rooms.php เมื่อบันทึกสำเร็จ
        header("Location: manage_rooms.php?status=success");
        exit(); // หยุดการทำงานหลังจากรีไดเรก
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มห้องประชุม</title>
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/addr.css"> <!-- ใช้ addr.css -->
    <link rel="stylesheet" href="css/bk.css">
    <script src="js/bk.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>
<?php 
    // include 'footer.php';
?>

<?php if (isset($_GET['status'])): ?>
    <script>
        window.onload = function() {
            showToast('<?php echo $_GET['status']; ?>');
        }

        function showToast(status) {
            var toast = document.createElement('div');
            toast.classList.add('toast');
            toast.innerHTML = (status === 'success') ? 'เพิ่มห้องประชุมเรียบร้อย!' : 'เกิดข้อผิดพลาดในการเพิ่มห้องประชุม!';
            document.body.appendChild(toast);

            // หายไปหลังจาก 3 วินาที
            setTimeout(function() {
                toast.classList.add('fade-out');
                setTimeout(function() {
                    toast.remove();
                }, 500); // รอให้ fade-out เสร็จก่อนจะลบ
            }, 3000); // แสดงข้อความ 3 วินาที
        }
    </script>
<?php endif; ?>

<style>
/* Style สำหรับ toast */
.toast {
    position: fixed;
    top: 50%; /* ตั้งตำแหน่งในแนวตั้ง */
    left: 50%; /* ตั้งตำแหน่งในแนวนอน */
    transform: translate(-50%, -50%); /* ปรับให้มันอยู่ตรงกลางจอ */
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    opacity: 1;
    z-index: 9999;
    transition: opacity 0.5s ease;
}

/* การทำให้ toast หายไป */
.toast.fade-out {
    opacity: 0;
}

body {
    padding-bottom: 100px; /* เพิ่มช่องว่างที่ท้ายหน้า */
}
#imageFileName {
    color: black; /* เปลี่ยนสีเป็นสีดำ */
}

</style>

<div class="container">
    <h2>เพิ่มห้องประชุม</h2>
    <form action="" method="POST" enctype="multipart/form-data">
    <label for="room_name">ชื่อห้อง:</label>
    <input type="text" id="room_name" name="room_name" required><br><br>

    <label for="description">รายละเอียด:</label><br>
    <textarea id="description" name="description" rows="4"></textarea><br><br>

    <label for="location">อาคาร/สถานที่:</label>
    <input type="text" id="location" name="location"><br><br>

    <!-- <label for="room_number">เลขที่ห้อง:</label>
    <input type="text" id="room_number" name="room_number"><br><br> -->

    <label for="capacity">จำนวนที่นั่ง:</label>
    <input type="number" id="capacity" name="capacity"><br><br>

    <label for="image">รูปภาพ:</label>
    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" onchange="previewImage();"><br><br>

        <!-- Display the selected image preview -->
        <div id="imagePreviewContainer" style="display: none;">
            <img id="imagePreview" src="" alt="Image preview" style="max-width: 200px; margin-top: 10px;">
            <p id="imageFileName"></p>
        </div>

        <button type="submit">บันทึก</button>
    </form>
</div>

<script>
// JavaScript function to preview the selected image and show the filename
function previewImage() {
    const fileInput = document.getElementById("image");
    const file = fileInput.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        const preview = document.getElementById("imagePreview");
        const fileName = document.getElementById("imageFileName");
        const imagePreviewContainer = document.getElementById("imagePreviewContainer");

        preview.src = e.target.result;
        fileName.textContent = file.name;

        imagePreviewContainer.style.display = "block"; // Show the preview container
    };

    if (file) {
        reader.readAsDataURL(file); // Read the file as data URL to display image preview
    }
}
</script>

</body>
</html> 
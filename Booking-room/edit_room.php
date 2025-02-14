<?php
// เชื่อมต่อฐานข้อมูล
include 'db/db_connection.php';
include 'check_admin.php';
// ตรวจสอบว่าได้รับห้องประชุมที่ต้องการแก้ไขจาก URL หรือไม่
if (isset($_GET['id'])) {
    $room_id = $_GET['id'];

    // ดึงข้อมูลห้องประชุมที่ต้องการแก้ไข
    $sql = "SELECT * FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();

    if (!$room) {
        echo "ไม่พบข้อมูลห้องประชุม.";
        exit;
    }

    // ถ้าฟอร์มถูกส่ง
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $room_name = isset($_POST['room_name']) ? $_POST['room_name'] : null;
        $description = isset($_POST['description']) ? $_POST['description'] : null;
        $location = isset($_POST['location']) ? $_POST['location'] : null;
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
        } else {
            // หากไม่มีการอัปโหลดไฟล์ ให้ใช้รูปเดิมจากฐานข้อมูล
            $target_file = $room['image'];
        }

        // อัปเดตข้อมูลห้องประชุมในฐานข้อมูล
        $sql = "UPDATE rooms SET room_name = ?, description = ?, location = ?, capacity = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssdsi",
            $room_name,
            $description,
            $location,
            $capacity,
            $target_file,
            $room_id
        );

        if ($stmt->execute()) {
            // รีไดเรกต์ไปที่หน้าเมเนจ
            header("Location: manage_rooms.php?status=success");
        } else {
            echo "เกิดข้อผิดพลาด: " . $conn->error;
        }

        $stmt->close();
    }
} else {
    echo "ไม่พบห้องประชุม.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขห้องประชุม</title>
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/addr.css">
    <link rel="stylesheet" href="css/bk.css">
    <script src="js/bk.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>

<?php if (isset($_GET['status'])): ?>
    <script>
        window.onload = function() {
            showToast('<?php echo $_GET['status']; ?>');
        }

        function showToast(status) {
            var toast = document.createElement('div');
            toast.classList.add('toast');
            toast.innerHTML = (status === 'success') ? 'แก้ไขห้องประชุมเรียบร้อย!' : 'เกิดข้อผิดพลาดในการแก้ไขห้องประชุม!';
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
    <h2>แก้ไขห้องประชุม</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="room_name">ชื่อห้อง:</label>
        <input type="text" id="room_name" name="room_name" value="<?php echo htmlspecialchars($room['room_name']); ?>" required><br><br>

        <label for="description">รายละเอียด:</label><br>
        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($room['description']); ?></textarea><br><br>

        <label for="location">อาคาร/สถานที่:</label>
        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($room['location']); ?>"><br><br>


        <label for="capacity">จำนวนที่นั่ง:</label>
        <input type="number" id="capacity" name="capacity" value="<?php echo htmlspecialchars($room['capacity']); ?>"><br><br>

        <label for="image">รูปภาพ:</label>
        <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" onchange="previewImage();"><br><br>

        <!-- ถ้าห้องประชุมมีรูปภาพแสดงอยู่ -->
        <?php if ($room['image']): ?>
            <div id="imagePreviewContainer" style="display: block;">
                <img id="imagePreview" src="<?php echo htmlspecialchars($room['image']); ?>" alt="Image preview" style="max-width: 200px; margin-top: 10px;">
                <p id="imageFileName"><?php echo basename($room['image']); ?></p>
            </div>
        <?php endif; ?>

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

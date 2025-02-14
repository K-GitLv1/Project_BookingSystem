<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกี่ยวกับเรา</title>
    <link rel="stylesheet" href="css/theme.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            padding: 50px;
            font-family: 'Noto Sans Thai', sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 810px;
            margin-top: 150px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 18px;
            color: #666;
            line-height: 1.6;
        }
        h2 {
            color: #333;
        }
        .social-links {
            margin-top: 20px;
        }
        .social-button {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 18px;
            margin: 5px;
            color: white;
        }
        .facebook-button {
            background-color: #1877F2;
        }
        .github-button {
            background-color: #333;
        }
        .social-button i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <?php include 'check_login.php'; ?>
    <div class="container">
        <h2>เกี่ยวกับเรา</h2>
        <p>เว็บไซต์นี้เป็นส่วนหนึ่งของ Project จบ วิทยาลัยเทคนิคอุดรธานี แผนกเทคโนโลยีสารสนเทศ</p>
        <p>ร่วมพัฒนาโดย นาย จิรพัฒน์ หนูราช 66309010034 และนาย วัทธิกร พรหมสาขา ณ สกลนคร 66309010036</p>
        <p>โครงงานนี้จัดทำเพื่อความสะดวกในการจองห้องประชุม
            ผู้จัดทำหวังเป็นอย่างยิ่งว่าเว็บไซต์นี้จะเป็นประโยชน์ต่อผู้ใช้งาน</p>

        <div class="social-links">
            <a href="https://www.facebook.com/profile.php?id=100016109047978" target="_blank" class="social-button facebook-button">
                <i class="fab fa-facebook-f"></i> ติดต่อเรา<br>Facebook
            </a>
            <a href="https://github.com/K-GitLv1" target="_blank" class="social-button github-button">
                <i class="fab fa-github"></i> ติดตามผลงาน<br>GitHub
            </a>
        </div>
    </div>
</body>
</html>

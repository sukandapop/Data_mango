<?php
require_once '../admin/db.php';

if (!isset($_GET['name'])) {
    // แสดงข้อความแจ้งเตือน หรือจะ echo อะไรก็ได้
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>กรุณาเลือกสายพันธุ์มะม่วงจากหน้าหลัก</h2>";
    exit;
}

$name = $_GET['name'];

// ดึงข้อมูลมะม่วงจากฐานข้อมูล (ใช้ฟิลด์ตามโครงสร้างใหม่)
$stmt = $conn->prepare("SELECT * FROM mangoes WHERE name_sci = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
$mango = $result->fetch_assoc();

if (!$mango) {
    echo "ไม่พบข้อมูลสายพันธุ์";
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($mango['name_sci']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --Primary: #4e73df;
            --Success: #1cc88a;
            --Info: #36b9cc;
            --Warning: #f6c23e;
            --Danger:rgb(246, 49, 31);
            --Secondary: #858796;
            --Light: #f8f9fc;
            --Dark: #5a5c69;
            --Darkss: #000000;
        }
        .col-6 img {
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .col-6:hover img {
            transform: scale(1.1);
        }
        .row h2, .row h4, .container h4 {
            font-weight: 600;
        }
        p strong {
            font-weight: 400;
        }
    </style>
</head>
<body>

<br>
<div class="container py-5 mt-5">
    <div class="row">
        <!-- คอลัมน์ซ้าย -->
        <div class="col-md-4">
            <h2 class="mb-4"><?= htmlspecialchars($mango['name_sci']) ?></h2>
            <img src="../uploads/<?= htmlspecialchars(basename($mango['header_img'])) ?>" class="img-fluid mb-3" alt="<?= htmlspecialchars($mango['name_sci']) ?>" style="max-height: 400px;">
        </div>
        <!-- คอลัมน์กลาง -->
        <div class="col-md-4">
            <h4>ข้อมูลทั่วไป</h4>
            <p><strong>ชื่อวิทยาศาสตร์:</strong> <?= htmlspecialchars($mango['name_sci'] ?? '-') ?></p>
            <p><strong>ชื่อท้องถิ่น:</strong> <?= !empty($mango['name_local']) ? htmlspecialchars($mango['name_local']) : '-' ?></p>
            <h4>การเพาะปลูก</h4>
            <p><strong>การขยายพันธุ์:</strong> <?= isset($mango['propagation']) ? htmlspecialchars($mango['propagation']) : '-' ?></p>
            <p><strong>ลักษณะดิน:</strong> <?= isset($mango['soil']) ? htmlspecialchars($mango['soil']) : '-' ?></p>
            <p><strong>ระยะเวลาเพาะปลูก:</strong> <?= isset($mango['planting_period']) ? htmlspecialchars($mango['planting_period']) : '-' ?></p>
        </div>
        <!-- คอลัมน์ขวา -->
        <div class="col-md-4">
            <h4>การแปรรูป</h4>
            <p><?= isset($mango['processing']) ? nl2br(htmlspecialchars($mango['processing'])) : '-' ?></p>
            <h4 class="mt-4">หมวดหมู่มะม่วง</h4>
            <p><strong>ประเภท:</strong> <?= isset($mango['category']) ? htmlspecialchars($mango['category']) : '-' ?></p>
        </div>
    </div>

    <h4 class="mt-5">รูปภาพ</h4>
    <div class="row text-center">
        <div class="col-6 col-md-3 mb-3">
            <h6>ผล</h6>
            <img src="../uploads/<?= htmlspecialchars(basename($mango['morph_fruit'])) ?>" class="img-fluid mb-3" alt="ผลมะม่วง" style="object-fit: cover; width: 100%; height: 200px;">
        </div>
        <div class="col-6 col-md-3 mb-3">
            <h6>ต้น</h6>
            <img src="../uploads/<?= htmlspecialchars(basename($mango['morph_tree'])) ?>" class="img-fluid mb-3" alt="ต้นมะม่วง" style="object-fit: cover; width: 100%; height: 200px;">
        </div>
        <div class="col-6 col-md-3 mb-3">
            <h6>ใบ</h6>
            <img src="../uploads/<?= htmlspecialchars(basename($mango['morph_leaf'])) ?>" class="img-fluid mb-3" alt="ใบมะม่วง" style="object-fit: cover; width: 100%; height: 200px;">
        </div>
        <div class="col-6 col-md-3 mb-3">
            <h6>กิ่ง</h6>
            <img src="../uploads/<?= htmlspecialchars(basename($mango['morph_branch'])) ?>" class="img-fluid mb-3" alt="กิ่งมะม่วง" style="object-fit: cover; width: 100%; height: 200px;">
        </div>
        <div class="col-6 col-md-3 mb-3">
            <h6>ดอก</h6>
            <img src="../uploads/<?= htmlspecialchars(basename($mango['morph_flower'])) ?>" class="img-fluid mb-3" alt="ดอกมะม่วง" style="object-fit: cover; width: 100%; height: 200px;">
        </div>
    </div>
    <a href="index.php" class="btn btn-secondary mt-4">← กลับหน้ารวม</a>
</div>

</body>
</html>
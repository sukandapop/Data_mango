<?php
require_once '../admin/db.php';

if (!isset($_GET['name'])) {
    header('Location: mango_detail.php');
    exit;
}

$name = $_GET['name'];

// ดึงข้อมูลมะม่วงจากฐานข้อมูล
$stmt = $conn->prepare("SELECT * FROM mangoes WHERE mango_name = ?");
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
    <title><?= htmlspecialchars($mango['mango_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Kanit", sans-serif;
            background-color: #fff;
            margin: 20px;
            padding: 0;
        }
        .row h2, .row h4, .container h4 {
            font-weight: 600;
        }
        p strong {
            font-weight: 400;
        }
        .col-6 img {
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .col-6:hover img {
            transform: scale(1.1);
        }
        /* ลักษณะสัณฐานวิทยา */
        .morphology-section {
            max-width: 50vw;
            margin: 40px auto 0 auto;
        }
        .morphology-columns {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
        }
        .morphology-column {
            flex: 1;
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 10px;
        }
        .morphology-column h3 {
            margin-top: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        @media (max-width: 900px) {
            .morphology-section {
                max-width: 100vw;
            }
            .morphology-columns {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<br>
<div class="container py-5 mt-5">
    <div class="row">
        <!-- คอลัมน์ซ้าย -->
        <div class="col-md-4">
            <h2 class="mb-4"><?= htmlspecialchars($mango['name_sci']) ?></h2>
            <img src="../admin/uploads/<?= htmlspecialchars(basename($mango['header_img'])) ?>" class="img-fluid mb-3" alt="<?= htmlspecialchars($mango['name_sci']) ?>" style="max-height: 400px;">
        </div>
        <!-- คอลัมน์กลาง -->
        <div class="col-md-4">
            <h4>ข้อมูลทั่วไป</h4>
            <p><strong>ชื่อวิทยาศาสตร์:</strong> <?= htmlspecialchars($mango['name_sci']) ?></p>
            <p><strong>ชื่อท้องถิ่น:</strong> <?= !empty($mango['name_local']) ? htmlspecialchars($mango['name_local']) : '-' ?></p>
            <h4 class="mt-4">ลักษณะสัณฐานวิทยา</h4>
            <p><strong>ลำต้น:</strong> <?= htmlspecialchars($mango['morph_tree']) ?></p>
            <p><strong>ผล:</strong> <?= htmlspecialchars($mango['morph_fruit']) ?></p>
            <p><strong>ใบ:</strong> <?= htmlspecialchars($mango['morph_leaf']) ?></p>
            <h4>การเพาะปลูก</h4>
            <p><strong>การขยายพันธุ์:</strong> <?= htmlspecialchars($mango['propagation']) ?></p>
            <p><strong>ลักษณะดิน:</strong> <?= htmlspecialchars($mango['soil']) ?></p>
            <p><strong>ระยะเวลาเพาะปลูก:</strong> <?= htmlspecialchars($mango['planting_period']) ?></p>
            <!-- ไม่มี harvest_season ในฐานข้อมูล -->
        </div>
        <!-- คอลัมน์ขวา -->
        <div class="col-md-4">
            <h4>การแปรรูป</h4>
            <p><?= nl2br(htmlspecialchars($mango['processing'])) ?></p>
            <h4 class="mt-4">หมวดหมู่มะม่วง</h4>
            <p><strong>ประเภท:</strong> <?= htmlspecialchars($mango['category']) ?></p>
            <!-- ไม่มี fresh_consumption ในฐานข้อมูล -->
        </div>
    </div>

    <!-- ลักษณะสัณฐานวิทยาแบบ 3 คอลัมน์ กว้างครึ่งหน้าจอ -->
    <div class="morphology-section">
        <h2 class="text-center">ลักษณะสัณฐานวิทยา</h2>
        <div class="morphology-columns">
            <div class="morphology-column">
                <h3>ต้น</h3>
                <ul>
                    <li><?= htmlspecialchars($mango['morph_tree']) ?></li>
                </ul>
            </div>
            <div class="morphology-column">
                <h3>ใบ</h3>
                <ul>
                    <li><?= htmlspecialchars($mango['morph_leaf']) ?></li>
                </ul>
            </div>
            <div class="morphology-column">
                <h3>ดอก</h3>
                <ul>
                    <li><?= htmlspecialchars($mango['morph_flower']) ?></li>
                </ul>
            </div>
        </div>
    </div>

    <h4 class="mt-5">รูปภาพ</h4>
    <div class="row text-center">
        <div class="col-6 col-md-3 mb-3">
            <h6>ผล</h6>
            <img src="../admin/uploads/<?= htmlspecialchars(basename($mango['morph_fruit'])) ?>" class="img-fluid mb-3" alt="ผลมะม่วง" style="object-fit: cover; width: 100%; height: 200px;">
        </div>
        <div class="col-6 col-md-3 mb-3">
            <h6>ต้น</h6>
            <img src="../admin/uploads/<?= htmlspecialchars(basename($mango['morph_tree'])) ?>" class="img-fluid mb-3" alt="ต้นมะม่วง" style="object-fit: cover; width: 100%; height: 200px;">
        </div>
        <div class="col-6 col-md-3 mb-3">
            <h6>ใบ</h6>
            <img src="../admin/uploads/<?= htmlspecialchars(basename($mango['morph_leaf'])) ?>" class="img-fluid mb-3" alt="ใบมะม่วง" style="object-fit: cover; width: 100%; height: 200px;">
        </div>
        <div class="col-6 col-md-3 mb-3">
            <h6>กิ่ง</h6>
            <img src="../admin/uploads/<?= htmlspecialchars(basename($mango['morph_branch'])) ?>" class="img-fluid mb-3" alt="กิ่งมะม่วง" style="object-fit: cover; width: 100%; height: 200px;">
        </div>
        <div class="col-6 col-md-3 mb-3">
            <h6>ดอก</h6>
            <img src="../admin/uploads/<?= htmlspecialchars(basename($mango['morph_flower'])) ?>" class="img-fluid mb-3" alt="ดอกมะม่วง" style="object-fit: cover; width: 100%; height: 200px;">
        </div>
    </div>
    <a href="mangoes.php" class="btn btn-secondary mt-4">← กลับหน้ารวม</a>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
<?php
require_once '../admin/db.php';

if (!isset($_GET['name'])) {
    // แสดงข้อความแจ้งเตือน หรือจะ echo อะไรก็ได้
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>กรุณาเลือกสายพันธุ์มะม่วงจากหน้าหลัก</h2>";
    exit;
}

$name = $_GET['name'];

// ดึงข้อมูลมะม่วงจากฐานข้อมูล (ใช้ฟิลด์ตามโครงสร้างใหม่)
$stmt = $conn->prepare("SELECT * FROM mangoes WHERE name_local = ?");
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
            --primary-color: #2e7d32;
            --secondary-color: #4caf50;
            --accent-color: #ff9800;
            --light-bg: #f8f9fa;
            --card-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            --Success: #1cc88a;
            --Danger: #e74a3b;
            --Dark: #5a5c69;
            --Darks: #000000;
            --Light: #fff;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Kanit', sans-serif;
        }

        .hero {
            height: 70vh;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background-blend-mode: darken;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            margin-top: 2rem;
            position: relative;
            overflow: hidden;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(46, 125, 50, 0.25) 0%, rgba(255, 152, 0, 0.10) 100%);
            z-index: 1;
        }

        .hero-contact {
            margin-top: 5rem;
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            color: #fff;
            font-size: 60px;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 8px rgba(46, 125, 50, 0.3);
            letter-spacing: 1px;
        }

        .hero samp h1 {
            font-size: 32px;
            color: var(--accent-color);
            margin-top: 1rem;
        }

        .hero samp h1 strong {
            color: var(--secondary-color);
        }

        .hero samp {
            color: #fff;
            font-size: 20px;
            font-family: "Kanit", sans-serif;
        }

        .container h4 {
            font-weight: 600;
            color: var(--primary-color);
            margin-top: 1.5rem;
        }

        p strong {
            font-weight: 500;
            color: var(--secondary-color);
        }

        .btn-secondary {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border: none;
            border-radius: 24px;
            font-weight: bold;
            padding: 0.6rem 2rem;
            box-shadow: var(--card-shadow);
            transition: background 0.3s, color 0.3s;
        }

        .btn-secondary:hover {
            background: var(--accent-color);
            color: #fff;
        }

        .img-fluid.mb-3 {
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(46, 125, 50, 0.10);
            border: 2px solid var(--secondary-color);
            background: #fff;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .img-fluid.mb-3:hover {
            transform: scale(1.03) rotate(-1deg);
            box-shadow: 0 8px 32px rgba(255, 152, 0, 0.13);
            border-color: var(--accent-color);
        }

        .row.text-center .col-6,
        .row.text-center .col-md-3 {
            margin-bottom: 1.5rem;
        }

        .row.text-center h6 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
        }

        .text-muted.py-5 {
            background: #fffbe7;
            border-radius: 12px;
            color: #bdb76b !important;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }

            .hero samp h1 {
                font-size: 20px;
            }

            .container h4 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <div id="mango-bounce"></div>
    <br>
    <div class="container py-5 mt-5">
        <?php
        $header_img = isset($mango['header_img']) ? $mango['header_img'] : '';
        $header_img_path = "../uploads/" . htmlspecialchars(basename($header_img));
        $header_img_abs = __DIR__ . "/../uploads/" . basename($header_img);
        $hero_bg = ($header_img && file_exists($header_img_abs)) ? "background-image: url('{$header_img_path}');" : "background-color: #e0e0e0;";
        ?>
        <div class="hero text-center"
            style="<?= $hero_bg ?> background-size: cover; background-position: center; background-blend-mode: darken; display: flex; align-items: center; justify-content: center; flex-direction: column; border-radius: 24px; box-shadow: var(--card-shadow); margin-top: 2rem; height: 70vh;">
            <div class="hero-contact">
                <h1 class="mb-4"><?= htmlspecialchars($mango['name_local']) ?></h1>
                <br>
                <samp>
                    <h1><strong></strong> <?= htmlspecialchars($mango['name_sci'] ?? '-') ?></้>
                </samp>
            </div>
        </div>
        <div class="row">
            <!-- คอลัมน์ซ้าย -->
            <div class="col-md-6">
                <br>
                <img src="../uploads/<?= htmlspecialchars(basename($mango['morph_fruit'])) ?>" class="img-fluid mb-3" alt="ผลมะม่วง <?= htmlspecialchars($mango['name_sci']) ?>" style="max-height: 800px;">
            </div>
            <!-- คอลัมน์กลาง -->
            <div class="col-md-6">
                <br>
                <h4><span style="font-size:1.2em;">🥭</span> ข้อมูลทั่วไป</h4>
                <p><strong>ชื่อวิทยาศาสตร์:</strong> <?= htmlspecialchars($mango['name_sci'] ?? '-') ?></p>
                <p><strong>ชื่อท้องถิ่น:</strong> <?= !empty($mango['name_local']) ? htmlspecialchars($mango['name_local']) : '-' ?></p>
                <h4 class="mt-4">หมวดหมู่มะม่วง</h4>
                <p><strong>ประเภท:</strong> <?= isset($mango['category']) ? htmlspecialchars($mango['category']) : '-' ?></p>
                <p><strong>การขยายพันธุ์:</strong> <?= isset($mango['propagation']) ? htmlspecialchars($mango['propagation']) : '-' ?></p>
                <p><strong>ลักษณะดิน:</strong> <?= isset($mango['soil']) ? htmlspecialchars($mango['soil']) : '-' ?></p>
                <p><strong>ระยะเวลาเพาะปลูก:</strong> <?= isset($mango['planting_period']) ? htmlspecialchars($mango['planting_period']) : '-' ?></p>
                <h4>การแปรรูป</h4>
                <p><?= isset($mango['processing']) ? nl2br(htmlspecialchars($mango['processing'])) : '-' ?></p>

            </div>
            <!-- คอลัมน์ขวา -->
        </div>

        <?php
        function showImage($filename, $alt)
        {
            $path = "../uploads/" . htmlspecialchars(basename($filename));
            $abs = __DIR__ . "/../uploads/" . basename($filename);
            if ($filename && file_exists($abs)) {
                echo '<img src="' . $path . '" class="img-fluid mb-3" alt="' . htmlspecialchars($alt) . '" style="object-fit: cover; width: 100%; height: 200px;">';
            } else {
                echo '<div class="text-muted py-5">ไม่มีรูปภาพ</div>';
            }
        }
        ?>
        <div class="row text-center">
            <div class="col-6 col-md-3 mb-3">

                <?php showImage($mango['morph_tree'] ?? '', 'ต้นมะม่วง'); ?>
            </div>
            <div class="col-6 col-md-3 mb-3">

                <?php showImage($mango['morph_leaf'] ?? '', 'ใบมะม่วง'); ?>
            </div>
            <div class="col-6 col-md-3 mb-3">

                <?php showImage($mango['morph_branch'] ?? '', 'กิ่งมะม่วง'); ?>
            </div>
            <div class="col-6 col-md-3 mb-3">

                <?php showImage($mango['morph_flower'] ?? '', 'ดอกมะม่วง'); ?>
            </div>
        </div>
        <a href="index.php" class="btn btn-secondary mt-4">
            ← กลับหน้ารวม
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/p5@1.9.2/lib/p5.min.js"></script>
    <script>
let x, y, dx = 4, mangoW = 120;

function setup() {
  let cnv = createCanvas(windowWidth, 180);
  cnv.parent('mango-bounce');
  cnv.style('display', 'block');
  cnv.style('margin', '0 auto');
  cnv.position(0, 10);
  x = mangoW / 2 + 10;
  y = 100;
  noStroke();
}

function windowResized() {
  resizeCanvas(windowWidth, 180);
  if (x > width - mangoW/2 - 10) x = width - mangoW/2 - 10;
}

function draw() {
  clear();
  background(0,0,0,0);

  // กระดอนซ้าย-ขวาเต็มจอ
  if (x < mangoW/2 + 10 || x > width - mangoW/2 - 10) dx *= -1;
  x += dx;

  drawCartoonMango(x, y);
}

function drawCartoonMango(x, y) {
  // ร่างมะม่วง
  stroke(0);
  strokeWeight(4);
  fill(255, 204, 0); // สีเหลือง
  beginShape();
  vertex(x, y - 80);
  bezierVertex(x + 60, y - 100, x + 50, y + 100, x, y + 120);
  bezierVertex(x - 50, y + 100, x - 60, y - 100, x, y - 80);
  endShape(CLOSE);

  // ใบไม้
  fill(34, 177, 76); // เขียวสด
  beginShape();
  vertex(x + 10, y - 100);
  bezierVertex(x + 60, y - 120, x + 20, y - 60, x + 40, y - 60);
  bezierVertex(x + 10, y - 70, x + 5, y - 90, x + 10, y - 100);
  endShape(CLOSE);

  // ก้าน
  stroke(0);
  strokeWeight(6);
  line(x + 10, y - 80, x + 10, y - 110);

  // ตา
  fill(0);
  noStroke();
  ellipse(x - 20, y + 10, 15, 15);
  ellipse(x + 20, y + 10, 15, 15);

  // แก้มชมพู
  fill(255, 105, 180, 180); // ชมพูใส
  ellipse(x - 20, y + 22, 15, 8);
  ellipse(x + 20, y + 22, 15, 8);

  // ปาก
  stroke(0);
  strokeWeight(2);
  noFill();
  beginShape();
  vertex(x - 5, y + 20);
  quadraticVertex(x, y + 25, x + 5, y + 20);
  endShape();
}
</script>
</body>

</html>
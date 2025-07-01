<?php
// filepath: c:\xampp\htdocs\Data_mango\admin\edit_mango.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';

// ตรวจสอบ id
if (!isset($_GET['id'])) {
    header("Location: mango_list.php");
    exit;
}
$id = intval($_GET['id']);

// ดึงข้อมูลเดิม
$stmt = $conn->prepare("SELECT * FROM mangoes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$mango = $result->fetch_assoc();
if (!$mango) {
    echo "ไม่พบข้อมูล";
    exit;
}

// ฟังก์ชัน sanitize ชื่อไฟล์
function safe_filename($filename) {
    $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename);
    return substr($filename, 0, 200);
}

// เมื่อ submit ฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_sci = htmlspecialchars(trim($_POST['name_sci'] ?? ''), ENT_QUOTES, 'UTF-8');
    $name_local = htmlspecialchars(trim($_POST['name_local'] ?? ''), ENT_QUOTES, 'UTF-8');
    $soil = htmlspecialchars(trim($_POST['soil'] ?? ''), ENT_QUOTES, 'UTF-8');
    $planting_period = htmlspecialchars(trim($_POST['planting_period'] ?? ''), ENT_QUOTES, 'UTF-8');

    // หมวดหมู่ (radio)
    $category = isset($_POST['category']) ? htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8') : null;

    // การขยายพันธุ์ (checkbox)
    if (isset($_POST['propagation'])) {
        if (is_array($_POST['propagation'])) {
            $propagation = implode(',', array_map(function($v) {
                return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
            }, $_POST['propagation']));
        } else {
            $propagation = htmlspecialchars($_POST['propagation'], ENT_QUOTES, 'UTF-8');
        }
    } else {
        $propagation = null;
    }

    // การแปรรูป (checkbox)
    if (isset($_POST['processing'])) {
        if (is_array($_POST['processing'])) {
            $processing = implode(',', array_map(function($v) {
                return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
            }, $_POST['processing']));
        } else {
            $processing = htmlspecialchars($_POST['processing'], ENT_QUOTES, 'UTF-8');
        }
    } else {
        $processing = null;
    }

    // อัปโหลดรูป (ถ้ามี)
    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $fields_img = [
        'header_img', 'morph_fruit', 'morph_tree', 'morph_leaf', 'morph_flower', 'morph_branch'
    ];
    $img_data = [];
    foreach ($fields_img as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($ext, $allowed)) {
                $new_filename = $field . '_' . uniqid() . '.' . $ext;
                $safe_path = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES[$field]['tmp_name'], $safe_path)) {
                    $img_data[$field] = 'uploads/' . $new_filename;
                } else {
                    $img_data[$field] = $mango[$field];
                }
            } else {
                $img_data[$field] = $mango[$field];
            }
        } else {
            $img_data[$field] = $mango[$field];
        }
    }

    // อัปเดตข้อมูล
    $stmt = $conn->prepare("UPDATE mangoes SET 
        name_sci=?, name_local=?, soil=?, planting_period=?, category=?, propagation=?, processing=?,
        header_img=?, morph_fruit=?, morph_tree=?, morph_leaf=?, morph_flower=?, morph_branch=?
        WHERE id=?");
    $stmt->bind_param(
        "sssssssssssssi", // 13 s + 1 i
        $name_sci,
        $name_local,
        $soil,
        $planting_period,
        $category,
        $propagation,
        $processing,
        $img_data['header_img'],
        $img_data['morph_fruit'],
        $img_data['morph_tree'],
        $img_data['morph_leaf'],
        $img_data['morph_flower'],
        $img_data['morph_branch'],
        $id
    );
    $stmt->execute();

    header("Location: view_mango.php?id=$id");
    exit;
}

// ตัวเลือกหมวดหมู่ (radio)
$category_options = ['เชิงพาณิชย์', 'เชิงอนุรักษ์', 'ครัวเรือน'];

// ตัวเลือกการขยายพันธุ์ (checkbox)
$propagation_options = ['การเพาะจากเมล็ด', 'ทาบกิ่ง', 'เสียบยอด'];

// ตัวเลือกการแปรรูป (checkbox)
$processing_options = ['การทำแห้ง', 'การแปรรูป', 'การบรรจุหีบห่อ'];

// แปลงค่าที่เก็บในฐานข้อมูลเป็น array
$propagation_arr = !empty($mango['propagation']) ? explode(',', $mango['propagation']) : [];
$processing_arr = !empty($mango['processing']) ? explode(',', $mango['processing']) : [];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลมะม่วง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4">แก้ไขข้อมูลมะม่วง</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>ชื่อวิทยาศาสตร์</label>
            <input type="text" name="name_sci" class="form-control" value="<?= htmlspecialchars($mango['name_sci']) ?>" required>
        </div>
        <div class="mb-3">
            <label>ชื่อท้องถิ่น</label>
            <input type="text" name="name_local" class="form-control" value="<?= htmlspecialchars($mango['name_local']) ?>" required>
        </div>
        <div class="mb-3">
            <label>ลักษณะของดิน</label>
            <textarea name="soil" class="form-control"><?= htmlspecialchars($mango['soil']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>ฤดูปลูก</label>
            <textarea name="planting_period" class="form-control"><?= htmlspecialchars($mango['planting_period']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>หมวดหมู่</label><br>
            <?php foreach ($category_options as $cat): ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="category" id="cat_<?= $cat ?>" value="<?= $cat ?>"
                        <?= ($mango['category'] === $cat) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="cat_<?= $cat ?>"><?= $cat ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mb-3">
            <label>การขยายพันธุ์</label><br>
            <?php foreach ($propagation_options as $opt): ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="propagation[]" id="prop_<?= $opt ?>" value="<?= $opt ?>"
                        <?= in_array($opt, $propagation_arr) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="prop_<?= $opt ?>"><?= $opt ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mb-3">
            <label>การแปรรูป</label><br>
            <?php foreach ($processing_options as $opt): ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="processing[]" id="proc_<?= $opt ?>" value="<?= $opt ?>"
                        <?= in_array($opt, $processing_arr) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="proc_<?= $opt ?>"><?= $opt ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mb-3">
            <label>รูปภาพหลัก (header)</label><br>
            <?php if ($mango['header_img']): ?>
                <img src="/Data_mango/<?= htmlspecialchars($mango['header_img']) ?>" width="120"><br>
            <?php endif; ?>
            <input type="file" name="header_img" class="form-control mt-2">
        </div>
        <div class="mb-3">
            <label>รูปผลมะม่วง</label><br>
            <?php if ($mango['morph_fruit']): ?>
                <img src="/Data_mango/<?= htmlspecialchars($mango['morph_fruit']) ?>" width="120"><br>
            <?php endif; ?>
            <input type="file" name="morph_fruit" class="form-control mt-2">
        </div>
        <div class="mb-3">
            <label>รูปต้น</label><br>
            <?php if ($mango['morph_tree']): ?>
                <img src="/Data_mango/<?= htmlspecialchars($mango['morph_tree']) ?>" width="120"><br>
            <?php endif; ?>
            <input type="file" name="morph_tree" class="form-control mt-2">
        </div>
        <div class="mb-3">
            <label>รูปใบ</label><br>
            <?php if ($mango['morph_leaf']): ?>
                <img src="/Data_mango/<?= htmlspecialchars($mango['morph_leaf']) ?>" width="120"><br>
            <?php endif; ?>
            <input type="file" name="morph_leaf" class="form-control mt-2">
        </div>
        <div class="mb-3">
            <label>รูปดอก</label><br>
            <?php if ($mango['morph_flower']): ?>
                <img src="/Data_mango/<?= htmlspecialchars($mango['morph_flower']) ?>" width="120"><br>
            <?php endif; ?>
            <input type="file" name="morph_flower" class="form-control mt-2">
        </div>
        <div class="mb-3">
            <label>รูปกิ่ง</label><br>
            <?php if ($mango['morph_branch']): ?>
                <img src="/Data_mango/<?= htmlspecialchars($mango['morph_branch']) ?>" width="120"><br>
            <?php endif; ?>
            <input type="file" name="morph_branch" class="form-control mt-2">
        </div>
        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
        <a href="mango_list.php" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
</body>
</html>